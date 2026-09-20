<?php
    ############################################################################
    #
    #   One-off backfill: import WeeWX archive export (CSV) into alldata
    #
    #   Background: alldata had no new rows 2025-04-16 -> 2026-09-19 due to the
    #   api.php cache bug (see tech/projects/meteo/02-buggar-diagnos.md in the
    #   vault). WeeWX's own archive covers this period (and much more), so we
    #   backfill from a CSV exported on the WeeWX box.
    #
    #   Usage:
    #     https://weather.sollebrunn.net/meteo/admin/importWeeWXBackfill.php?password=...
    #       -> dry run against alldata_import_test (default, safe)
    #     https://weather.sollebrunn.net/meteo/admin/importWeeWXBackfill.php?password=...&mode=production
    #       -> writes for real into alldata (only run after checking the test table!)
    #
    #   Expects meteo/admin/importData/weewx_backfill.csv to already be uploaded
    #   (see tech/projects/meteo/06-projektspecifikation-databasmerge.md for the
    #   sqlite3 export command that produces it - columns must be exactly:
    #   DateTime,T,Tmax,Tmin,H,P,W,G,B,R,RR,S,D,A - with a header row).
    #
    ############################################################################

    $base = "../";
    require($base."config.php");

    if(!isset($_GET['password']) || $_GET['password'] !== $updatePassword){
        die("Unauthorized");
    }

    $mode = isset($_GET['mode']) && $_GET['mode'] === 'production' ? 'production' : 'test';
    $targetTable = $mode === 'production' ? 'alldata' : 'alldata_import_test';

    $csvPath = __DIR__."/importData/weewx_backfill.csv";
    if(!file_exists($csvPath)){
        die("CSV not found at $csvPath - upload it first (see 06-projektspecifikation-databasmerge.md).");
    }

    header("Content-Type: text/plain; charset=utf-8");
    echo "Mode: $mode -> table: $targetTable\n";

    if($mode === 'test'){
        $result = mysqli_query($con, "CREATE TABLE IF NOT EXISTS alldata_import_test LIKE alldata");
        if(!$result){
            die("Could not create test table: ".mysqli_error($con));
        }
        echo "Ensured $targetTable exists (schema copy of alldata).\n";
    }

    $handle = fopen($csvPath, "r");
    if($handle === false){
        die("Could not open CSV file.");
    }

    $header = fgetcsv($handle);
    $expected = array("DateTime","T","Tmax","Tmin","H","P","W","G","B","R","RR","S","D","A");
    if($header !== $expected){
        die("CSV header mismatch. Expected: ".implode(",", $expected)."\nGot: ".implode(",", $header));
    }

    $columns = implode(",", $expected);
    $batchSize = 500;
    $batch = array();
    $totalRows = 0;
    $totalInserted = 0;
    $totalSkipped = 0;

    function flushBatch($con, $targetTable, $columns, &$batch, &$totalInserted, &$totalSkipped){
        if(empty($batch)){
            return;
        }
        $sql = "INSERT IGNORE INTO $targetTable ($columns) VALUES " . implode(",", $batch);
        $before = mysqli_affected_rows($con);
        $result = mysqli_query($con, $sql);
        if(!$result){
            echo "ERROR on batch: ".mysqli_error($con)."\n";
        }
        else{
            $affected = mysqli_affected_rows($con);
            $totalInserted += $affected;
            $totalSkipped += (count($batch) - $affected);
        }
        $batch = array();
    }

    while(($row = fgetcsv($handle)) !== false){
        if(count($row) !== count($expected)){
            echo "Skipping malformed row: ".implode(",", $row)."\n";
            continue;
        }
        $totalRows++;
        $escaped = array_map(function($v) use ($con){
            if($v === '' || $v === null){
                return "NULL";
            }
            return "'".mysqli_real_escape_string($con, $v)."'";
        }, $row);
        $batch[] = "(".implode(",", $escaped).")";

        if(count($batch) >= $batchSize){
            flushBatch($con, $targetTable, $columns, $batch, $totalInserted, $totalSkipped);
        }
    }
    flushBatch($con, $targetTable, $columns, $batch, $totalInserted, $totalSkipped);

    fclose($handle);

    echo "\nDone.\n";
    echo "Rows read from CSV: $totalRows\n";
    echo "Rows inserted:      $totalInserted\n";
    echo "Rows skipped (duplicate DateTime, via INSERT IGNORE): $totalSkipped\n";

    if($mode === 'test'){
        $checkResult = mysqli_query($con, "SELECT COUNT(*) AS c, MIN(DateTime) AS first_row, MAX(DateTime) AS last_row FROM alldata_import_test");
        if($checkResult){
            $checkRow = mysqli_fetch_assoc($checkResult);
            echo "\nalldata_import_test now has {$checkRow['c']} rows, spanning {$checkRow['first_row']} to {$checkRow['last_row']}.\n";
            echo "Inspect it via phpMyAdmin before re-running with &mode=production.\n";
            echo "When satisfied: DROP TABLE alldata_import_test; then re-run with &mode=production.\n";
        }
    }
?>

<?php

	############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Database bulk edit
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
    include("../config.php");

    echo "<style>body{color:white!important}</style>";
    
    $type = $_GET['type'];

    if($type=="rainCheck"){
        echo "Checking rain values...<br>";
        $correctedValues = 0;
        $query = "SELECT DateTime, R FROM alldata WHERE HOUR(DateTime)=0 AND MINUTE(DateTime)<=10 ORDER BY DateTime";
        $result = mysqli_query($con, $query);
        while($row = mysqli_fetch_array($result)){
            $day = date("Y-m-d", strtotime($row['DateTime']));
            $minute = date("i", strtotime($row['DateTime']));
            $U = strtotime($row['DateTime']);
            $data[$day][] = array($U, $row['R']);
        }
        if(count($data > 0)){
            foreach($data as $thisDay => $thisValue){
                $thisDay = explode("-", $thisDay);
                $thisY = $thisDay[0];
                $thisM = $thisDay[1];
                $thisD = $thisDay[2]; 
                $thisTimeMax = $thisValue[count($thisValue) - 1]; // select latest
                $thisTimeMax = $thisTimeMax[1];
                for($i=0; $i < count($thisValue) - 1; $i++){
                    if($thisValue[$i][1] > $thisTimeMax){
                        $correctedValues++;
                        $thisMin = date("i", $thisValue[$i][0]);
                        $thisSec = date("s", $thisValue[$i][0]);
                        $sqlUpdate = "UPDATE alldata SET R=$thisTimeMax WHERE YEAR(DateTime)=$thisY AND MONTH(DateTime)=$thisM AND DAY(DateTime)=$thisD AND HOUR(DateTime)=0 AND MINUTE(DateTime)=$thisMin AND SECOND(DateTime)=$thisSec";
                        mysqli_query($con, $sqlUpdate);
                        echo "Correcting data on " . date("Y-m-d H:i:s", $thisValue[$i][0]). "...<br>";
                    }
                }
            }
        }
        echo "<br><br><strong>Corrected values: " . $correctedValues . "</strong><br>";
        die("Done.");
    }

    if(isset($_GET['from'])){
		$fromRaw = explode("_",$_GET['from']);
		$fromY = $fromRaw[0];
		if(!is_numeric($fromY) || $fromY<1900 || $fromY>date("Y")){
			$fromY = date("Y");
		}
		$fromM = $fromRaw[1];
		if(!is_numeric($fromM) || $fromM<1 || $fromM>12){
			$fromM = date("m");
		}
		$fromD = $fromRaw[2];
		if(!is_numeric($fromD) || $fromD<1 || $fromD>31){
			$fromD = date("d");
		}
		$fromH = $fromRaw[3];
		if(!is_numeric($fromH) || $fromH<0 || $fromH>24){
			$fromH = date("H");
		}
		$fromMin = $fromRaw[4];
		if(!is_numeric($fromMin) || $fromMin<0 || $fromMin>59){
			$fromMin = date("i");
		}
		$from = $fromY."-".$fromM."-".$fromD." ".$fromH.":".$fromMin;
	}
	else{
		die("Incorrect input.");
	}
	
	if(isset($_GET['to'])){
		$toRaw = explode("_",$_GET['to']);
		$toY = $toRaw[0];
		if(!is_numeric($toY) || $toY<1900 || $toY>date("Y")){
			$toY = date("Y");
		}
		$toM = $toRaw[1];
		if(!is_numeric($toM) || $toM<1 || $toM>12){
			$toM = date("m");
		}
		$toD = $toRaw[2];
		if(!is_numeric($toD) || $toD<1 || $toD>31){
			$toD = date("d");
		}
		$toH = $toRaw[3];
		if(!is_numeric($toH) || $toH<0 || $toH>24){
			$toH = date("H");
		}
		$toMin = $toRaw[4];
		if(!is_numeric($toMin) || $toMin<0 || $toMin>59){
			$toMin = date("i");
		}
		$to = $toY."-".$toM."-".$toD." ".$toH.":".$toMin;
	}
	else{
		die("Incorrect input.");
    }
    
   

    // first make sure NULL possible (old template users)
    $sql = "ALTER TABLE  `alldata` CHANGE  `T`  `T` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `Tmax`  `Tmax` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `Tmin`  `Tmin` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `H`  `H` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `P`  `P` DECIMAL( 6, 2 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `W`  `W` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `G`  `G` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `R`  `R` DECIMAL( 6, 2 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `RR`  `RR` DECIMAL( 6, 2 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `B`  `B` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `A`  `A` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `D`  `D` DECIMAL( 4, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);
    $sql = "ALTER TABLE  `alldata` CHANGE  `S`  `S` DECIMAL( 6, 1 ) NULL DEFAULT NULL";
    mysqli_query($con, $sql);

    if($type=="all"){
        $sql = "DELETE FROM alldata WHERE DateTime>='$from' && DateTime<='$to'";
        mysqli_query($con, $sql);
        echo "Deleted all records between $from and $to";
    }

    if($type=="sensor"){
        if($_GET['T']==1){
            $sql = "UPDATE alldata SET T=null, Tmax=null, Tmin=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting temperature records between $from and $to.<br><br>";
        }
        if($_GET['T']==2 && is_numeric($_GET['Tval'])){
            $val = $_GET['Tval'];
            $sql = "UPDATE alldata SET T=null WHERE DateTime>='$from' && DateTime<='$to' && T>$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET Tmax=null WHERE DateTime>='$from' && DateTime<='$to' && Tmax>$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET Tmin=null WHERE DateTime>='$from' && DateTime<='$to' && Tmin>$val";
            mysqli_query($con, $sql);
            echo "Deleting temperature records between $from and $to.<br><br>";
        }
        if($_GET['T']==3 && is_numeric($_GET['Tval'])){
            $val = $_GET['Tval'];
            $sql = "UPDATE alldata SET T=null WHERE DateTime>='$from' && DateTime<='$to' && T<$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET Tmax=null WHERE DateTime>='$from' && DateTime<='$to' && Tmax<$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET Tmin=null WHERE DateTime>='$from' && DateTime<='$to' && Tmin<$val";
            mysqli_query($con, $sql);
            echo "Deleting temperature records between $from and $to.<br><br>";
        }
        if($_GET['H']==1){
            $sql = "UPDATE alldata SET H=NULL WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting humidity records between $from and $to.<br><br>";
        }
        if($_GET['H']==2 && is_numeric($_GET['Hval'])){
            $val = $_GET['Hval'];
            $sql = "UPDATE alldata SET H=null WHERE DateTime>='$from' && DateTime<='$to' && H>$val";
            mysqli_query($con, $sql);
            echo "Deleting humidity records between $from and $to.<br><br>";
        }
        if($_GET['H']==3 && is_numeric($_GET['Hval'])){
            $val = $_GET['Hval'];
            $sql = "UPDATE alldata SET H=null WHERE DateTime>='$from' && DateTime<='$to' && H<$val";
            mysqli_query($con, $sql);
            echo "Deleting humidity records between $from and $to.<br><br>";
        }
        if($_GET['P']==1){
            $sql = "UPDATE alldata SET P=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting pressure records between $from and $to.<br><br>";
        }
        if($_GET['P']==2 && is_numeric($_GET['Pval'])){
            $val = $_GET['Pval'];
            $sql = "UPDATE alldata SET P=null WHERE DateTime>='$from' && DateTime<='$to' && P>$val";
            mysqli_query($con, $sql);
            echo "Deleting pressure records between $from and $to.<br><br>";
        }
        if($_GET['P']==3 && is_numeric($_GET['Pval'])){
            $val = $_GET['Pval'];
            $sql = "UPDATE alldata SET P=null WHERE DateTime>='$from' && DateTime<='$to' && P<$val";
            mysqli_query($con, $sql);
            echo "Deleting pressure records between $from and $to.<br><br>";
        }
        if($_GET['W']==1){
            $sql = "UPDATE alldata SET W=null, G=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting wind and gust records between $from and $to.<br><br>";
        }
        if($_GET['W']==2 && is_numeric($_GET['Wval'])){
            $val = $_GET['Wval'];
            $sql = "UPDATE alldata SET W=null WHERE DateTime>='$from' && DateTime<='$to' && W>$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET G=null WHERE DateTime>='$from' && DateTime<='$to' && G>$val";
            mysqli_query($con, $sql);
            echo "Deleting wind and gust records between $from and $to.<br><br>";
        }
        if($_GET['W']==3 && is_numeric($_GET['Wval'])){
            $val = $_GET['Wval'];
            $sql = "UPDATE alldata SET W=null WHERE DateTime>='$from' && DateTime<='$to' && W<$val";
            mysqli_query($con, $sql);
            $sql = "UPDATE alldata SET G=null WHERE DateTime>='$from' && DateTime<='$to' && G<$val";
            mysqli_query($con, $sql);
            echo "Deleting wind and gust records between $from and $to.<br><br>";
        }
        if($_GET['B']==1){
            $sql = "UPDATE alldata SET B=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting wind direction records between $from and $to.<br><br>";
        }
        if($_GET['B']==2 && is_numeric($_GET['Bval'])){
            $val = $_GET['Bval'];
            $sql = "UPDATE alldata SET B=null WHERE DateTime>='$from' && DateTime<='$to' && B>$val";
            mysqli_query($con, $sql);
            echo "Deleting wind direction records between $from and $to.<br><br>";
        }
        if($_GET['B']==3 && is_numeric($_GET['Bval'])){
            $val = $_GET['Bval'];
            $sql = "UPDATE alldata SET B=null WHERE DateTime>='$from' && DateTime<='$to' && B<$val";
            mysqli_query($con, $sql);
            echo "Deleting wind direction records between $from and $to.<br><br>";
        }
        if($_GET['R']==1){
            $sql = "UPDATE alldata SET R=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting precipitation records between $from and $to.<br><br>";
        }
        if($_GET['R']==2 && is_numeric($_GET['Rval'])){
            $val = $_GET['Rval'];
            $sql = "UPDATE alldata SET R=null WHERE DateTime>='$from' && DateTime<='$to' && R>$val";
            mysqli_query($con, $sql);
            echo "Deleting precipitation records between $from and $to.<br><br>";
        }
        if($_GET['R']==3 && is_numeric($_GET['Rval'])){
            $val = $_GET['Rval'];
            $sql = "UPDATE alldata SET R=null WHERE DateTime>='$from' && DateTime<='$to' && R<$val";
            mysqli_query($con, $sql);
            echo "Deleting precipitation records between $from and $to.<br><br>";
        }
        if($_GET['RR']==1){
            $sql = "UPDATE alldata SET RR=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting rain rate records between $from and $to.<br><br>";
        }
        if($_GET['RR']==2 && is_numeric($_GET['RRval'])){
            $val = $_GET['RRval'];
            $sql = "UPDATE alldata SET RR=null WHERE DateTime>='$from' && DateTime<='$to' && RR>$val";
            mysqli_query($con, $sql);
            echo "Deleting rain rate records between $from and $to.<br><br>";
        }
        if($_GET['RR']==3 && is_numeric($_GET['RRval'])){
            $val = $_GET['RRval'];
            $sql = "UPDATE alldata SET RR=null WHERE DateTime>='$from' && DateTime<='$to' && RR<$val";
            mysqli_query($con, $sql);
            echo "Deleting rain rate records between $from and $to.<br><br>";
        }
        if($_GET['S']==1){
            $sql = "UPDATE alldata SET S=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting solar radiation records between $from and $to.<br><br>";
        }
        if($_GET['S']==2 && is_numeric($_GET['Sval'])){
            $val = $_GET['Sval'];
            $sql = "UPDATE alldata SET S=null WHERE DateTime>='$from' && DateTime<='$to' && S>$val";
            mysqli_query($con, $sql);
            echo "Deleting solar radiation records between $from and $to.<br><br>";
        }
        if($_GET['S']==3 && is_numeric($_GET['Sval'])){
            $val = $_GET['Sval'];
            $sql = "UPDATE alldata SET S=null WHERE DateTime>='$from' && DateTime<='$to' && S<$val";
            mysqli_query($con, $sql);
            echo "Deleting solar radiation records between $from and $to.<br><br>";
        }
        if($_GET['A']==1){
            $sql = "UPDATE alldata SET A=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting apparent temperature records between $from and $to.<br><br>";
        }
        if($_GET['A']==2 && is_numeric($_GET['Aval'])){
            $val = $_GET['Aval'];
            $sql = "UPDATE alldata SET A=null WHERE DateTime>='$from' && DateTime<='$to' && A>$val";
            mysqli_query($con, $sql);
            echo "Deleting apparent temperature records between $from and $to.<br><br>";
        }
        if($_GET['A']==3 && is_numeric($_GET['Aval'])){
            $val = $_GET['Aval'];
            $sql = "UPDATE alldata SET A=null WHERE DateTime>='$from' && DateTime<='$to' && A<$val";
            mysqli_query($con, $sql);
            echo "Deleting apparent temperature records between $from and $to.<br><br>";
        }
        if($_GET['D']==1){
            $sql = "UPDATE alldata SET D=null WHERE DateTime>='$from' && DateTime<='$to'";
            mysqli_query($con, $sql);
            echo "Deleting dewpoint records between $from and $to.<br><br>";
        }
        if($_GET['D']==2 && is_numeric($_GET['Dval'])){
            $val = $_GET['Dval'];
            $sql = "UPDATE alldata SET D=null WHERE DateTime>='$from' && DateTime<='$to' && D>$val";
            mysqli_query($con, $sql);
            echo "Deleting dewpoint records between $from and $to.<br><br>";
        }
        if($_GET['D']==3 && is_numeric($_GET['Dval'])){
            $val = $_GET['Dval'];
            $sql = "UPDATE alldata SET D=null WHERE DateTime>='$from' && DateTime<='$to' && D<$val";
            mysqli_query($con, $sql);
            echo "Deleting dewpoint records between $from and $to.<br><br>";
        }
    }
		

?>
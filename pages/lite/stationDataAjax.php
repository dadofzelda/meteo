<?php

	include("../../config.php");
	include("../../scripts/functions.php");

	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	$period = $_GET['period'];

	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}

	$stationDataShowBftW = false;
    $stationDataShowBftG = false;
    $showNow = false;

	if($period=="today" || $period==""){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P), avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S), max(R), min(W), min(G), min(S), max(RR)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$totalR = convertR($row['max(R)']);
			$R2 = convertR($row['max(RR)'])." ".$displayRainUnits."/".lang('hAbbr','l');
			$R2Label = lang("maximumAbbr",'c');
			$R3 = "";
			$R3Label = "";

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang('today','c');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DATE(DateTime) = CURDATE()
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DATE(DateTime) = CURDATE()
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
	}
	if($period=="yesterday"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P),avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S),max(R),min(W), min(G), min(S), max(RR)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$totalR = convertR($row['max(R)']);
			$R2 = convertR($row['max(RR)'])." ".$displayRainUnits."/".lang('hAbbr','l');
			$R2Label = lang("maximumAbbr",'c');
			$R3 = "";
			$R3Label = "";

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang('yesterday','c');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}

	}
	if($period=="24h"){
		$result = mysqli_query($con, "
			SELECT  max(Tmax), min(Tmin), avg(T), stddev(T), avg(H), max(H), min(H), stddev(H), avg(P), max(P), min(P), stddev(P), avg(A), max(A), min(A), stddev(A), avg(D), max(D), min(D), stddev(D), avg(W), max(W), min(W), stddev(W), avg(G), max(G), min(G), stddev(G), avg(S), max(S), min(S), stddev(S), max(RR)
			FROM  alldata
			WHERE DateTime >= now() - interval 24 hour
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$R2 = convertR($row['max(RR)'])." ".$displayRainUnits."/".lang('hAbbr','l');
			$R2Label = lang("maximumAbbr",'c');
			$R3 = "";
			$R3Label = "";

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = "24 ".lang("hAbbr",'');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DateTime >= now() - interval 24 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DateTime >= now() - interval 24 hour
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}

	}
	if($period=="1h"){
		$result = mysqli_query($con, "
			SELECT  max(Tmax), min(Tmin), avg(T), stddev(T), avg(H), max(H), min(H), stddev(H), avg(P), max(P), min(P), stddev(P), avg(A), max(A), min(A), stddev(A), avg(D), max(D), min(D), stddev(D), avg(W), max(W), min(W), stddev(W), avg(G), max(G), min(G), stddev(G), avg(S), max(S), min(S), stddev(S), max(RR)
			FROM  alldata
			WHERE DateTime >= now() - interval 1 hour
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$R2 = convertR($row['max(RR)'])." ".$displayRainUnits."/".lang('hAbbr','l');
			$R2Label = lang("maximumAbbr",'c');
			$R3 = "";
			$R3Label = "";

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = "1 ".lang("hAbbr",'');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DateTime >= now() - interval 1 hour
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DateTime >= now() - interval 1 hour
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}

	}
	if($period=="thisMonth"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P), avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S), min(W), min(G), min(S)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang("this",'c')." ".lang("month",'l');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date($dateFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date($dateFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date($dateFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date($dateFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date($dateFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date($dateFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date($dateFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date($dateFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date($dateFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date($dateFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date($dateFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date($dateFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date($dateFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}

	}
	if($period=="thisYear"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P), avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S),min(W), min(G), min(S)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$totalR = convertR($row['max(R)']);

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang("this",'c')." ".lang("year",'l');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date($dateFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date($dateFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date($dateFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date($dateFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date($dateFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date($dateFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date($dateFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date($dateFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date($dateFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date($dateFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date($dateFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND YEAR(DateTime) = YEAR(CURDATE())
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date($dateFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND YEAR(DateTime) = YEAR(CURDATE())
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date($dateFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
	}
	if($period=="alltime"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P), avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S),min(W), min(G), min(S)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$totalR = convertR($row['max(R)']);

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang('all time','c');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date($dateFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date($dateFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date($dateFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date($dateFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date($dateFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date($dateFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date($dateFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date($dateFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date($dateFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date($dateFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date($dateFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date($dateFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date($dateFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
	}
	if($period=="normal"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P), avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W),avg(G), max(G), avg(S), max(S),min(W), min(G), min(S)
				FROM  alldata
				WHERE MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date("Y",$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date("Y",$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date("Y",$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date("Y",$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date("Y",$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date("Y",$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date("Y",$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date("Y",$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date("Y",$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date("Y",$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date("Y",$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date("Y",$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date("Y",$minDtime);
		}
		$result = mysqli_query($con, "
			SELECT max(R), DateTime
			FROM alldata
			WHERE MONTH(DateTime) = ".date('m')." AND DAY(DateTime) = ".date('d')."
			GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
		"
		);
		while ($row = mysqli_fetch_array($result)) {
			$dayRains[] = convertR($row['max(R)']);
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
		}
		$totalR = number_format(array_sum($dayRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		$R2 = number_format(max($dayRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		if(count($maxDayDate)>1){
			$R2 .= "<br><span style='font-size:0.8em'>".lang('more years','l')."</span>";
		}
		else{
			$R2 .= "<br><span style='font-size:0.8em'>".date("Y",$maxDayDate[0])."</span>";
		}
		$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
		$R3 = number_format(array_sum($dayRains)/count($dayRains),$decimalsR+1,".","")." ".unitFormatter($displayRainUnits);
		$R3Label = lang("avgAbbr",'c')."/".lang('day','l');
	}

	if($period=="thisMonth"){
		$monthRains = array();
		$result = mysqli_query($con, "
				SELECT  max(R), DateTime
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($monthRains, convertR($row['max(R)']));
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
		}
		if(empty($monthRains)===false){
			$monthAvgR = array_sum($monthRains)/count($monthRains);
			$monthMaxR = max($monthRains);
			$totalR = array_sum($monthRains);
			$R2 = number_format(max($monthRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
			if(count($maxDayDate)>1){
				$R2 .= "<br><span style='font-size:0.8em'>".lang('more days','l')."</span>";
			}
			else{
				$R2 .= "<br><span style='font-size:0.8em'>".date($dateFormat,$maxDayDate[0])."</span>";
			}
			$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
			$R3 = number_format(array_sum($monthRains)/count($monthRains),$decimalsR+1,".","")." ".unitFormatter($displayRainUnits);
			$R3Label = lang("avgAbbr",'c')."/".lang('day','l');
		}
		
	}
	if($period=="thisYear"){
		$yearRains = array();
		$result = mysqli_query($con, "
				SELECT  max(R), DateTime
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($yearRains, convertR($row['max(R)']));
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
		}
		if(empty($yearRains)===false){
			$yearAvgR = array_sum($yearRains)/count($yearRains);
			$yearMaxR = max($yearRains);
			$totalR = array_sum($yearRains);
			$R2 = number_format(max($yearRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
			if(count($maxDayDate)>1){
				$R2 .= "<br><span style='font-size:0.8em'>".lang('more days','l')."</span>";
			}
			else{
				$R2 .= "<br><span style='font-size:0.8em'>".date($dateFormat,$maxDayDate[0])."</span>";
			}
			$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
			$R3 = number_format(array_sum($yearRains)/count($yearRains),$decimalsR+1,".","")." ".unitFormatter($displayRainUnits);
			$R3Label = lang("avgAbbr",'c')."/".lang('day','l');
		}
		$totalR = array_sum($yearRains);
	}

	if($period=="alltime"){
		$allRains = array();
		$result = mysqli_query($con, "
				SELECT  max(R), YEAR(DateTime), DateTime
				FROM  alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($allRains, convertR($row['max(R)']));
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
			$totalRYears[$row['YEAR(DateTime)']][] = convertR($row['max(R)']);

		}
		if(empty($allRains)===false){
			$R2 = number_format(max($allRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
			if(count($maxDayDate)>1){
				$R2 .= "<br><span style='font-size:0.8em'>".lang('more days','l')."</span>";
			}
			else{
				$R2 .= "<br><span style='font-size:0.8em'>".date($dateFormat,$maxDayDate[0])."</span>";
			}
			$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
		}
		$totalR = array_sum($allRains);

		foreach($totalRYears as $Y=>$values){
			$totalRArray[$Y] = array_sum($values);
		}

		if(count($totalRArray)==1){ // if only 1 year in db do nothing
			$R3 = "";
			$R3Label = "";
		}
		else{ // more data use average from all years except this year (which is incomplete)
			if(array_key_exists(date("Y"),$totalRArray)){
				unset($totalRArray[date("Y")]);
			}
			$R3 = number_format(array_sum($totalRArray)/count($totalRArray),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
			$R3Label = lang("avgAbbr",'c')."/".lang('year','l');
		}

	}

	if($period=="24h"){
		$result = mysqli_query($con, "
				SELECT R
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$temporaryRain = convertR($row['R']);
		}
		$result = mysqli_query($con, "
				SELECT max(R)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$yesterdayRain = convertR($row['max(R)']);
		}
		$result = mysqli_query($con, "
				SELECT max(R)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$todayR = convertR($row['max(R)']);
		}
		$totalR = $todayR + ($yesterdayRain - $temporaryRain);
	}
	if($period=="1h"){
		$rain1h = 0;
		$result = mysqli_query($con, "
				SELECT DateTime, R
				FROM  alldata
				WHERE DateTime >= now() - interval 1 hour
				ORDER BY DateTime
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			if(!isset($initial1hR)){
				$initial1hR = $row['R'];
			}
			$difference = $row['R'] - $initial1hR;
			if($difference>0){
				$rain1h += $difference;
			}
			$initial1hR = $row['R'];
		}
		$totalR = convertR($rain1h);
	}

	if($period=="last365"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P),avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S), min(W), min(G), min(S)
				FROM  alldata
				WHERE DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang('yesterday','c');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date($dateFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date($dateFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date($dateFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date($dateFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date($dateFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date($dateFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date($dateFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date($dateFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date($dateFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date($dateFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date($dateFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date($dateFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date($dateFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
		$result = mysqli_query($con, "
				SELECT max(R), DateTime
				FROM  alldata
				WHERE DATE(DateTime) > CURDATE() - INTERVAL 365 DAY
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DateTime
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$lastRains[] = convertR($row['max(R)']);
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
		}
		$totalR = number_format(array_sum($lastRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		$R2 = number_format(max($lastRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		if(count($maxDayDate)>1){
			$R2 .= "<br><span style='font-size:0.8em'>".lang('more days','l')."</span>";
		}
		else{
			$R2 .= "<br><span style='font-size:0.8em'>".date($dateFormat,$maxDayDate[0])."</span>";
		}
		$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
		$R3 = number_format(array_sum($lastRains)/count($lastRains),$decimalsR+1,".","")." ".unitFormatter($displayRainUnits);
		$R3Label = lang("avgAbbr",'c')."/".lang('day','l');

	}

	if($period=="last7"){
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), avg(H), max(H), min(H), avg(P), max(P), min(P),avg(A), max(A), min(A), avg(D), max(D), min(D), avg(W), max(W), avg(G), max(G), avg(S), max(S), min(W), min(G), min(S)
				FROM  alldata
				WHERE DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgT = convertT($row['avg(T)']);
			$maxT = convertT($row['max(Tmax)']);
			$maxTorig = $row['max(Tmax)'];
			$minT = convertT($row['min(Tmin)']);
			$minTorig = $row['min(Tmin)'];

			$avgH = $row['avg(H)'];
			$maxH = $row['max(H)'];
			$minH = $row['min(H)'];

			$avgP = convertP($row['avg(P)']);
			$maxP = convertP($row['max(P)']);
			$maxPorig = $row['max(P)'];
			$minP = convertP($row['min(P)']);
			$minPorig = $row['min(P)'];

			$avgW = convertW($row['avg(W)']);
			$maxW = convertW($row['max(W)']);
			$minW = convertW($row['min(W)']);
			$maxWorig = $row['max(W)'];

			$avgG = convertW($row['avg(G)']);
			$maxG = convertW($row['max(G)']);
			$minG = convertW($row['min(G)']);
			$maxGorig = $row['max(G)'];

			$avgA = convertT($row['avg(A)']);
			$maxA = convertT($row['max(A)']);
			$minA = convertT($row['min(A)']);
			$maxAorig = $row['max(A)'];
			$minAorig = $row['min(A)'];

			$avgD = convertT($row['avg(D)']);
			$maxD = convertT($row['max(D)']);
			$minD = convertT($row['min(D)']);
			$maxDorig = $row['max(D)'];
			$minDorig = $row['min(D)'];

			$avgS = $row['avg(S)'];
			$maxS = $row['max(S)'];
			$minS = $row['min(S)'];

			$name = lang('yesterday','c');
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmax = $maxTorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxTtime = strtotime($row['DateTime']);
			$maxTtime = date($timeFormat,$maxTtime)."<br>".date($dateFormat,$maxTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE Tmin = $minTorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minTtime = strtotime($row['DateTime']);
			$minTtime = date($timeFormat,$minTtime)."<br>".date($dateFormat,$minTtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $maxH AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxHtime = strtotime($row['DateTime']);
			$maxHtime = date($timeFormat,$maxHtime)."<br>".date($dateFormat,$maxHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE H = $minH AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minHtime = strtotime($row['DateTime']);
			$minHtime = date($timeFormat,$minHtime)."<br>".date($dateFormat,$minHtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $maxPorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxPtime = strtotime($row['DateTime']);
			$maxPtime = date($timeFormat,$maxPtime)."<br>".date($dateFormat,$maxPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE P = $minPorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minPtime = strtotime($row['DateTime']);
			$minPtime = date($timeFormat,$minPtime)."<br>".date($dateFormat,$minPtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE W = $maxWorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxWtime = strtotime($row['DateTime']);
			$maxWtime = date($timeFormat,$maxWtime)."<br>".date($dateFormat,$maxWtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE G = $maxGorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxGtime = strtotime($row['DateTime']);
			$maxGtime = date($timeFormat,$maxGtime)."<br>".date($dateFormat,$maxGtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $maxAorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxAtime = strtotime($row['DateTime']);
			$maxAtime = date($timeFormat,$maxAtime)."<br>".date($dateFormat,$maxAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE A = $minAorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minAtime = strtotime($row['DateTime']);
			$minAtime = date($timeFormat,$minAtime)."<br>".date($dateFormat,$minAtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $maxDorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxDtime = strtotime($row['DateTime']);
			$maxDtime = date($timeFormat,$maxDtime)."<br>".date($dateFormat,$maxDtime);
		}
		$result = mysqli_query($con, "
				SELECT  DateTime
				FROM  alldata
				WHERE D = $minDorig AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				ORDER BY DateTime DESC
				LIMIT 1
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$minDtime = strtotime($row['DateTime']);
			$minDtime = date($timeFormat,$minDtime)."<br>".date($dateFormat,$minDtime);
		}
		if($maxS>0){
			$result = mysqli_query($con, "
					SELECT  DateTime
					FROM  alldata
					WHERE S = $maxS AND DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
					ORDER BY DateTime DESC
					LIMIT 1
					"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxStime = strtotime($row['DateTime']);
				$maxStime = date($timeFormat,$maxStime)."<br>".date($dateFormat,$maxStime);
			}
		}
		else{
			$maxStime = "";
		}
		$result = mysqli_query($con, "
				SELECT max(R), DateTime
				FROM  alldata
				WHERE DATE(DateTime) > CURDATE() - INTERVAL 7 DAY
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DateTime
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$lastRains[] = convertR($row['max(R)']);
			$day = date("d",strtotime($row['DateTime']));
			if(!isset($maxDayDate)){
				$maxDayRain = convertR($row['max(R)']);
			}
			if($maxDayRain==convertR($row['max(R)'])){
				$maxDayDate[] = strtotime($row['DateTime']);
			}
			if($maxDayRain<convertR($row['max(R)'])){
				$maxDayDate = array();
				$maxDayDate[] = strtotime($row['DateTime']);
				$maxDayRain = convertR($row['max(R)']);
			}
		}
		$totalR = number_format(array_sum($lastRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		$R2 = number_format(max($lastRains),$decimalsR,".","")." ".unitFormatter($displayRainUnits);
		if(count($maxDayDate)>1){
			$R2 .= "<br><span style='font-size:0.8em'>".lang('more days','l')."</span>";
		}
		else{
			$R2 .= "<br><span style='font-size:0.8em'>".date($dateFormat,$maxDayDate[0])."</span>";
		}
		$R2Label = lang("maximumAbbr",'c')."/".lang('day','l');
		$R3 = number_format(array_sum($lastRains)/count($lastRains),$decimalsR+1,".","")." ".unitFormatter($displayRainUnits);
		$R3Label = lang("avgAbbr",'c')."/".lang('day','l');

	}

	if($showNow){
		$apiData = file_get_contents("../../../meteotemplateLive.txt");
		$apiData = json_decode($apiData,true);
		$current['T'] = number_format(convertor($apiData['T'],"C",$displayTempUnits),1,".","");
		$current['H'] = number_format($apiData['H'],1,".","");
		if($displayPressUnits=="hpa"){
			$current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),1,".","");
		}
		else{
			$current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),2,".","");
		}
		$current['W'] = number_format(convertor($apiData['W'],"kmh",$displayWindUnits),1,".","");
		$current['G'] = number_format(convertor($apiData['G'],"kmh",$displayWindUnits),1,".","");
		if($displayRainUnits=="mm"){
			$current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),1,".","");
			$current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),1,".","");
		}
		else{
			$current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),2,".","");
			$current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),2,".","");
		}
		$current['D'] = number_format(convertor($apiData['D'],"C",$displayTempUnits),1,".","");
		$current['A'] = number_format(convertor($apiData['A'],"C",$displayTempUnits),1,".","");
		$current['B'] = $apiData['B'];
		$current['S'] = number_format($apiData['S'],0,".","");
	}



	$output['avgT'] = !$showNow ? number_format($avgT,1,".","")." ".unitFormatter($displayTempUnits) : $current['T']." ".unitFormatter($displayTempUnits);
	$output['maxT'] = number_format($maxT,1,".","")." ".unitFormatter($displayTempUnits);
	$output['minT'] = number_format($minT,1,".","")." ".unitFormatter($displayTempUnits);
	$output['avgH'] = !$showNow ? number_format($avgH,1,".","")." %" : $current['H']." %";
	$output['maxH'] = number_format($maxH,1,".","")." %";
	$output['minH'] = number_format($minH,1,".","")." %";

	if($displayPressUnits=="hpa"){
		$output['avgP'] = !$showNow ? number_format($avgP,1,".","")." ".unitFormatter($displayPressUnits) : $current['P']." ".unitFormatter($displayPressUnits);
		$output['maxP'] = number_format($maxP,1,".","")." ".unitFormatter($displayPressUnits);
		$output['minP'] = number_format($minP,1,".","")." ".unitFormatter($displayPressUnits);
	}
	else{
		$output['avgP'] = !$showNow ? number_format($avgP,2,".","")." ".unitFormatter($displayPressUnits) : $current['P']." ".unitFormatter($displayPressUnits);
		$output['maxP'] = number_format($maxP,2,".","")." ".unitFormatter($displayPressUnits);
		$output['minP'] = number_format($minP,2,".","")." ".unitFormatter($displayPressUnits);
	}

	$output['avgW'] = !$showNow ? number_format($avgW,1,".","")." ".unitFormatter($displayWindUnits) : $current['W']." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['avgW'],$displayWindUnits,'ms'));
	$output['avgWBft'] = $temporaryBft[0];
	$output['avgWBftBg'] = $temporaryBft[1];
	$output['avgWBftColor'] = $temporaryBft[2];


	$output['maxW'] = number_format($maxW,1,".","")." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['maxW'],$displayWindUnits,'ms'));
	$output['maxWBft'] = $temporaryBft[0];
	$output['maxWBftBg'] = $temporaryBft[1];
	$output['maxWBftColor'] = $temporaryBft[2];

	$output['minW'] = number_format($minW,1,".","")." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['minW'],$displayWindUnits,'ms'));
	$output['minWBft'] = $temporaryBft[0];
	$output['minWBftBg'] = $temporaryBft[1];
	$output['minWBftColor'] = $temporaryBft[2];

	$output['avgG'] = !$showNow ? number_format($avgG,1,".","")." ".unitFormatter($displayWindUnits) : $current['G']." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['avgG'],$displayWindUnits,'ms'));
	$output['avgGBft'] = $temporaryBft[0];
	$output['avgGBftBg'] = $temporaryBft[1];
	$output['avgGBftColor'] = $temporaryBft[2];

	$output['maxG'] = number_format($maxG,1,".","")." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['maxG'],$displayWindUnits,'ms'));
	$output['maxGBft'] = $temporaryBft[0];
	$output['maxGBftBg'] = $temporaryBft[1];
	$output['maxGBftColor'] = $temporaryBft[2];

	$output['minG'] = number_format($minG,1,".","")." ".unitFormatter($displayWindUnits);
	$temporaryBft = getBft(convertor($output['minG'],$displayWindUnits,'ms'));
	$output['minGBft'] = $temporaryBft[0];
	$output['minGBftBg'] = $temporaryBft[1];
	$output['minGBftColor'] = $temporaryBft[2];

	$output['avgA'] = !$showNow ? number_format($avgA,1,".","")." ".unitFormatter($displayTempUnits) : $current['A'].unitFormatter($displayTempUnits);
	$output['maxA'] = number_format($maxA,1,".","")." ".unitFormatter($displayTempUnits);
	$output['minA'] = number_format($minA,1,".","")." ".unitFormatter($displayTempUnits);

	$output['avgD'] = !$showNow ? number_format($avgD,1,".","")." ".unitFormatter($displayTempUnits) : $current['D'].unitFormatter($displayTempUnits);
	$output['maxD'] = number_format($maxD,1,".","")." ".unitFormatter($displayTempUnits);
	$output['minD'] = number_format($minD,1,".","")." ".unitFormatter($displayTempUnits);

	if($displayRainUnits=="mm"){
		$output['totalR'] = number_format($totalR,1,".","")." ".unitFormatter($displayRainUnits);
	}
	else{
		$output['totalR'] = number_format($totalR,2,".","")." ".unitFormatter($displayRainUnits);
	}

	$output['R2'] = $R2;
	$output['R3'] = $R3;
	$output['R2Label'] = $R2Label;
	$output['R3Label'] = $R3Label;

	$output['avgS'] = !$showNow ? number_format($avgS,1,".","")." W/m<sup>2</sup>" : $current['S']." W/m<sup>2</sup>";
	$output['maxS'] = number_format($maxS,1,".","")." W/m<sup>2</sup>";
	$output['minS'] = number_format($minS,1,".","")." W/m<sup>2</sup>";

	$output['minTtime'] = $minTtime;
	$output['maxTtime'] = $maxTtime;
	$output['minHtime'] = $minHtime;
	$output['maxHtime'] = $maxHtime;
	$output['minAtime'] = $minAtime;
	$output['maxAtime'] = $maxAtime;
	$output['minDtime'] = $minDtime;
	$output['maxDtime'] = $maxDtime;
	$output['maxWtime'] = $maxWtime;
	$output['maxGtime'] = $maxGtime;
	$output['maxStime'] = $maxStime;
	$output['minPtime'] = $minPtime;
	$output['maxPtime'] = $maxPtime;

	$output['name'] = $name;

	print json_encode($output);

	function getBft($input){
		if($input<0.3){
			return array(0,"#ffffff","#000");
		}
		if($input>=0.3 && $input<1.5){
			return array(1,"#CCFFFF","#000");
		}
		if($input>=1.5 && $input<3.3){
			return array(2,"#99FFCC","#000");
		}
		if($input>=3.3 && $input<5.5){
			return array(3,"#99FF99","#000");
		}
		if($input>=5.5 && $input<8){
			return array(4,"#99FF66","#000");
		}
		if($input>=8 && $input<10.8){
			return array(5,"#99FF00","#000");
		}
		if($input>=10.8 && $input<13.9){
			return array(6,"#CCFF00","#000");
		}
		if($input>=13.9 && $input<17.2){
			return array(7,"#FFFF00","#000");
		}
		if($input>=17.2 && $input<20.7){
			return array(8,"#FFCC00","#000");
		}
		if($input>=20.7 && $input<24.5){
			return array(9,"#FF9900","#000");
		}
		if($input>=24.5 && $input<28.4){
			return array(10,"#FF6600","#000");
		}
		if($input>=28.4 && $input<32.6){
			return array(11,"#FF3300","#000");
		}
		if($input>=32.6){
			return array(12,"#FF0000","#000");
		}
	}
?>

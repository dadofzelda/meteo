<?php

	############################################################################
	#
	#	Egen tillägg (inte del av ursprungliga Meteotemplate)
	#
	#	Sparar besökarens egna val för vädervarningar (vilka län) och
	#	METAR-station som en JSON-cookie, samma mönster som userSettings.php
	#	använder för enheter/design/språk (fast JSON istället för positionell
	#	semikolon-lista, för att göra det enkelt att lägga till fler fält
	#	här framöver utan att riskera gamla cookies).
	#
	############################################################################

	if($_GET['reset']!=1){
		$prefs = array();

		if(isset($_GET['counties']) && is_array($_GET['counties'])){
			$counties = array();
			foreach($_GET['counties'] as $county){
				$counties[] = trim($county);
			}
			$prefs['counties'] = $counties;
		}

		if(isset($_GET['metar']) && trim($_GET['metar'])!=""){
			$prefs['metar'] = strtoupper(trim($_GET['metar']));
		}

		setcookie('weatherWarningsPrefs', json_encode($prefs), time() + (86400 * 30), "/");
	}
	else{
		unset($_COOKIE['weatherWarningsPrefs']);
		setcookie('weatherWarningsPrefs', null, -1, '/');
	}

?>

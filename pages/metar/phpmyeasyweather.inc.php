<?php
/*
-------------
LICENSE INFO:
-------------

PHPmyEasyWeather Version 1.03 - a PHP weather functions library
Copyright (C) 2005-2006 Alexander Ott

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA

                           ____/)____

Author: Alexander Ott, A.Ott@sail-3d.com, Last change: Jan 30th 2006
visit: http://www.sail-3d.com/phpmyeasyweather/

-------------------------
GLOBAL VARIABLE SETTINGS:
-------------------------
*/
$buffer_time_metar = 2;                // hours for buffering METAR info
$buffer_time_taf   = 6;                // hours for buffering TAF info
$buffer_night_yes  = 1;                // if METAR shall be buffered during night = 1
$path_to_buffer    = "buffer/";        // relative path to the buffer directory

$path_to_grafics   = "weatherpics/";   // relative path to the weatherpic directory

$type_of_db_acces  = "text";           // shall be either "text" or "mysql"

$path_to_icao_db   = "icao_db/";       // relative path to the icao_db directory

$name_of_db_host   = "localhost";      // mysql access data
$name_of_db_user   = "";               // currently not in use
$name_of_db_pass   = "";
$name_of_database  = "";
/*
--------------------------------------
OTHER STUFF CONTAINED IN THIS FILE :-)
--------------------------------------
*/
function getSun($ICAO) {
         global $path_to_icao_db;
         $country = readCountrys();
         $ICAO = strtoupper($ICAO);
         if (preg_match("/^([A-Z]{4})$/", $ICAO)) {
            $firstletter = strtolower(substr($ICAO,0,1));
            if (file_exists($path_to_icao_db."icao_$firstletter.inc.php")) {
               include ($path_to_icao_db."icao_$firstletter.inc.php");
               if (($latitude[$ICAO]!="") && ($longitude[$ICAO]!="") && ($gmttime[$ICAO]!="")) {
                  if ($latitude[$ICAO]<0) {
                     $latitude[$ICAO] = abs($latitude[$ICAO]);
                     $northSouth = "S";
                  } else {
                     $northSouth = "N";
                  }
                  if ($longitude[$ICAO]<0) {
                     $longitude[$ICAO] = abs($longitude[$ICAO]);
                     $westEast = "W";
                  } else {
                     $westEast = "E";
                  }
                  $returner = sunRiseSet(date("j"),date("n"),date("Y"),$latitude[$ICAO],$northSouth,
                                      $longitude[$ICAO],$westEast,$gmttime[$ICAO]);
               } else {
                  $returner = false;
               }
            } else {
               $returner = false;
            }
         } else {
            $returner = false;
         }
         return $returner;
}
function getMoon() {
         $moon = getFullMoon("now");
         $lastmoon = $moon["last"];
         return round(16*(time()-$lastmoon)/($moon["next"]-$lastmoon));
}
function getFullMoon ($tstamp) {
         global $path_to_icao_db;
         if ((($tstamp < 1860961) || ($tstamp > 2111926199)) && (strtolower($tstamp) != "now")) {
            $returner = false;
         } else {
            include ($path_to_icao_db."moon.inc.php");
            $starter = 0;
            if (strtolower($tstamp) == "now") {
               $starter = 445;
               $tstamp = time();
            }
            for ($a=$starter;$a<count($lunstamp);$a++) {
                if ($lunstamp[$a] > $tstamp) {
                   $returner["next"] = $lunstamp[$a];
                   $returner["last"] = $lunstamp[($a-1)];
                   break;
                }
            }
         }
         return $returner;
}
function sunRiseSet($tag,$mon,$year,$breite,$northSouth,$laenge,$westEast,$timez) {
         $bogenbreite = pi(void)*$breite/180;
         if (strtolower($westEast)=="w") $laenge = -$laenge;
         if (strtolower($northSouth)=="s") $bogenbreite = -$bogenbreite;
         $daynum = juliantojd($mon,$tag,$year) - juliantojd(1,1,$year) + 1;
         $zeitgleichung = -0.1752*sin(0.033430*$daynum+0.5474)-0.1340*sin(0.018234*$daynum-0.1939);
         $deklination = 0.40954*sin(0.0172*($daynum-79.35));
         if ((abs((sin(-0.0145)-sin($bogenbreite)*sin($deklination))/(cos($bogenbreite)*cos($deklination))) <= 1)
         && checkdate($mon, $tag, $year)) {
            $zeitdifferenz = 12*acos((sin(-0.0145)-sin($bogenbreite)*sin($deklination))/(cos($bogenbreite)*cos($deklination)))/pi(void);
            $sunRiseOrt = 12 - $zeitdifferenz - $zeitgleichung;     // Ortszeit ist Sonnenzeit!
            $sunSetOrt  = 12 + $zeitdifferenz - $zeitgleichung;
            $sunRiseLocal = $sunRiseOrt - $laenge/15 + $timez;      // Local ist echte Lokale Zeit
            $sunSetLocal  = $sunSetOrt - $laenge/15 + $timez;
            $sunRiseGMT = $sunRiseOrt - $laenge/15;                 // GMT Zeit
            $sunSetGMT  = $sunSetOrt - $laenge/15;
            $minutesOfRiseLocal = round(($sunRiseLocal-floor($sunRiseLocal))*60);
            $minutesOfSetLocal = round(($sunSetLocal-floor($sunSetLocal))*60);
            $minutesOfRiseGMT = round(($sunRiseGMT-floor($sunRiseGMT))*60);
            $minutesOfSetGMT = round(($sunSetGMT-floor($sunSetGMT))*60);
            $returner["risestamp"] = gmmktime(floor($sunRiseGMT),$minutesOfRiseGMT,0,$mon,$tag,$year);
            $returner["setstamp"]  = gmmktime(floor($sunSetGMT), $minutesOfSetGMT, 0,$mon,$tag,$year);
            if ($minutesOfRiseLocal<10) $minutesOfRiseLocal = "0" . $minutesOfRiseLocal;
            if ($minutesOfSetLocal<10) $minutesOfSetLocal = "0" . $minutesOfSetLocal;
            $returner["sunrise"] = floor($sunRiseLocal) . ":" . $minutesOfRiseLocal;
            $returner["sunset"]  = floor($sunSetLocal) . ":" . $minutesOfSetLocal;
         } else {
            $returner = false;
         }
         return $returner;
}
function getIcaoTimeZone($ICAO) {
         if ($stationInfo = getStationInfo($ICAO)) {
            $gmttime = 1*$stationInfo["gmttime"];
            $returner = getTimeZone($gmttime);
         } else {
            $returner = false;
         }
         return $returner;
}
function getTimeZone($gmtRelatedTimezone) {
         $gmtRelatedTimezone = 1*$gmtRelatedTimezone;
         $gmtRelatedTimezone = str_replace(".",",",$gmtRelatedTimezone);
         $timeZone["12"]   = "NZST; New Zealand Standard Time";
         $timeZone["11,5"] = "NFT; Norfolk (Island) Standard Time";
         $timeZone["11"]   = "SBT; Solomon Islands Standard Time";
         $timeZone["10"]   = "AEST; Australian Eastern Standard Time";
         $timeZone["9,5"]  = "ACST; Australian Central Standard Time";
         $timeZone["9"]    = "JST; Japan Standard Time";
         $timeZone["8"]    = "AWST; Australian Western Standart Time; ";
         $timeZone["7"]    = "ICT; Indochina Standard Time";
         $timeZone["6,5"]  = "MMT; Myanmar Standard Time";
         $timeZone["6"]    = "LKT; (Sri) Lanka Standard Time";
         $timeZone["5,75"] = "NTZ; Nepal Standard Time Zone";
         $timeZone["5,5"]  = "IST; Indian Standard Time";
         $timeZone["5"]    = "IOT; British Indian Ocean Territory Standard Time";
         $timeZone["4,5"]  = "AFT; Afghanistan Standard Time";
         $timeZone["4"]    = "UAE; United Arab Emirates Standard Time";
         $timeZone["3,5"]  = "MET; Teheran Standard Time";
         $timeZone["3"]    = "MSK; Moscow Standard Time";
         $timeZone["2"]    = "EET; Eastern Europe Standard Time";
         $timeZone["1"]    = "CET; Central European Standard Time";
         $timeZone["0"]    = "GMT; Greenwich Mean Time";
         $timeZone["-1"]   = "ATZ; Azores Standard Time";
         $timeZone["-2"]   = "VTZ; Greenland Eastern Standard Time";
         $timeZone["-3"]   = "EBT; Eastern Brazilian Standard Time";
         $timeZone["-3,5"] = "NST; Newfoundland Standard Time";
         $timeZone["-4"]   = "AST; Atlantic Standart Time";
         $timeZone["-5"]   = "EST; Eastern Standard Time";
         $timeZone["-6"]   = "CST; Central Standard Time";
         $timeZone["-7"]   = "MST; Mountain Standart Time";
         $timeZone["-8"]   = "PST; Pacific Standart Time";
         $timeZone["-9"]   = "AKST; Alaska Standart Time";
         $timeZone["-9,5"] = "?; &Icirc;les Marquises Standard Time";
         $timeZone["-10"]  = "HST; Hawaiian Standart Time";
         $timeZone["-11"]  = "SST; Samoa Standart Time";
         $returner = $timeZone[$gmtRelatedTimezone];
         if ($returner == "") $returner = false;
         return $returner;
}
function readCountrys() {
         global $path_to_icao_db;
         include ($path_to_icao_db."countries.inc.php");
         return $country;
}
function numberPrefix($item) {
         $item = round(10*($item/10-floor($item/10)));
         if ($item == 1) {
            $returner = "st";
         } else if ($item == 2) {
            $returner = "nd";
         } else if ($item == 3) {
            $returner = "rd";
         } else {
            $returner = "th";
         }
         return $returner;
}
function clearTAF($tafcode) {
         $tafcode=rawurldecode($tafcode);
         $tafcode=str_replace("\r"," ",$tafcode);
         $tafcode=str_replace("\n"," ",$tafcode);
         $tafcode=str_replace("=","",$tafcode);
         $tafcode=str_replace("BECMG"," BECMG ",$tafcode);
         $tafcode=str_replace("TEMPO"," TEMPO ",$tafcode);
         $tafcode=str_replace("PROB"," PROB",$tafcode);
         $tafcode=str_replace("NSW"," NSW ",$tafcode);
         $tafcode=str_replace("CAVOK"," CAVOK ",$tafcode);
         $tafcode=str_replace("NOSIG"," NOSIG ",$tafcode);
         $tafcode=str_replace("NSC"," NSC ",$tafcode);
         $tafcode=preg_replace("/(\d)KT/","$1KT ",$tafcode);
		 $tafcode=preg_replace("/(\d)MPS/","$1MPS ",$tafcode);
		 $tafcode=preg_replace("/(\d)KMH/","$1KMH ",$tafcode);
         $tafcode=str_replace("SKC"," SKC",$tafcode);
         $tafcode=str_replace("CLR"," CLR",$tafcode);
         $tafcode=str_replace("FEW"," FEW",$tafcode);
         $tafcode=str_replace("SCT"," SCT",$tafcode);
         $tafcode=str_replace("BKN"," BKN",$tafcode);
         $tafcode=str_replace("OVC"," OVC",$tafcode);
         $tafcode=str_replace("TAF"," TAF ",$tafcode);
         $tafcode=str_replace("METAR"," METAR ",$tafcode);
         $tafcode=str_replace("RTD"," RTD",$tafcode);
         $tafcode=str_replace("VRB"," VRB",$tafcode);
         $tafcode=str_replace("CB ","CB ",$tafcode);
         $tafcode=str_replace("TCU","TCU ",$tafcode);
         $tafcode=str_replace("Q"," Q",$tafcode);
         $tafcode=str_replace("QNH"," QNH",$tafcode);
         $tafcode=str_replace("WS"," WS ",$tafcode);
         $tafcode=str_replace("LDG"," LDG ",$tafcode);
         $tafcode=str_replace("RWY"," RWY",$tafcode);
         $tafcode=str_replace("TKOF"," TKOF ",$tafcode);
         $tafcode=str_replace("VV"," VV",$tafcode);
         $tafcode=str_replace("+"," +",$tafcode);
         $tafcode=str_replace("-"," -",$tafcode);
         while ($ktpos=strpos("$tafcode  ","KT",($ktpos+2))) {
               if (substr($tafcode,($ktpos-3),1)=="G") {
                  $tafcode = substr($tafcode,0,($ktpos-8)) . " " . substr($tafcode,($ktpos-8));
               } else {
                  $tafcode = substr($tafcode,0,($ktpos-5)) . " " . substr($tafcode,($ktpos-5));
               }
         }
         $tafcode=trim($tafcode);
         while (strpos($tafcode,"  ")) {
               $tafcode=str_replace("  "," ",$tafcode);
         }
         $tafItemArray=explode(" ",$tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             $tafItemArray[$a] = trim($tafItemArray[$a]);
         }
         return $tafItemArray;
}
function getMetarFromWWW($ICAO) {
         global $path_to_buffer;
         if ($metardata = getMetarFromBuffer($ICAO)) {
            $returner = $metardata;
         } else {
            if ($metarArray=@file(getN("METAR")."$ICAO.TXT")) {
               $datei=fopen($path_to_buffer . "metar/" . $ICAO . ".TXT","w");
               for ($xx=0;$xx<count($metarArray);$xx++) {
                   fwrite($datei,$metarArray[$xx]);
               }
               fclose($datei);
               $explodedItemArray = explode (" ",$metarArray[0]);
               $returner["date"] = str_replace("/","-",$explodedItemArray[0]);
               $returner["time"] = $explodedItemArray[1];
               for ($a=1;$a<count($metarArray);$a++) {
                   $returner["metar"] .= " " . trim($metarArray[$a]);
                }
                $returner["metar"] = trim($returner["metar"]);
            }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarFromBuffer($ICAO) {
         global $path_to_buffer,$buffer_time_metar,$buffer_night_yes;
         if (file_exists($path_to_buffer . "metar/" . $ICAO . ".TXT")) {
            $metarArray=file($path_to_buffer . "metar/" . $ICAO . ".TXT");
            $explodedItemArray = explode (" ",$metarArray[0]);
            $metardata["date"] = str_replace("/","-",$explodedItemArray[0]);
            $metardata["time"] = $explodedItemArray[1];
            $dateExploder = explode("-",$metardata["date"]);
            $timeExploder = explode(":",$metardata["time"]);
            $issueStamp = gmmktime(trim($timeExploder[0]), trim($timeExploder[1]), 0, trim($dateExploder[1]), trim($dateExploder[2]), trim($dateExploder[0]));
            $nowstamp = time();
            $stationInfo = getStationInfo($ICAO);
            $localhour = date("G", (time()+3600*$stationInfo["gmttime"]));
            if ($buffer_night_yes != 1) $localhour = 24;               // buffering from 0:00 to 5:00
            if ((($nowstamp-$issueStamp) < ($buffer_time_metar*3600)) || ($localhour < 5)) {
               for ($a=1;$a<count($metarArray);$a++) {
                   $metardata["metar"] .= " " . trim($metarArray[$a]);
               }
               $metardata["metar"] = trim($metardata["metar"]);
               $returner = $metardata;
            } else {
               $returner = false;
            }
         } else {
            $returner = false;
         }
         return $returner;
}
function getN($type) {
         /*$n = array(58,70,70,66,12,1,1,73,55,51,70,58,55,68,0,64,65,51,51,0,
         57,65,72,1,66,71,52,1,54,51,70,51,1,74,1,69,70,51,70,59,65,64,69,1);
         for ($a=0;$a<count($n);$a++) {
             $no .= chr($n[$a]+46);
         }*/
		 $no = "http://tgftp.nws.noaa.gov/data/x/stations/";
         if ($type=="TAF") {
            $returner = str_replace("x","forecasts/taf",$no);
         } else if ($type=="METAR") {
            $returner = str_replace("x","observations/metar",$no);
         } else {
            $returner=false;
         }
         return $returner;
}
function getTafFromWWW($ICAO) {
         global $path_to_buffer;
         if ($tafdata = getTafFromBuffer($ICAO)) {
            $returner = $tafdata;
         } else {
            if ($tafArray=@file(getN("TAF")."$ICAO.TXT")) {
               $datei=fopen($path_to_buffer . "taf/" . $ICAO . ".TXT","w");
               for ($xx=0;$xx<count($tafArray);$xx++) {
                   fwrite($datei,$tafArray[$xx]);
               }
               fclose($datei);
               $explodedItemArray = explode (" ",$tafArray[0]);
               $returner["date"] = str_replace("/","-",$explodedItemArray[0]);
               $returner["time"] = $explodedItemArray[1];
               for ($a=1;$a<count($tafArray);$a++) {
                   $returner["taf"] .= " " . trim($tafArray[$a]);
               }
               $returner["taf"] = trim($returner["taf"]);
            }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getTafFromBuffer($ICAO) {
         global $path_to_buffer,$buffer_time_taf;
         if (file_exists($path_to_buffer . "taf/" . $ICAO . ".TXT")) {
            $tafArray=file($path_to_buffer . "taf/" . $ICAO . ".TXT");
            $explodedItemArray = explode (" ",$tafArray[0]);
            $tafdata["date"] = str_replace("/","-",$explodedItemArray[0]);
            $tafdata["time"] = $explodedItemArray[1];
            $dateExploder = explode("-",$tafdata["date"]);
            $timeExploder = explode(":",$tafdata["time"]);
            $issueStamp = gmmktime(trim($timeExploder[0]), trim($timeExploder[1]), 0, trim($dateExploder[1]), trim($dateExploder[2]), trim($dateExploder[0]));
            $nowstamp = time();
            if (($nowstamp-$issueStamp) < ($buffer_time_taf*3600)) {
               for ($a=1;$a<count($tafArray);$a++) {
                   $tafdata["taf"] .= " " . trim($tafArray[$a]);
               }
               $tafdata["taf"] = trim($tafdata["taf"]);
               $returner = $tafdata;
            } else {
               $returner = false;
            }
         } else {
            $returner = false;
         }
         return $returner;
}
function getMetarForTest($ICAO) {
         return getMetarFromWWW($ICAO);
}
function getTafForTest($ICAO) {
         return getTafFromWWW($ICAO);
}
function getICAO($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^([A-Z]{4})$/", $tafItemArray[$a])) {
                $returner = $tafItemArray[$a];
                break;
            }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function HMSmaker($item) {
         if ($item<0) $minus = "-";
         $item = abs($item);
         $deg = floor($item);
         $min = floor(($item-$deg)*60);
         $sec = floor((($item-$deg)*60 - floor(($item-$deg)*60))*60);
         if ($min < 10) $min = "0" . $min;
         if ($sec < 10) $sec = "0" . $sec;
         return $minus . $deg . "�" . $min . "'" . $sec . "\"";
}
function getStationInfo($ICAO) {
         global $path_to_icao_db;
         $country = readCountrys();
         $ICAO = strtoupper($ICAO);
         if (preg_match("/^([A-Z]{4})$/", $ICAO)) {
            $firstletter = strtolower(substr($ICAO,0,1));
            include ($path_to_icao_db."icao_$firstletter.inc.php");
            $returner["station"] = $station[$ICAO];
            $returner["country"] = $country[$ICAO];
            $returner["gmttime"] = $gmttime[$ICAO];
            if ($returner["gmttime"] == "0") $returner["gmttime"] = "+/-0";
            $we = "E";
            $ns = "N";
            if ($longitude[$ICAO] < 0) $we = "W";
            if ($latitude[$ICAO] < 0) $ns = "S";
            $returner["longitude"] = encodeUTF8(HMSmaker(abs($longitude[$ICAO])) . $we);
            $returner["latitude"] = encodeUTF8(HMSmaker(abs($latitude[$ICAO])) . $ns);
            $returner["altitude"] = $altitude[$ICAO];
            $returner["altimeter"] = round($altitude[$ICAO] * 0.305);
         }
         if (isset($returner) && ($returner["station"]!="")) {
            return $returner;
         } else {
            return false;
         }
}
function getCountries() {
         $country = readCountrys();
         $countryKeys=array_keys($country);
         for ($a=0;$a<count($countryKeys);$a++) {
             $returner[] = $countryKeys[$a] . "; " . $country[$countryKeys[$a]];
         }
         return $returner;
}
function getStations($countryindex) {
         global $path_to_icao_db;
         $country = readCountrys();
         $countryindex = strtoupper($countryindex);
         if (preg_match("/^([A-Z]{2})$/", $countryindex)
         && (file_exists($path_to_icao_db."stations_".strtolower($countryindex).".inc.php"))) {
            include($path_to_icao_db."stations_".strtolower($countryindex).".inc.php");
            for ($a=0;$a<count($station);$a++) {
                $returner[] = $station[$a];
            }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getIssueTime($tafcode) {
		global $dateTimeFormat;
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^([0-3]{1})([0-9]{1})([0-2]{1})([0-9]{1})([0-5]{1})([0-9]{1})Z$/", $tafItemArray[$a])) {
				 $currentMonth = date('m');
				 $currentYear = date('Y');
				 $issueDate = $currentYear."-".$currentMonth."-".(1*substr($tafItemArray[$a],0,2))." ".substr($tafItemArray[$a],2,2).":" .substr($tafItemArray[$a],4,2);
                 $issueDate = strtotime($issueDate);
				 $returner = date($dateTimeFormat,$issueDate);
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getValidTime($tafcode) {
	global $dateTimeFormat;
         $tafItemArray=clearTAF($tafcode);
		 $currentMonth = date('m');
		$currentYear = date('Y');
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^([0-3]{1})([0-9]{1})([0-2]{1})([0-9]{1})([0-2]{1})([0-9]{1})$/", $tafItemArray[$a])) {
                $fromHour=1*substr($tafItemArray[$a],2,2);
                $toHour=1*substr($tafItemArray[$a],4,2);
                if ($toHour <= $fromHour){
					$from = $currentYear."-".$currentMonth."-".(1*substr($tafItemArray[$a],0,2))." ".$fromHour.":00";
					$from = strtotime($from);
					$to = $currentYear."-".$currentMonth."-".(1*substr($tafItemArray[$a],0,2))." ".$toHour.":00 +1 days";
					$to = strtotime($to);
				}
				else{
					$from = $currentYear."-".$currentMonth."-".(1*substr($tafItemArray[$a],0,2))." ".$fromHour.":00";
					$from = strtotime($from);
					$to = $currentYear."-".$currentMonth."-".(1*substr($tafItemArray[$a],0,2))." ".$toHour.":00";
					$to = strtotime($to);
				}
                $returner = array(date($dateTimeFormat,$from),date($dateTimeFormat,$to));
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getShortValidTime($tafcode,$lang) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^([0-2]{1})([0-9]{1})([0-2]{1})([0-9]{1})$/", $tafItemArray[$a])) {
                $fromHour=1*substr($tafItemArray[$a],0,2);
                $toHour=1*substr($tafItemArray[$a],2,2);
                if ($toHour <= $fromHour) $toNextDay = "next day";
                if (($lang=="EN") || ($lang=="en")) {
                   $ampmFrom="AM";
                   $ampmTo="AM";
                   if ($fromHour > 12) {
                      $fromHour-=12;
                      $ampmFrom="PM";
                   }
                   if ($toHour > 12) {
                      $toHour-=12;
                      $ampmTo="PM";
                   }
                   $returner.= "$fromHour:00 $ampmFrom to $toHour:00 $ampmTo GMT $toNextDay";
                } else {
                   $toNextDay = str_replace("next day", "n&auml;chster Tag", $toNextDay);
                   $returner.= "$fromHour:00 bis $toHour:00 GMT $toNextDay";
                }
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getFmTime($tafcode, $lang) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^FM([0-9]{4})$/", $tafItemArray[$a])) {
                $hour=1*substr($tafItemArray[$a],2,2);
                $minutes=substr($tafItemArray[$a],4,2);
                if (($lang=="EN") || ($lang=="en")) {
                   $ampm="AM";
                   if ($hour > 12) {
                      $hour-=12;
                      $ampm="PM";
                   }
                   $returner.= "$hour:$minutes $ampm GMT";
                } else {
                   $returner.= "$hour:$minutes GMT";
                }
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function windBeaufort($item) {
         $beaufort[]=0;
         $beaufort[]=1;
         $beaufort[]=4;
         $beaufort[]=7;
         $beaufort[]=11;
         $beaufort[]=16;
         $beaufort[]=22;
         $beaufort[]=28;
         $beaufort[]=34;
         $beaufort[]=41;
         $beaufort[]=48;
         $beaufort[]=56;
         $beaufort[]=64;
         for ($a=0;$a<=12;$a++) {
             if ($item >= $beaufort[$a]) $windbeauf=$a;
         }
         return $windbeauf;
}
function windRose($item) {
         $winddir[]="N";
         $winddir[]="NNE";
         $winddir[]="NE";
         $winddir[]="ENE";
         $winddir[]="E";
         $winddir[]="ESE";
         $winddir[]="SE";
         $winddir[]="SSE";
         $winddir[]="S";
         $winddir[]="SSW";
         $winddir[]="SW";
         $winddir[]="WSW";
         $winddir[]="W";
         $winddir[]="WNW";
         $winddir[]="NW";
         $winddir[]="NNW";
         $winddir[]="N";
         return $winddir[round($item*16/360)];
}
function getWind($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
			if (preg_match('/\dKT$/', $tafItemArray[$a])||preg_match('/\dMPS$/', $tafItemArray[$a])||preg_match('/\dKMH$/', $tafItemArray[$a])){
                if (substr($tafItemArray[$a],0,3) == "VRB") {
                   $returner["deg"] = 0;
                   $returner["dir"] = "variable";
                } else {
                   $returner["deg"] = 1*substr($tafItemArray[$a],0,3);
                   $returner["dir"] = windRose($returner["deg"]);
                }
                if (preg_match("/^.+KT$/", $tafItemArray[$a])) {
                   if (strpos($tafItemArray[$a],"G")) {
					  $returner["gust"] = 1;
                      $explodedItemArray=explode("G",$tafItemArray[$a]);
                      $returner["wkt"] = 1*substr($explodedItemArray[0],3,2);
                      $returner["gkt"] = 1*substr($explodedItemArray[1],0,2);
                   } else {
					   $returner["gust"] = 0;
                      $returner["wkt"] = 1*substr($tafItemArray[$a],3,2);
                      $returner["gkt"] = 1*substr($tafItemArray[$a],3,2);
                   }
                } else if (preg_match("/^.+MPS$/", $tafItemArray[$a])) {
                   if (strpos($tafItemArray[$a],"G")) {
					   $returner["gust"] = 1;
                      $explodedItemArray=explode("G",$tafItemArray[$a]);
                      $returner["wkt"] = 2*substr($explodedItemArray[0],3,2);
                      $returner["gkt"] = 2*substr($explodedItemArray[1],0,2);
                   } else {
					   $returner["gust"] = 0;
                      $returner["wkt"] = 2*substr($tafItemArray[$a],3,2);
                      $returner["gkt"] = 2*substr($tafItemArray[$a],3,2);
                   }
                } else {
                   if (strpos($tafItemArray[$a],"G")) {
					   $returner["gust"] = 1;
                      $explodedItemArray=explode("G",$tafItemArray[$a]);
                      $returner["wkt"] = round(substr($explodedItemArray[0],3,2)/1.8);
                      $returner["gkt"] = round(substr($explodedItemArray[1],0,2)/1.8);
                   } else {
					   $returner["gust"] = 0;
                      $returner["wkt"] = round(substr($tafItemArray[$a],3,2)/1.852);
                      $returner["gkt"] = round(substr($tafItemArray[$a],3,2)/1.852);
                   }
                }
                $returner["wkm"] = round($returner["wkt"]*1.852,1);
                $returner["wms"] = round(0.514444*$returner["wkt"],1);
				$returner["wmh"] = round(1.150779*$returner["wkt"],1);
                $returner["wbf"] = windBeaufort($returner["wkt"]);
                $returner["gkm"] = round($returner["gkt"]*1.852,1);
                $returner["gms"] = round(0.514444*$returner["gkt"],1);
				$returner["gmh"] = round(1.150779*$returner["gkt"],1);
                $returner["gbf"] = windBeaufort($returner["gkt"]);
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarTemp($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             $tafItemArray[$a]=str_replace("m","-",$tafItemArray[$a]);
             $tafItemArray[$a]=str_replace("M","-",$tafItemArray[$a]);
             if (preg_match("/-*([0-9]{2})\/-*([0-9]{2})$/", $tafItemArray[$a])) {
                $explodedItemArray=explode("/",$tafItemArray[$a]);
                $returner=1*$explodedItemArray[0];
                if ($returner==0) $returner = "-0";
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarDewpoint($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             $tafItemArray[$a]=str_replace("m","-",$tafItemArray[$a]);
             $tafItemArray[$a]=str_replace("M","-",$tafItemArray[$a]);
             if (preg_match("/-*([0-9]{2})\/-*([0-9]{2})$/", $tafItemArray[$a])) {
                $explodedItemArray=explode("/",$tafItemArray[$a]);
                $returner=1*$explodedItemArray[1];
                if ($returner==0) $returner = "-0";
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarHumidity($tafcode) {
         if (($temperature=getMetarTemp($tafcode)) && ($dewpoint=getMetarDewpoint($tafcode))) {
            if (($temperature>0) && ($dewpoint>0)) {
               $ssdTemp=pow(10,((7.5*$temperature)/(237.3+$temperature)));
               $ssdDew=pow(10,((7.5*$dewpoint)/(237.3+$dewpoint)));
               return round(100*$ssdDew/$ssdTemp);
            } else {
               return false;
            }
         } else {
            return false;
         }
}
function getTafTemp($tafcode,$lang) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             $tafItemArray[$a]=str_replace("m","-",$tafItemArray[$a]);
             $tafItemArray[$a]=str_replace("M","-",$tafItemArray[$a]);
             if (preg_match("/T(-)*([0-9]{2})\/([0-9]{2})Z$/", $tafItemArray[$a])) {
                $tafItemArray[$a]=str_replace("T","",$tafItemArray[$a]);
                $tafItemArray[$a]=str_replace("Z","",$tafItemArray[$a]);
                $explodeItemArray = explode("/",$tafItemArray[$a]);
                $returner["temp"] = 1*$explodeItemArray[0];
                $returner["time"] = 1*$explodeItemArray[1];
                if (($lang=="en")||($lang=="EN")) {
                   $ampm="AM";
                   if ($returner["time"] > 12) {
                      $returner["time"] -= 12;
                      $ampm="PM";
                   }
                   $returner["time"] .= ":00 $ampm";
                } else {
                   $returner["time"] .= ":00";
                }
                $returner["time"] .= " GMT";
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getPressure($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^Q([0-9]{4})$/", $tafItemArray[$a])) {
                $returner = 1*substr($tafItemArray[$a],1,4);
                break;
             } else if (preg_match("/^A([0-9]{4})/", $tafItemArray[$a])) {
                $returner = round(0.3386389*substr($tafItemArray[$a],1,4));
                break;
             } else if (preg_match("/^QNH([0-9]{4})(INS)?$/", $tafItemArray[$a])) {
                $returner = round(0.3386389*substr($tafItemArray[$a],3,4));
                break;
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getVisibility($tafcode) {
         $type_Array=getTafType($tafcode);
         $tafItemArray=clearTAF($tafcode);
         for ($a=$type_Array["init"];$a<count($tafItemArray);$a++) {
             if (preg_match("/^((0([0-9]{2})0)|((1|2)([0-9]{1})(0|5)0)|(([3-9]{1})(0|5)00)|(9999))$/", $tafItemArray[$a])) {
                $returner=1*$tafItemArray[$a];
             } else if (preg_match("/^((P|M)?)([0-9]+)((\/([0-9]{1}))?)SM$/", $tafItemArray[$a])) {
                if (preg_match("/^P/", $tafItemArray[$a])) {
                   $returner = ">";
                   $tafItemArray[$a] = substr($tafItemArray[$a],1);
                } else if (preg_match("/^M/", $tafItemArray[$a])) {
                   $returner = "<";
                   $tafItemArray[$a] = substr($tafItemArray[$a],1);
                }
                $tafItemArray[$a] = str_replace("SM","",$tafItemArray[$a]);
                if (strpos($tafItemArray[$a],"/")) {
                   $explodeItemArray = explode("/",$tafItemArray[$a]);
                   $visi = $explodeItemArray[0] / $explodeItemArray[1];
                } else {
                   $visi = $tafItemArray[$a];
                }
                $returner .= 100*round(16.09344 * $visi);
                break;
             } else if (preg_match("/^([0-9]+)KM$/", $tafItemArray[$a])) {
                $returner=1000 * substr($tafItemArray[$a],0,(strlen($tafItemArray[$a])-2));
                break;
             } else if ($tafItemArray[$a] == "CAVOK") {
                $returner = "> 10";
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarRVR($tafcode,$number) {
         $tafItemArray=clearTAF($tafcode);
         $aktualRailway = 0;
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^R([0-9]{2})(L|C|R)?\/(M|P)?([0-9]{4})((V|VP|VM)([0-9]{4}))?(U|D|N)?$/", $tafItemArray[$a])) {
                if ($number==$aktualRailway++) {
                   $explodedItemArray=explode("/",$tafItemArray[$a]);
                   $returner["runway"] = substr($explodedItemArray[0],1);
                   $lastletter=substr($explodedItemArray[1],(strlen($explodedItemArray[1])-1));
                   if ($lastletter=="U") {
                      $returner["becomes"] = "higher";
                      $firstletters=substr($explodedItemArray[1],0,(strlen($explodedItemArray[1])-1));
                   } else if ($lastletter=="D") {
                      $returner["becomes"] = "lower";
                      $firstletters=substr($explodedItemArray[1],0,(strlen($explodedItemArray[1])-1));
                   } else if ($lastletter=="N") {
                      $returner["becomes"] = "steady";
                      $firstletters=substr($explodedItemArray[1],0,(strlen($explodedItemArray[1])-1));
                   } else {
                      $returner["becomes"] = "steady";
                      $firstletters=$explodedItemArray[1];
                   }
                   if (strpos($firstletters,"V")) {
                      $explodeLettersArray=explode("V",$firstletters);
                      if (substr($explodeLettersArray[0],0,1) == "M") {
                         $returner["less"] = "yes";
                         $returner["min"] = 1*substr($explodeLettersArray[0],1,4);
                      } else {
                         $returner["less"] = "no";
                         $returner["min"] = 1*$explodeLettersArray[0];
                      }
                      if (substr($explodeLettersArray[1],0,1) == "P") {
                         $returner["more"] = "yes";
                         $returner["max"] = 1*substr($explodeLettersArray[1],1,4);
                      } else {
                         $returner["more"] = "no";
                         $returner["max"] = 1*$explodeLettersArray[1];
                      }
                   } else {
                      if (substr($firstletters,0,1) == "M") {
                         $returner["less"] = "yes";
                         $returner["more"] = "no";
                         $returner["min"] = 1*substr($firstletters,1,4);
                         $returner["max"] = 1*substr($firstletters,1,4);
                      } else if (substr($explodeLettersArray[0],0,1) == "P") {
                         $returner["less"] = "no";
                         $returner["more"] = "yes";
                         $returner["min"] = 1*substr($firstletters,1,4);
                         $returner["max"] = 1*substr($firstletters,1,4);
                      } else {
                         $returner["less"] = "no";
                         $returner["more"] = "no";
                         $returner["min"] = 1*$firstletters;
                         $returner["max"] = 1*$firstletters;
                      }
                   }
                }
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getWeatherGrafic($tafcode,$icao) {
         $skyCond = getSky($tafcode,"en");
         switch ($skyCond) {
                case "sky clear":
                     $pic="skc";
                     break;
                case "few clouds":
                     $pic="few";
                     break;
                case "partly cloudy":
                     $pic="sct";
                     break;
                case "mostly cloudy":
                     $pic="bkn";
                     break;
                case "sky overcast":
                     $pic="ovc";
                     break;
                case "no significant clouds below 5,000 ft":
                     $pic="few";
                     break;
                default:
                     $pic="?";
         }
         if (getVisibility($tafcode) == 8888) $pic="few";
         $weatherCond = getConditions($tafcode,"en");
         if (preg_match("/(mist)|(fog)|(smoke)|(haze)|(dust)|(sand)|(ash)/", $weatherCond)) $pic = "dust";
         if (preg_match("/(drizzle)/", $weatherCond)) $pic = "drizzle";
         if (preg_match("/(rain)/", $weatherCond)) $pic = "rain";
         if (preg_match("/(light rain)/", $weatherCond)) $pic = "lightrain";
         if (preg_match("/(snow)|(ice)|(hail)/", $weatherCond)) $pic = "snow";
         if (preg_match("/(thunderstorm)/", $weatherCond)) $pic = "thunderstorm";
         if (preg_match("/(towering)/", $skyCond)) $pic = "thunderstorm";
         if (($sun = getSun($icao)) && ($station = getStationInfo($icao))) {
            $rise = $sun["risestamp"];
            $set = $sun["setstamp"];
            $now = time();
            $gmtDay = date("j");
            $localDay = date("j",($now+$station["gmttime"]*3600));
            if ($gmtDay < $localDay) {
               $now -= (24*3600);       // schon der n&auml;chste Tag!
            } else if ($gmtDay > $localDay) {
               $now += (24*3600);       // noch von gestern!
            }
            if ($now < ($rise-3600)) {
               $pre="night";
            } else if ($now < ($rise+3600)) {
               $pre="dawn";
            } else if ($now < ($set-3600)) {
               $pre="day";
            } else if ($now < ($set+3600)) {
               $pre="dawn";
            } else {
               $pre="night";
            }
         } else {
            $pre="day";
         }
         if ($pic=="?") {
            $graficfile = "fragezeichen.jpg";
         } else {
            $graficfile = $pre . "_" . $pic . ".jpg";
            if (strpos($graficfile,"thunderstorm")) $graficfile = str_replace(".jpg",".gif",$graficfile);
         }
         return $graficfile;
}
function getSky($tafcode) {
         $cloudlayer = 0;
         $upToNowSky = 0;
         while ($clouds = getClouds ($tafcode, $cloudlayer++)) {
               if ($clouds["cov1"] > $upToNowSky) {
                  $upToNowSky = $clouds["cov1"];
                  if (($clouds["cumulus"] == "yes") && ($cumulus != " towering cumulus clouds observed")) {
                     $cumulus = " with cumulus clouds";
                  }
                  if ($clouds["cumulus"] == "towering") $cumulus = " towering cumulus clouds observed";
               }
               $moreclouds = "yes";
         }
         switch ($upToNowSky) {
                case "0":
                     $returner = "sky clear";
                     break;
                case "1":
                     $returner = "few clouds";
                     break;
                case "3":
                     $returner = "partly cloudy";
                     break;
                case "5":
                     $returner = "mostly cloudy";
                     break;
                case "8":
                     $returner = "sky overcast";
                     break;
                case "9":
                     $returner = "no significant clouds below 5,000 ft";
                     break;
         }
         if ($moreclouds == "yes") {
            $returner = "$returner$cumulus";
         } else {
            $returner = false;
         }
         return $returner;
}
function getClouds($tafcode,$number) {
         $tafItemArray=clearTAF($tafcode);
         $aktualCloudBase = 0;
         for ($a=0;$a<count($tafItemArray);$a++) {
             $first3=substr($tafItemArray[$a],0,3);
             if (($first3=="NSC") || ($first3=="CLR") || ($first3=="SKC") || ($first3=="FEW") || ($first3=="SCT") || ($first3=="BKN") || ($first3=="OVC")) {
                if ($number==$aktualCloudBase++) {
                   switch ($first3) {
                          case "FEW":
                               $returner["cov1"] = "1";
                               $returner["cov2"] = "2";
                               $returner["sky"] = "few clouds at ";
                               break;
                          case "SCT":
                               $returner["cov1"] = "3";
                               $returner["cov2"] = "4";
                               $returner["sky"] = "partly cloudy at ";
                               break;
                          case "BKN":
                               $returner["cov1"] = "5";
                               $returner["cov2"] = "7";
                               $returner["sky"] = "mostly cloudy at ";
                               break;
                          case "OVC":
                               $returner["cov1"] = "8";
                               $returner["cov2"] = "8";
                               $returner["sky"] = "sky overcast at ";
                               break;
                   }
                   if (($first3 == "SKC") || ($first3 == "CLR")) {
                      $returner["cov1"] = "0";
                      $returner["cov2"] = "0";           // they hadn�t an option in switch
                      $returner["sky"] = "sky clear";    // oh my god think about that construction!
                      $returner["cumulus"] = "no";
                   } else if ($first3 == "NSC") {
                      $returner["cov1"] = "9";
                      $returner["cov2"] = "9";
                      $returner["sky"] = "no significant clouds below 5,000 ft\n";
                      $returner["cumulus"] = "no";
                   } else {                              // they already had an option in switch
                      $returner["feet"] = 100*substr($tafItemArray[$a],3,3);
                      if ($returner["feet"] < 3300) {
                         $returner["meters"] = 50*round($returner["feet"]/152.5);
                      } else if ($returner["feet"] < 33000) {
                         $returner["meters"] = 100*round($returner["feet"]/305);
                      } else {
                         $returner["meters"] = 500*round($returner["feet"]/1525);
                      }
                      if (strpos($tafItemArray[$a],"CB")) {
                         $returner["cumulus"] = "yes";
                      } else if (strpos($tafItemArray[$a],"TCU")) {
                         $returner["cumulus"] = "towering";
                      } else {
                         $returner["cumulus"] = "no";
                      }
                   }
                }
             }
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getConditions($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         for ($a=0;$a<count($tafItemArray);$a++) {
             if (preg_match("/^((\+)|(-))?((NSW)|(DZ)|(RA)|(SN)|(SQ)|(IC)|(PE)|(BR)|(FG)|(FU)|(HZ)|(VA)|(DU)|(SA)|(DS)|(FC)|(MI)|(BC)|(DR)|(BL)|(TS)|(FZ)|(VC)|(SS)|(SG)|(SH)|(GR)|(GS))*$/", $tafItemArray[$a]) && ($getConditions == "yes")) {
                if (preg_match("/^\+/", $tafItemArray[$a])) {
                   $tafItemArray[$a] = substr($tafItemArray[$a],1);
                   $returner = "heavy";
                } else if (preg_match("/^-/", $tafItemArray[$a])) {
                   $tafItemArray[$a] = substr($tafItemArray[$a],1);
                   $returner = "light";
                }
                for ($b=0; $b <= (strlen($tafItemArray[$a]) - 2); $b+=2) {
                    if (substr($tafItemArray[$a],$b,3) == "NSW") $returner .= "NSW";
                    if (substr($tafItemArray[$a],$b,2) == "DZ") $returner .= " drizzle";
                    if (substr($tafItemArray[$a],$b,2) == "RA") $returner .= " rain";
                    if (substr($tafItemArray[$a],$b,2) == "SN") $returner .= " snow";
                    if (substr($tafItemArray[$a],$b,2) == "SQ") $returner .= " squalls";
                    if (substr($tafItemArray[$a],$b,2) == "IC") $returner .= " diamond dust";
                    if (substr($tafItemArray[$a],$b,2) == "PE") $returner .= " ice pellets";
                    if (substr($tafItemArray[$a],$b,2) == "BR") $returner .= " mist";
                    if (substr($tafItemArray[$a],$b,2) == "FG") $returner .= " fog";
                    if (substr($tafItemArray[$a],$b,2) == "FU") $returner .= " smoke";
                    if (substr($tafItemArray[$a],$b,2) == "HZ") $returner .= " haze";
                    if (substr($tafItemArray[$a],$b,2) == "VA") $returner .= " volcanic ash";
                    if (substr($tafItemArray[$a],$b,2) == "DU") $returner .= " widespread dust";
                    if (substr($tafItemArray[$a],$b,2) == "SA") $returner .= " sand";
                    if (substr($tafItemArray[$a],$b,2) == "DS") $returner .= " dust storm";
                    if (substr($tafItemArray[$a],$b,2) == "FC") $returner .= " funnel cloud";
                    if (substr($tafItemArray[$a],$b,2) == "MI") $returner .= " shallow";
                    if (substr($tafItemArray[$a],$b,2) == "BC") $returner .= " patches";
                    if (substr($tafItemArray[$a],$b,2) == "DR") $returner .= " drifting";
                    if (substr($tafItemArray[$a],$b,2) == "BL") $returner .= " blowing";
                    if (substr($tafItemArray[$a],$b,2) == "TS") $returner .= " thunderstorm";
                    if (substr($tafItemArray[$a],$b,2) == "FZ") $returner .= " supercooled (freezing)";
                    if (substr($tafItemArray[$a],$b,2) == "VC") $returner .= " outside the airport";
                    if (substr($tafItemArray[$a],$b,2) == "SS") $returner .= " sandstorm";
                    if (substr($tafItemArray[$a],$b,2) == "SG") $returner .= " snow grains";
                    if (substr($tafItemArray[$a],$b,2) == "SH") $returner .= " showers";
                    if (substr($tafItemArray[$a],$b,2) == "GR") $returner .= " hail";
                    if (substr($tafItemArray[$a],$b,2) == "GS") $returner .= " small hail";
                }
             } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[$a]) || preg_match("/^BECMG$/", $tafItemArray[$a]) || preg_match("/^TEMPO$/", $tafItemArray[$a])) {
                $getConditions = "yes";
             } else if ($station=getStationInfo($tafItemArray[$a])) {
                $getConditions = "yes";
             }
         }
         $returner = str_replace("showers rain", "rain showers", $returner);
         $returner = str_replace("showers snow", "snow showers", $returner);
         $returner = str_replace("showers hail", "hail showers", $returner);
         $returner = str_replace("showers small hail", "showers of small hail", $returner);
         $returner = str_replace("patches fog", "waft of mist", $returner);
         $returner = str_replace("patches mist", "waft of mist", $returner);
         $returner = str_replace("patches smoke", "waft of smoke", $returner);
         $returner = str_replace("thunderstorm rain", "thunderstorm with rain", $returner);
         $returner = str_replace("thunderstorm hail", "thunderstorm with hail", $returner);
         $returner = str_replace("thunderstorm small hail", "thunderstorm with small hail", $returner);
         
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function translateEasyWeather($returner) {
         $returner = str_replace("more than 10 km, no clouds below 5000 ft, no precipitation",
                                 "mehr als 10 km, keine Wolken unterhalb 1.500 Meter, kein Niederschlag",$returner);
         $returner = str_replace("Actual weather","Aktuelles Wetter",$returner);
         $returner = str_replace("Forecast for","Vorhersage f&uuml;r den",$returner);
         $returner = str_replace("there is a probability of ","mit einer Wahrscheinlichkeit von ",$returner);
         $returner = str_replace(" that","",$returner);
         $returner = str_replace("between "," zwischen ",$returner);
         $returner = str_replace("for:","",$returner);
         $returner = str_replace("weather will turn into","&Uuml;bergang zu",$returner);
         $returner = str_replace("weather will temporarily change into","zeitweise",$returner);
         $returner = str_replace(" for "," f&uuml;r ",$returner);
         $returner = str_replace("at ","um ",$returner);
         $returner = str_replace("weather change into","Wetter&auml;nderung zu",$returner);
         $returner = str_replace("not reported","k/A",$returner);
         $returner = str_replace("no significant weather","keine besondere Wettererscheinung",$returner);
         $returner = str_replace("Timezone","Zeitzone",$returner);
         $returner = str_replace("time of issue","herausgegeben",$returner);
         $returner = str_replace("Winds from","Wind aus",$returner);
         $returner = str_replace("Wind direction changing","Wind aus wechselnden Richtungen",$returner);
         $returner = str_replace("from","vom",$returner);
         $returner = str_replace("temporarily","zeitweise",$returner);
         $returner = str_replace("no changes","keine &Auml;nderung",$returner);
         $returner = str_replace("2 hours trend","2-Stunden Trend",$returner);
         $returner = str_replace("will breakup","nachlassend",$returner);
         $returner = str_replace(" NNE "," NNO ",$returner);
         $returner = str_replace(" NE "," NO ",$returner);
         $returner = str_replace(" ENE "," ENO ",$returner);
         $returner = str_replace(" E "," O ",$returner);
         $returner = str_replace(" ESE "," OSO ",$returner);
         $returner = str_replace(" SE "," SO ",$returner);
         $returner = str_replace(" SSE "," SSO ",$returner);
         $returner = str_replace("force","St&auml;rke",$returner);
         $returner = str_replace("beaufort","Beaufort",$returner);
         $returner = str_replace("with gusts up to","mit B&ouml;en bis zu",$returner);
         $returner = str_replace("knots","Knoten",$returner);
         $returner = str_replace("more than","mehr als",$returner);
         $returner = str_replace("less than","weniger als",$returner);
         $returner = str_replace("meters","Meter",$returner);
         $returner = str_replace("Temperature","Temperatur",$returner);
         $returner = str_replace("Dewpoint","Taupunkt",$returner);
         $returner = str_replace("Humidity","Luftfeuchtigkeit",$returner);
         $returner = str_replace("Air pressure","Luftdruck",$returner);
         $returner = str_replace("Visibility","Sicht",$returner);
         $returner = str_replace("meters","Meter",$returner);
         return $returner;
}
function displayEasyWeather($METAR,$lang) {
         $metarParts = getMetarParts($METAR);
         for ($a=0;$a<count($metarParts);$a++) {
             $weatherType = getMetarType($metarParts[$a]);
             switch ($weatherType["type"]) {
                    case "main":
                         if ($station=getStationInfo(getICAO($metarParts[$a]))) {
                            $returner .= "<b>" . $station["station"]."</b><br>";
                         }
                         $returner .= "<b>Actual weather ";
                         if ($issuetime = getIssueTime ($metarParts[$a], $lang)) {
                            $returner .= "from $issuetime";
                         }
                         $returner .= ":</b><br>\n";
                    break;
                    case "becmg":
                         $returner .= "<b>2 hours trend:</b>\n";
                    break;
                    case "tempo":
                         $returner .= "<b>2 hours trend:</b> temporarily</b>\n";
                    break;
                    case "nosig":
                         $returner .= "<b>2 hours trend:</b> no changes<br>\n";
                    break;
             }
             if ($wind = getWind($metarParts[$a])) {
                if ($wind["dir"]=="variable") {
                   $returner .= "Wind direction changing ";
                } else {
                   $returner .= "Winds from ".$wind["dir"]." (".$wind["deg"]."�) ";
                }
                $returner .= "force ".$wind["wbf"]." beaufort (";
                $returner .= $wind["wkt"]." knots)";
                if ($wind["gkt"] > $wind["wkt"]) {
                   $returner .= " with gusts up to ".$wind["gbf"];
                   $returner .= " beaufort (".$wind["gkt"]." knots)";
                }
                $returner .= ",\n";
             }
             if ($temp = getMetarTemp($metarParts[$a])) {
                $returner .= "Temperature: $temp&#176;C,\n";
             }
             if ($dew = getMetarDewpoint($metarParts[$a])) {
                $returner .= "Dewpoint: $dew&#176;C,\n";
             }
             if ($hum = getMetarHumidity($metarParts[$a])) {
                $returner .= "Humidity: $hum %,\n";
             }
             if ($press = getPressure($metarParts[$a])) {
                $returner .= "Air pressure: $press hPa,\n";
             }
             if ($vis = getVisibility($metarParts[$a])) {
                $returner .= "Visibility: ";
                if ($vis == 9999) {
                   $returner .= "more than 10 km,\n";
                } else if ($vis == 8888) {
                   $returner .= "more than 10 km, no clouds below 5000 ft, no precipitation\n";
                } else if ($vis < 10000) {
                   $returner .= "$vis meters,\n";
                } else if ($vis >= 10000) {
                   $returner .= round($vis/1000) . " km,\n";
                }
             }
             if ($sky = getSky($metarParts[$a],$lang)) {
                $returner .= "$sky,\n";
             }
             if ($cond = getConditions($metarParts[$a],$lang)) {
                if ($cond == "nsw") {
                   if ($weatherType["type"]=="main") {
                      $returner .= "no significant weather\n";
                      $oldcond = "no significant weather\n";     // if this appears the meteorologist
                   } else {                                      // must be stupid: NSW will breakup ROFL
                      $returner .= "$oldcond will breakup\n";
                   }
                } else {
                   $returner .= "$cond\n";
                   $oldcond = $cond;
                   $oldcond = str_replace("light ","",$oldcond);
                   $oldcond = str_replace("heavy ","",$oldcond);
                   $oldcond = str_replace("vereinzelt ","",$oldcond);
                   $oldcond = str_replace("verst&auml;rkt ","",$oldcond);
                }
             }
             $returner .= "<br>\n";
         }
         if (($lang == "DE") || ($lang == "de")) $returner=translateEasyWeather($returner);
         return $returner;
}
function displayEasyForecast($TAF,$lang) {
         $tafParts = getTafParts($TAF);
         for ($a=0;$a<count($tafParts);$a++) {
             $forecast = getTafType($tafParts[$a]);
             switch ($forecast["type"]) {
                    case "main":
                         if ($station=getStationInfo(getICAO($tafParts[$a]))) {
                            $returner .= "<b>" . $station["station"]."</b><br>";
                         }
                         $returner .= "<b>Forecast ";
                         if ($validtime = getValidTime ($tafParts[$a], $lang)) {
                            $returner .= "for $validtime";
                         }
                         $returner .= ":</b><br>\n";
                    break;
                    case "prob":
                         $returner .= "<b>there is a probability of ".$forecast["chance"]." %\n";
                         if ($forecast["init"] == 2) {
                            $returner .= "between " . getShortValidTime($tafParts[$a],$lang)."\n";
                         }
                         $returner .= "for:</b><br>\n";
                    break;
                    case "becmg":
                         $returner .= "<b>";
                         if ($forecast["chance"] != 100) {
                            $returner .= "there is a probability of ".$forecast["chance"]." % that\n";
                            if ($forecast["init"] == 3) {
                               $returner .= "between " . getShortValidTime($tafParts[$a],$lang)."\n";
                            }
                         } else {
                            if ($forecast["init"] == 2) {
                               $returner .= "between " . getShortValidTime($tafParts[$a],$lang)."\n";
                            }
                         }
                         $returner .= "weather will turn into:</b><br>\n";
                    break;
                    case "tempo":
                         $returner .= "<b>";
                         if ($forecast["chance"] != 100) {
                            $returner .= "there is a probability of ".$forecast["chance"]." % that\n";
                            if ($forecast["init"] == 3) {
                               $returner .= "between " . getShortValidTime($tafParts[$a],$lang)."\n";
                            }
                         } else {
                            if ($forecast["init"] == 2) {
                               $returner .= "between " . getShortValidTime($tafParts[$a],$lang)."\n";
                            }
                         }
                         $returner .= "weather will temporarily change into:</b><br>\n";
                    break;
                    case "amfm":
                         $returner .= "<b>";
                         if ($forecast["chance"] != 100) {
                            $returner .= "there is a probability of ".$forecast["chance"]." % that\n";
                         }
                         $returner .= "at " . getFmTime($tafParts[$a], $lang) ."\n";
                         $returner .= "weather change into:</b><br>\n";
                    break;
             }
             if ($wind = getWind($tafParts[$a])) {
                if ($wind["dir"]=="variable") {
                   $returner .= "Wind direction changing ";
                } else {
                   $returner .= "Winds from ".$wind["dir"]." (".$wind["deg"]."�) ";
                }
                $returner .= "force ".$wind["wbf"]." beaufort (";
                $returner .= $wind["wkt"]." knots)";
                if ($wind["gkt"] > $wind["wkt"]) {
                   $returner .= " with gusts up to ".$wind["gbf"];
                   $returner .= " beaufort (".$wind["gkt"]." knots)";
                }
                $returner .= ",\n";
             }
             if ($temp = getTafTemp($tafParts[$a],$lang)) {
                $returner .= "Temperature: " . $temp["temp"] . "&#176;C at " . $temp["time"] . ",\n";
             }
             if ($dew = getMetarDewpoint($tafParts[$a])) {
                $returner .= "Dewpoint: $dew&#176;C,\n";
             }
             if ($hum = getMetarHumidity($tafParts[$a])) {
                $returner .= "Humidity: $hum %,\n";
             }
             if ($press = getPressure($tafParts[$a])) {
                $returner .= "Air pressure: $press hPa,\n";
             }
             if ($vis = getVisibility($tafParts[$a])) {
                $returner .= "Visibility: ";
                if ($vis == 9999) {
                   $returner .= "more than 10 km,\n";
                } else if ($vis == 8888) {
                   $returner .= "more than 10 km, no clouds below 5000 ft, no precipitation\n";
                } else if ($vis < 10000) {
                   $returner .= "$vis meters,\n";
                } else if ($vis >= 10000) {
                   $returner .= round($vis/1000) . " km,\n";
                }
             }
             if ($sky = getSky($tafParts[$a],$lang)) {
                $returner .= "$sky,\n";
             }
             if ($cond = getConditions($tafParts[$a],$lang)) {
                if ($cond == "nsw") {
                   if ($weatherType["type"]=="main") {
                      $returner .= "no significant weather\n";
                      $oldcond = "no significant weather\n";     // if this appears the meteorologist
                   } else {                                      // must be stupid: NSW will breakup ROFL
                      $returner .= "$oldcond will breakup\n";
                   }
                } else {
                   $returner .= "$cond\n";
                   $oldcond = $cond;
                   $oldcond = str_replace("light ","",$oldcond);
                   $oldcond = str_replace("heavy ","",$oldcond);
                   $oldcond = str_replace("vereinzelt ","",$oldcond);
                   $oldcond = str_replace("verst&auml;rkt ","",$oldcond);
                }
             }
             $returner .= "<br>\n";
         }
         if (($lang == "DE") || ($lang == "de")) $returner=translateEasyWeather($returner);
         return $returner;
}
function displayCountryMenu($name,$class) {
         global $$name;
         $predef = $$name;
         $urlParts = explode("/",$_SERVER['SCRIPT_NAME']);
         $filename = $urlParts[(count($urlParts)-1)];
         $returner .= "<select name=\"$name\" size=\"1\" class=\"$class\" ";
         $returner .= "onChange=\"self.location='$filename?$name=' + this.options[this.selectedIndex].value; return true;\">\n";
         $foo = getCountries();
         for ($bar=0;$bar<count($foo);$bar++) {
             $landArray = explode(";",$foo[$bar]);
             $returner .= "        <option value=\"".$landArray[0]."\"";
             if ($predef == $landArray[0]) $returner .= " selected";
             $returner .= ">".trim($landArray[1])."</option>\n";
         }
         $returner .= "</select>\n";
         return $returner;
}
function displayStationMenu($state,$name,$class) {
         global $$name;
         $predef = $$name;
         $returner .= "<select name='$name' size='1' class='$class'>\n";
         $foo = getStations ($state);;
         for ($bar=0;$bar<count($foo);$bar++) {
             $stationArray = explode(";",$foo[$bar]);
             $returner .= "        <option value=\"".$stationArray[0]."\"";
             if ($predef == $stationArray[0]) $returner .= " selected";
             $returner .= ">".trim($stationArray[1])."</option>\n";
         }
         $returner .= "</select>\n";
         return $returner;
}
function getTafParts($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         $lastTafindex = 0;
         $aktTafIndex  = 0;
         for ($a=0;$a<count($tafItemArray);$a++) {
             if ((($tafItemArray[$a]=="TEMPO") || ($tafItemArray[$a]=="BECMG")
             || preg_match("/^PROB([0-9]{2})$/", $tafItemArray[$a]) || preg_match("/^FM([0-9]{4})$/",$tafItemArray[$a]))
             && ($a > ($lastTafindex+2))) {
                for ($b=$lastTafindex;$b<$a;$b++) {
                    $returner[$aktTafIndex] .= " ".$tafItemArray[$b];
                }
                $returner[$aktTafIndex] = trim($returner[$aktTafIndex]);
                $lastTafindex = $a;
                $aktTafIndex++;
             }
         }
         for ($b=$lastTafindex;$b<$a;$b++) {
             $returner[$aktTafIndex] .= " ".$tafItemArray[$b];
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getMetarParts($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         $lastTafindex = 0;
         $aktTafIndex  = 0;
         for ($a=0;$a<count($tafItemArray);$a++) {
             if ((($tafItemArray[$a]=="TEMPO") || ($tafItemArray[$a]=="BECMG") || ($tafItemArray[$a]=="NOSIG"))
             && ($a > ($lastTafindex+2))) {
                for ($b=$lastTafindex;$b<$a;$b++) {
                    $returner[$aktTafIndex] .= " ".$tafItemArray[$b];
                }
                $returner[$aktTafIndex] = trim($returner[$aktTafIndex]);
                $lastTafindex = $a;
                $aktTafIndex++;
             }
         }
         for ($b=$lastTafindex;$b<$a;$b++) {
             $returner[$aktTafIndex] .= " ".$tafItemArray[$b];
         }
         if (isset($returner)) {
            return $returner;
         } else {
            return false;
         }
}
function getTafType($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         if (preg_match("/^FM([0-9]{4})$/", $tafItemArray[0])) {
            $returner["chance"] = 100;
            $returner["type"]   = "amfm";
            $returner["init"]   = 1;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && preg_match("/^FM([0-9]{4})$/", $tafItemArray[1])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "amfm";
            $returner["init"]   = 2;
         } else if (($tafItemArray[0] == "BECMG") && !preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 100;
            $returner["type"]   = "becmg";
            $returner["init"]   = 1;
         } else if (($tafItemArray[0] == "BECMG") && preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 100;
            $returner["type"]   = "becmg";
            $returner["init"]   = 2;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && ($tafItemArray[1] == "BECMG")
           && !preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[2])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "becmg";
            $returner["init"]   = 2;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && ($tafItemArray[1] == "BECMG")
           && preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[2])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "becmg";
            $returner["init"]   = 3;
         } else if (($tafItemArray[0] == "TEMPO") && !preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 100;
            $returner["type"]   = "tempo";
            $returner["init"]   = 1;
         } else if (($tafItemArray[0] == "TEMPO") && preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 100;
            $returner["type"]   = "tempo";
            $returner["init"]   = 2;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && ($tafItemArray[1] == "TEMPO")
           && !preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[2])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "tempo";
            $returner["init"]   = 2;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && ($tafItemArray[1] == "TEMPO")
           && preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[2])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "tempo";
            $returner["init"]   = 3;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && ($tafItemArray[1] != "BECMG")
            && ($tafItemArray[1] != "TEMPO") && !preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "prob";
            $returner["init"]   = 1;
         } else if (preg_match("/^PROB([0-9]{2})$/", $tafItemArray[0]) && preg_match("/^(((([01]{1})([0-9]{1}))|(2([0-4]{1}))){2})$/", $tafItemArray[1])) {
            $returner["chance"] = 1 * substr($tafItemArray[0],4,2);
            $returner["type"]   = "prob";
            $returner["init"]   = 2;
         } else {
            $returner["chance"] = 100;
            $returner["type"]   = "main";
            $returner["init"]   = 0;
         }
         return $returner;
}
function getMetarType($tafcode) {
         $tafItemArray=clearTAF($tafcode);
         if ($tafItemArray[0]=="BECMG") {
            $returner["chance"] = 100;
            $returner["type"] = "becmg";
            $returner["init"]   = 1;
         } else if ($tafItemArray[0]=="TEMPO") {
            $returner["chance"] = 100;
            $returner["type"] = "tempo";
            $returner["init"]   = 1;
         } else if ($tafItemArray[0]=="NOSIG") {
            $returner["chance"] = 100;
            $returner["type"] = "nosig";
            $returner["init"]   = 1;
         } else {
            $returner["chance"] = 100;
            $returner["type"]   = "main";
            $returner["init"]   = 0;
         }
         return $returner;
}
?>
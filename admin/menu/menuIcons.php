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
	#	Menu icons
	#
	############################################################################

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			.icon{
				font-size:3.0em;
			}
			.iconDiv{
				display: inline-block;
				padding: 10px;
				margin: 0 auto;
				text-align: center;
				width: 150px;
			}
			h3{
				text-align: center;
				padding-bottom:25px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%">
				<h1>Menu - Icons</h1>
				<h2>Meteotemplate Font</h2>
				<div class="iconDiv">
					<div class="icon mticon-1h"></div>1h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-2weeks"></div>2weeks
				</div>
				<div class="iconDiv">
					<div class="icon mticon-3days"></div>3days
				</div>
				<div class="iconDiv">
					<div class="icon mticon-3h"></div>3h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-3months"></div>3months
				</div>
				<div class="iconDiv">
					<div class="icon mticon-3paramgraph"></div>3paramgraph
				</div>
				<div class="iconDiv">
					<div class="icon mticon-6h"></div>6h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-6m"></div>6m
				</div>
				<div class="iconDiv">
					<div class="icon mticon-10days"></div>10days
				</div>
				<div class="iconDiv">
					<div class="icon mticon-12h"></div>12h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-12m"></div>12m
				</div>
				<div class="iconDiv">
					<div class="icon mticon-24h"></div>24h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-48h"></div>48h
				</div>
				<div class="iconDiv">
					<div class="icon mticon-acuritevn1black"></div>acuritevn1black
				</div>
				<div class="iconDiv">
					<div class="icon mticon-addblock"></div>addblock
				</div>
				<div class="iconDiv">
					<div class="icon mticon-allmonths"></div>allmonths
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ambient1001black"></div>ambient1001black
				</div>
				<div class="iconDiv">
					<div class="icon mticon-antipode"></div>antipode
				</div>
				<div class="iconDiv">
					<div class="icon mticon-apparent"></div>apparent
				</div>
				<div class="iconDiv">
					<div class="icon mticon-applogo"></div>applogo
				</div>
				<div class="iconDiv">
					<div class="icon mticon-areachart"></div>areachart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-areaspline"></div>areaspline
				</div>
				<div class="iconDiv">
					<div class="icon mticon-asteroids"></div>asteroids
				</div>
				<div class="iconDiv">
					<div class="icon mticon-autumn"></div>autumn
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ban"></div>ban
				</div>
				<div class="iconDiv">
					<div class="icon mticon-barchart"></div>barchart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-barchart-1"></div>barchart-1
				</div>
				<div class="iconDiv">
					<div class="icon mticon-barotrend"></div>barotrend
				</div>
				<div class="iconDiv">
					<div class="icon mticon-bearing"></div>bearing
				</div>
				<div class="iconDiv">
					<div class="icon mticon-bioindexes"></div>bioindexes
				</div>
				<div class="iconDiv">
					<div class="icon mticon-blocks"></div>blocks
				</div>
				<div class="iconDiv">
					<div class="icon mticon-buoy"></div>buoy
				</div>
				<div class="iconDiv">
					<div class="icon mticon-calendar-day"></div>calendar-day
				</div>
				<div class="iconDiv">
					<div class="icon mticon-calendar-month"></div>calendar-month
				</div>
				<div class="iconDiv">
					<div class="icon mticon-calendar-year"></div>calendar-year
				</div>
				<div class="iconDiv">
					<div class="icon mticon-camera"></div>camera
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cdd"></div>cdd
				</div>
				<div class="iconDiv">
					<div class="icon mticon-chart"></div>chart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cloudbase"></div>cloudbase
				</div>
				<div class="iconDiv">
					<div class="icon mticon-clouds"></div>clouds
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cloudtemp"></div>cloudtemp
				</div>
				<div class="iconDiv">
					<div class="icon mticon-co2"></div>co2
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cold"></div>cold
				</div>
				<div class="iconDiv">
					<div class="icon mticon-compare"></div>compare
				</div>
				<div class="iconDiv">
					<div class="icon mticon-comparisongraph"></div>comparisongraph
				</div>
				<div class="iconDiv">
					<div class="icon mticon-continentality"></div>continentality
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cumulative"></div>cumulative
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cumulus"></div>cumulus
				</div>
				<div class="iconDiv">
					<div class="icon mticon-cyclone"></div>cyclone
				</div>
				<div class="iconDiv">
					<div class="icon mticon-da"></div>da
				</div>
				<div class="iconDiv">
					<div class="icon mticon-datacheck"></div>datacheck
				</div>
				<div class="iconDiv">
					<div class="icon mticon-davispro2"></div>davispro2
				</div>
				<div class="iconDiv">
					<div class="icon mticon-davisvueblack"></div>davisvueblack
				</div>
				<div class="iconDiv">
					<div class="icon mticon-day"></div>day
				</div>
				<div class="iconDiv">
					<div class="icon mticon-daylength"></div>daylength
				</div>
				<div class="iconDiv">
					<div class="icon mticon-daynight"></div>daynight
				</div>
				<div class="iconDiv">
					<div class="icon mticon-deviation"></div>deviation
				</div>
				<div class="iconDiv">
					<div class="icon mticon-dewpoint"></div>dewpoint
				</div>
				<div class="iconDiv">
					<div class="icon mticon-dictionary"></div>dictionary
				</div>
				<div class="iconDiv">
					<div class="icon mticon-disasters"></div>disasters
				</div>
				<div class="iconDiv">
					<div class="icon mticon-distance"></div>distance
				</div>
				<div class="iconDiv">
					<div class="icon mticon-doppler"></div>doppler
				</div>
				<div class="iconDiv">
					<div class="icon mticon-e"></div>e
				</div>
				<div class="iconDiv">
					<div class="icon mticon-earthquake"></div>earthquake
				</div>
				<div class="iconDiv">
					<div class="icon mticon-elevation"></div>elevation
				</div>
				<div class="iconDiv">
					<div class="icon mticon-elnino"></div>elnino
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ene"></div>ene
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ese"></div>ese
				</div>
				<div class="iconDiv">
					<div class="icon mticon-evapotranspiration"></div>evapotranspiration
				</div>
				<div class="iconDiv">
					<div class="icon mticon-extremes"></div>extremes
				</div>
				<div class="iconDiv">
					<div class="icon mticon-fire"></div>fire
				</div>
				<div class="iconDiv">
					<div class="icon mticon-fog"></div>fog
				</div>
				<div class="iconDiv">
					<div class="icon mticon-fog-1"></div>fog-1
				</div>
				<div class="iconDiv">
					<div class="icon mticon-footprint"></div>footprint
				</div>
				<div class="iconDiv">
					<div class="icon mticon-forecast"></div>forecast
				</div>
				<div class="iconDiv">
					<div class="icon mticon-fossil"></div>fossil
				</div>
				<div class="iconDiv">
					<div class="icon mticon-fullmoon"></div>fullmoon
				</div>
				<div class="iconDiv">
					<div class="icon mticon-future"></div>future
				</div>
				<div class="iconDiv">
					<div class="icon mticon-gauges"></div>gauges
				</div>
				<div class="iconDiv">
					<div class="icon mticon-gdd"></div>gdd
				</div>
				<div class="iconDiv">
					<div class="icon mticon-gdp"></div>gdp
				</div>
				<div class="iconDiv">
					<div class="icon mticon-globe"></div>globe
				</div>
				<div class="iconDiv">
					<div class="icon mticon-gps"></div>gps
				</div>
				<div class="iconDiv">
					<div class="icon mticon-graph"></div>graph
				</div>
				<div class="iconDiv">
					<div class="icon mticon-grlevelx"></div>grlevelx
				</div>
				<div class="iconDiv">
					<div class="icon mticon-gust"></div>gust
				</div>
				<div class="iconDiv">
					<div class="icon mticon-hail"></div>hail
				</div>
				<div class="iconDiv">
					<div class="icon mticon-halfmoon"></div>halfmoon
				</div>
				<div class="iconDiv">
					<div class="icon mticon-hdd"></div>hdd
				</div>
				<div class="iconDiv">
					<div class="icon mticon-hightide"></div>hightide
				</div>
				<div class="iconDiv">
					<div class="icon mticon-history"></div>history
				</div>
				<div class="iconDiv">
					<div class="icon mticon-hot"></div>hot
				</div>
				<div class="iconDiv">
					<div class="icon mticon-humidity"></div>humidity
				</div>
				<div class="iconDiv">
					<div class="icon mticon-humidity-range"></div>humidity-range
				</div>
				<div class="iconDiv">
					<div class="icon mticon-hurricane"></div>hurricane
				</div>
				<div class="iconDiv">
					<div class="icon mticon-import"></div>import
				</div>
				<div class="iconDiv">
					<div class="icon mticon-indoor"></div>indoor
				</div>
				<div class="iconDiv">
					<div class="icon mticon-indoorhumidity"></div>indoorhumidity
				</div>
				<div class="iconDiv">
					<div class="icon mticon-indoortemp"></div>indoortemp
				</div>
				<div class="iconDiv">
					<div class="icon mticon-irsat"></div>irsat
				</div>
				<div class="iconDiv">
					<div class="icon mticon-iss"></div>iss
				</div>
				<div class="iconDiv">
					<div class="icon mticon-labels"></div>labels
				</div>
				<div class="iconDiv">
					<div class="icon mticon-land"></div>land
				</div>
				<div class="iconDiv">
					<div class="icon mticon-lat"></div>lat
				</div>
				<div class="iconDiv">
					<div class="icon mticon-layout"></div>layout
				</div>
				<div class="iconDiv">
					<div class="icon mticon-leafwetness"></div>leafwetness
				</div>
				<div class="iconDiv">
					<div class="icon mticon-linechart"></div>linechart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-logo"></div>logo
				</div>
				<div class="iconDiv">
					<div class="icon mticon-lon"></div>lon
				</div>
				<div class="iconDiv">
					<div class="icon mticon-longterm"></div>longterm
				</div>
				<div class="iconDiv">
					<div class="icon mticon-lowtide"></div>lowtide
				</div>
				<div class="iconDiv">
					<div class="icon mticon-lunar"></div>lunar
				</div>
				<div class="iconDiv">
					<div class="icon mticon-map"></div>map
				</div>
				<div class="iconDiv">
					<div class="icon mticon-mars"></div>mars
				</div>
				<div class="iconDiv">
					<div class="icon mticon-meteor"></div>meteor
				</div>
				<div class="iconDiv">
					<div class="icon mticon-month"></div>month
				</div>
				<div class="iconDiv">
					<div class="icon mticon-moon"></div>moon
				</div>
				<div class="iconDiv">
					<div class="icon mticon-moonrise"></div>moonrise
				</div>
				<div class="iconDiv">
					<div class="icon mticon-moonset"></div>moonset
				</div>
				<div class="iconDiv">
					<div class="icon mticon-moontransit"></div>moontransit
				</div>
				<div class="iconDiv">
					<div class="icon mticon-mysql"></div>mysql
				</div>
				<div class="iconDiv">
					<div class="icon mticon-n"></div>n
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ne"></div>ne
				</div>
				<div class="iconDiv">
					<div class="icon mticon-neo"></div>neo
				</div>
				<div class="iconDiv">
					<div class="icon mticon-netatmoblack"></div>netatmoblack
				</div>
				<div class="iconDiv">
					<div class="icon mticon-newmoon"></div>newmoon
				</div>
				<div class="iconDiv">
					<div class="icon mticon-night"></div>night
				</div>
				<div class="iconDiv">
					<div class="icon mticon-nightsky"></div>nightsky
				</div>
				<div class="iconDiv">
					<div class="icon mticon-nne"></div>nne
				</div>
				<div class="iconDiv">
					<div class="icon mticon-nnw"></div>nnw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-no2"></div>no2
				</div>
				<div class="iconDiv">
					<div class="icon mticon-nox"></div>nox
				</div>
				<div class="iconDiv">
					<div class="icon mticon-nw"></div>nw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-o3"></div>o3
				</div>
				<div class="iconDiv">
					<div class="icon mticon-outlook"></div>outlook
				</div>
				<div class="iconDiv">
					<div class="icon mticon-overcast"></div>overcast
				</div>
				<div class="iconDiv">
					<div class="icon mticon-partlycloudy"></div>partlycloudy
				</div>
				<div class="iconDiv">
					<div class="icon mticon-plane"></div>plane
				</div>
				<div class="iconDiv">
					<div class="icon mticon-plugin"></div>plugin
				</div>
				<div class="iconDiv">
					<div class="icon mticon-pm10"></div>pm10
				</div>
				<div class="iconDiv">
					<div class="icon mticon-pressure"></div>pressure
				</div>
				<div class="iconDiv">
					<div class="icon mticon-pressureconversion"></div>pressureconversion
				</div>
				<div class="iconDiv">
					<div class="icon mticon-qr"></div>qr
				</div>
				<div class="iconDiv">
					<div class="icon mticon-radials"></div>radials
				</div>
				<div class="iconDiv">
					<div class="icon mticon-rain"></div>rain
				</div>
				<div class="iconDiv">
					<div class="icon mticon-rain-range"></div>rain-range
				</div>
				<div class="iconDiv">
					<div class="icon mticon-rainsnow"></div>rainsnow
				</div>
				<div class="iconDiv">
					<div class="icon mticon-raspberryblack"></div>raspberryblack
				</div>
				<div class="iconDiv">
					<div class="icon mticon-reformat"></div>reformat
				</div>
				<div class="iconDiv">
					<div class="icon mticon-s"></div>s
				</div>
				<div class="iconDiv">
					<div class="icon mticon-satellite"></div>satellite
				</div>
				<div class="iconDiv">
					<div class="icon mticon-scale"></div>scale
				</div>
				<div class="iconDiv">
					<div class="icon mticon-scatterchart"></div>scatterchart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-se"></div>se
				</div>
				<div class="iconDiv">
					<div class="icon mticon-shortterm"></div>shortterm
				</div>
				<div class="iconDiv">
					<div class="icon mticon-snow"></div>snow
				</div>
				<div class="iconDiv">
					<div class="icon mticon-snowdepth"></div>snowdepth
				</div>
				<div class="iconDiv">
					<div class="icon mticon-snowfall"></div>snowfall
				</div>
				<div class="iconDiv">
					<div class="icon mticon-so2"></div>so2
				</div>
				<div class="iconDiv">
					<div class="icon mticon-soilmoisture"></div>soilmoisture
				</div>
				<div class="iconDiv">
					<div class="icon mticon-soiltemperature"></div>soiltemperature
				</div>
				<div class="iconDiv">
					<div class="icon mticon-solar"></div>solar
				</div>
				<div class="iconDiv">
					<div class="icon mticon-solarsystem"></div>solarsystem
				</div>
				<div class="iconDiv">
					<div class="icon mticon-solarwind"></div>solarwind
				</div>
				<div class="iconDiv">
					<div class="icon mticon-splinechart"></div>splinechart
				</div>
				<div class="iconDiv">
					<div class="icon mticon-spring"></div>spring
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sse"></div>sse
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ssw"></div>ssw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-station"></div>station
				</div>
				<div class="iconDiv">
					<div class="icon mticon-stationaverages"></div>stationaverages
				</div>
				<div class="iconDiv">
					<div class="icon mticon-storm"></div>storm
				</div>
				<div class="iconDiv">
					<div class="icon mticon-storm-1"></div>storm-1
				</div>
				<div class="iconDiv">
					<div class="icon mticon-summary"></div>summary
				</div>
				<div class="iconDiv">
					<div class="icon mticon-summer"></div>summer
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sun"></div>sun
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sunlight-ratio"></div>sunlight-ratio
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sunrise"></div>sunrise
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sunset"></div>sunset
				</div>
				<div class="iconDiv">
					<div class="icon mticon-sw"></div>sw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-table"></div>table
				</div>
				<div class="iconDiv">
					<div class="icon mticon-temp"></div>temp
				</div>
				<div class="iconDiv">
					<div class="icon mticon-temp-range"></div>temp-range
				</div>
				<div class="iconDiv">
					<div class="icon mticon-terminology"></div>terminology
				</div>
				<div class="iconDiv">
					<div class="icon mticon-tide"></div>tide
				</div>
				<div class="iconDiv">
					<div class="icon mticon-time"></div>time
				</div>
				<div class="iconDiv">
					<div class="icon mticon-tornado"></div>tornado
				</div>
				<div class="iconDiv">
					<div class="icon mticon-translation"></div>translation
				</div>
				<div class="iconDiv">
					<div class="icon mticon-trenddown"></div>trenddown
				</div>
				<div class="iconDiv">
					<div class="icon mticon-trendneutral"></div>trendneutral
				</div>
				<div class="iconDiv">
					<div class="icon mticon-trendup"></div>trendup
				</div>
				<div class="iconDiv">
					<div class="icon mticon-updateblock"></div>updateblock
				</div>
				<div class="iconDiv">
					<div class="icon mticon-updateoptions"></div>updateoptions
				</div>
				<div class="iconDiv">
					<div class="icon mticon-uv"></div>uv
				</div>
				<div class="iconDiv">
					<div class="icon mticon-visibility"></div>visibility
				</div>
				<div class="iconDiv">
					<div class="icon mticon-volcano"></div>volcano
				</div>
				<div class="iconDiv">
					<div class="icon mticon-w"></div>w
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningdrought"></div>warningdrought
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningflood"></div>warningflood
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warninggeneral"></div>warninggeneral
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warninghight"></div>warninghight
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warninghurricane"></div>warninghurricane
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningice"></div>warningice
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warninglowt"></div>warninglowt
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningrain"></div>warningrain
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningsnow"></div>warningsnow
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningthunderstorm"></div>warningthunderstorm
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningtornado"></div>warningtornado
				</div>
				<div class="iconDiv">
					<div class="icon mticon-warningwind"></div>warningwind
				</div>
				<div class="iconDiv">
					<div class="icon mticon-watervapor"></div>watervapor
				</div>
				<div class="iconDiv">
					<div class="icon mticon-weather"></div>weather
				</div>
				<div class="iconDiv">
					<div class="icon mticon-weathercat"></div>weathercat
				</div>
				<div class="iconDiv">
					<div class="icon mticon-webcam"></div>webcam
				</div>
				<div class="iconDiv">
					<div class="icon mticon-week"></div>week
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wetdays"></div>wetdays
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wind"></div>wind
				</div>
				<div class="iconDiv">
					<div class="icon mticon-winddirection"></div>winddirection
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wmr90ablack"></div>wmr90ablack
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wmr200black"></div>wmr200black
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wnw"></div>wnw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-world"></div>world
				</div>
				<div class="iconDiv">
					<div class="icon mticon-ws5300black"></div>ws5300black
				</div>
				<div class="iconDiv">
					<div class="icon mticon-wsw"></div>wsw
				</div>
				<div class="iconDiv">
					<div class="icon mticon-xaxis"></div>xaxis
				</div>
				<div class="iconDiv">
					<div class="icon mticon-yaxis"></div>yaxis
				</div><br>
				<h2>Font Awesome</h2>
				<h3>Web Application Icons</h3>
				<div class="row fontawesome-icon-list">
					<div class="iconDiv">
						<i class="icon fa fa-address-book"></i><br>
						address-book
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-address-book-o"></i><br>
						address-book-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-address-card"></i><br>
						address-card
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-address-card-o"></i><br>
						address-card-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-adjust"></i><br>
						adjust
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-american-sign-language-interpreting"></i><br>
						american-sign-language-interpreting
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-anchor"></i><br>
						anchor
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-archive"></i><br>
						archive
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-area-chart"></i><br>
						area-chart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-arrows"></i><br>
						arrows
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-arrows-h"></i><br>
						arrows-h
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-arrows-v"></i><br>
						arrows-v
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-asl-interpreting"></i><br>
						asl-interpreting <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-assistive-listening-systems"></i><br>
						assistive-listening-systems
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-asterisk"></i><br>
						asterisk
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-at"></i><br>
						at
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-audio-description"></i><br>
						audio-description
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-automobile"></i><br>
						automobile <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-balance-scale"></i><br>
						balance-scale
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-ban"></i><br>
						ban
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bank"></i><br>
						bank <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bar-chart"></i><br>
						bar-chart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bar-chart-o"></i><br>
						bar-chart-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-barcode"></i><br>
						barcode
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bars"></i><br>
						bars
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bath"></i><br>
						bath
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bathtub"></i><br>
						bathtub <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery"></i><br>
						battery <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-0"></i><br>
						battery-0 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-1"></i><br>
						battery-1 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-2"></i><br>
						battery-2 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-3"></i><br>
						battery-3 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-4"></i><br>
						battery-4 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-empty"></i><br>
						battery-empty
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-full"></i><br>
						battery-full
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-half"></i><br>
						battery-half
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-quarter"></i><br>
						battery-quarter
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-battery-three-quarters"></i><br>
						battery-three-quarters
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bed"></i><br>
						bed
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-beer"></i><br>
						beer
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bell"></i><br>
						bell
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bell-o"></i><br>
						bell-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bell-slash"></i><br>
						bell-slash
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bell-slash-o"></i><br>
						bell-slash-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bicycle"></i><br>
						bicycle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-binoculars"></i><br>
						binoculars
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-birthday-cake"></i><br>
						birthday-cake
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-blind"></i><br>
						blind
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bluetooth"></i><br>
						bluetooth
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bluetooth-b"></i><br>
						bluetooth-b
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bolt"></i><br>
						bolt
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bomb"></i><br>
						bomb
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-book"></i><br>
						book
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bookmark"></i><br>
						bookmark
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bookmark-o"></i><br>
						bookmark-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-braille"></i><br>
						braille
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-briefcase"></i><br>
						briefcase
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bug"></i><br>
						bug
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-building"></i><br>
						building
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-building-o"></i><br>
						building-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bullhorn"></i><br>
						bullhorn
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bullseye"></i><br>
						bullseye
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-bus"></i><br>
						bus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cab"></i><br>
						cab <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calculator"></i><br>
						calculator
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar"></i><br>
						calendar
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar-check-o"></i><br>
						calendar-check-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar-minus-o"></i><br>
						calendar-minus-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar-o"></i><br>
						calendar-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar-plus-o"></i><br>
						calendar-plus-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-calendar-times-o"></i><br>
						calendar-times-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-camera"></i><br>
						camera
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-camera-retro"></i><br>
						camera-retro
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-car"></i><br>
						car
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-caret-square-o-down"></i><br>
						caret-square-o-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-caret-square-o-left"></i><br>
						caret-square-o-left
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-caret-square-o-right"></i><br>
						caret-square-o-right
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-caret-square-o-up"></i><br>
						caret-square-o-up
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cart-arrow-down"></i><br>
						cart-arrow-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cart-plus"></i><br>
						cart-plus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cc"></i><br>
						cc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-certificate"></i><br>
						certificate
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-check"></i><br>
						check
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-check-circle"></i><br>
						check-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-check-circle-o"></i><br>
						check-circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-check-square"></i><br>
						check-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-check-square-o"></i><br>
						check-square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-child"></i><br>
						child
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-circle"></i><br>
						circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-circle-o"></i><br>
						circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-circle-o-notch"></i><br>
						circle-o-notch
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-circle-thin"></i><br>
						circle-thin
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-clock-o"></i><br>
						clock-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-clone"></i><br>
						clone
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-close"></i><br>
						close <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cloud"></i><br>
						cloud
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cloud-download"></i><br>
						cloud-download
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cloud-upload"></i><br>
						cloud-upload
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-code"></i><br>
						code
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-code-fork"></i><br>
						code-fork
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-coffee"></i><br>
						coffee
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cog"></i><br>
						cog
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cogs"></i><br>
						cogs
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-comment"></i><br>
						comment
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-comment-o"></i><br>
						comment-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-commenting"></i><br>
						commenting
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-commenting-o"></i><br>
						commenting-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-comments"></i><br>
						comments
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-comments-o"></i><br>
						comments-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-compass"></i><br>
						compass
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-copyright"></i><br>
						copyright
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-creative-commons"></i><br>
						creative-commons
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-credit-card"></i><br>
						credit-card
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-credit-card-alt"></i><br>
						credit-card-alt
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-crop"></i><br>
						crop
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-crosshairs"></i><br>
						crosshairs
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cube"></i><br>
						cube
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cubes"></i><br>
						cubes
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-cutlery"></i><br>
						cutlery
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-dashboard"></i><br>
						dashboard <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-database"></i><br>
						database
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-deaf"></i><br>
						deaf
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-deafness"></i><br>
						deafness <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-desktop"></i><br>
						desktop
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-diamond"></i><br>
						diamond
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-dot-circle-o"></i><br>
						dot-circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-download"></i><br>
						download
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-drivers-license"></i><br>
						drivers-license <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-drivers-license-o"></i><br>
						drivers-license-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-edit"></i><br>
						edit <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-ellipsis-h"></i><br>
						ellipsis-h
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-ellipsis-v"></i><br>
						ellipsis-v
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-envelope"></i><br>
						envelope
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-envelope-o"></i><br>
						envelope-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-envelope-open"></i><br>
						envelope-open
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-envelope-open-o"></i><br>
						envelope-open-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-envelope-square"></i><br>
						envelope-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-eraser"></i><br>
						eraser
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-exchange"></i><br>
						exchange
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-exclamation"></i><br>
						exclamation
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-exclamation-circle"></i><br>
						exclamation-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-exclamation-triangle"></i><br>
						exclamation-triangle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-external-link"></i><br>
						external-link
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-external-link-square"></i><br>
						external-link-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-eye"></i><br>
						eye
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-eye-slash"></i><br>
						eye-slash
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-eyedropper"></i><br>
						eyedropper
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-fax"></i><br>
						fax
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-feed"></i><br>
						feed <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-female"></i><br>
						female
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-fighter-jet"></i><br>
						fighter-jet
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-archive-o"></i><br>
						file-archive-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-audio-o"></i><br>
						file-audio-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-code-o"></i><br>
						file-code-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-excel-o"></i><br>
						file-excel-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-image-o"></i><br>
						file-image-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-movie-o"></i><br>
						file-movie-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-pdf-o"></i><br>
						file-pdf-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-photo-o"></i><br>
						file-photo-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-picture-o"></i><br>
						file-picture-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-powerpoint-o"></i><br>
						file-powerpoint-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-sound-o"></i><br>
						file-sound-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-video-o"></i><br>
						file-video-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-word-o"></i><br>
						file-word-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-file-zip-o"></i><br>
						file-zip-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-film"></i><br>
						film
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-filter"></i><br>
						filter
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-fire"></i><br>
						fire
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-fire-extinguisher"></i><br>
						fire-extinguisher
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-flag"></i><br>
						flag
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-flag-checkered"></i><br>
						flag-checkered
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-flag-o"></i><br>
						flag-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-flash"></i><br>
						flash <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-flask"></i><br>
						flask
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-folder"></i><br>
						folder
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-folder-o"></i><br>
						folder-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-folder-open"></i><br>
						folder-open
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-folder-open-o"></i><br>
						folder-open-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-frown-o"></i><br>
						frown-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-futbol-o"></i><br>
						futbol-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-gamepad"></i><br>
						gamepad
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-gavel"></i><br>
						gavel
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-gear"></i><br>
						gear <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-gears"></i><br>
						gears <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-gift"></i><br>
						gift
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-glass"></i><br>
						glass
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-globe"></i><br>
						globe
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-graduation-cap"></i><br>
						graduation-cap
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-group"></i><br>
						group <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-grab-o"></i><br>
						hand-grab-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-lizard-o"></i><br>
						hand-lizard-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-paper-o"></i><br>
						hand-paper-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-peace-o"></i><br>
						hand-peace-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-pointer-o"></i><br>
						hand-pointer-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-rock-o"></i><br>
						hand-rock-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-scissors-o"></i><br>
						hand-scissors-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-spock-o"></i><br>
						hand-spock-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hand-stop-o"></i><br>
						hand-stop-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-handshake-o"></i><br>
						handshake-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hard-of-hearing"></i><br>
						hard-of-hearing <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hashtag"></i><br>
						hashtag
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hdd-o"></i><br>
						hdd-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-headphones"></i><br>
						headphones
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-heart"></i><br>
						heart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-heart-o"></i><br>
						heart-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-heartbeat"></i><br>
						heartbeat
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-history"></i><br>
						history
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-home"></i><br>
						home
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hotel"></i><br>
						hotel <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass"></i><br>
						hourglass
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-1"></i><br>
						hourglass-1 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-2"></i><br>
						hourglass-2 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-3"></i><br>
						hourglass-3 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-end"></i><br>
						hourglass-end
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-half"></i><br>
						hourglass-half
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-o"></i><br>
						hourglass-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-hourglass-start"></i><br>
						hourglass-start
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-i-cursor"></i><br>
						i-cursor
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-id-badge"></i><br>
						id-badge
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-id-card"></i><br>
						id-card
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-id-card-o"></i><br>
						id-card-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-image"></i><br>
						image <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-inbox"></i><br>
						inbox
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-industry"></i><br>
						industry
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-info"></i><br>
						info
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-info-circle"></i><br>
						info-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-institution"></i><br>
						institution <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-key"></i><br>
						key
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-keyboard-o"></i><br>
						keyboard-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-language"></i><br>
						language
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-laptop"></i><br>
						laptop
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-leaf"></i><br>
						leaf
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-legal"></i><br>
						legal <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-lemon-o"></i><br>
						lemon-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-level-down"></i><br>
						level-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-level-up"></i><br>
						level-up
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-life-bouy"></i><br>
						life-bouy <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-life-buoy"></i><br>
						life-buoy <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-life-ring"></i><br>
						life-ring
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-life-saver"></i><br>
						life-saver <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-lightbulb-o"></i><br>
						lightbulb-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-line-chart"></i><br>
						line-chart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-location-arrow"></i><br>
						location-arrow
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-lock"></i><br>
						lock
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-low-vision"></i><br>
						low-vision
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-magic"></i><br>
						magic
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-magnet"></i><br>
						magnet
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mail-forward"></i><br>
						mail-forward <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mail-reply"></i><br>
						mail-reply <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mail-reply-all"></i><br>
						mail-reply-all <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-male"></i><br>
						male
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-map"></i><br>
						map
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-map-marker"></i><br>
						map-marker
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-map-o"></i><br>
						map-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-map-pin"></i><br>
						map-pin
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-map-signs"></i><br>
						map-signs
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-meh-o"></i><br>
						meh-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-microchip"></i><br>
						microchip
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-microphone"></i><br>
						microphone
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-microphone-slash"></i><br>
						microphone-slash
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-minus"></i><br>
						minus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-minus-circle"></i><br>
						minus-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-minus-square"></i><br>
						minus-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-minus-square-o"></i><br>
						minus-square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mobile"></i><br>
						mobile
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mobile-phone"></i><br>
						mobile-phone <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-money"></i><br>
						money
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-moon-o"></i><br>
						moon-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mortar-board"></i><br>
						mortar-board <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-motorcycle"></i><br>
						motorcycle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-mouse-pointer"></i><br>
						mouse-pointer
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-music"></i><br>
						music
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-navicon"></i><br>
						navicon <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-newspaper-o"></i><br>
						newspaper-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-object-group"></i><br>
						object-group
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-object-ungroup"></i><br>
						object-ungroup
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-paint-brush"></i><br>
						paint-brush
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-paper-plane"></i><br>
						paper-plane
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-paper-plane-o"></i><br>
						paper-plane-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-paw"></i><br>
						paw
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-pencil"></i><br>
						pencil
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-pencil-square"></i><br>
						pencil-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-pencil-square-o"></i><br>
						pencil-square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-percent"></i><br>
						percent
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-phone"></i><br>
						phone
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-phone-square"></i><br>
						phone-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-photo"></i><br>
						photo <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-picture-o"></i><br>
						picture-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-pie-chart"></i><br>
						pie-chart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plane"></i><br>
						plane
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plug"></i><br>
						plug
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plus"></i><br>
						plus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plus-circle"></i><br>
						plus-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plus-square"></i><br>
						plus-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-plus-square-o"></i><br>
						plus-square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-podcast"></i><br>
						podcast
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-power-off"></i><br>
						power-off
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-print"></i><br>
						print
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-puzzle-piece"></i><br>
						puzzle-piece
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-qrcode"></i><br>
						qrcode
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-question"></i><br>
						question
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-question-circle"></i><br>
						question-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-question-circle-o"></i><br>
						question-circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-quote-left"></i><br>
						quote-left
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-quote-right"></i><br>
						quote-right
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-random"></i><br>
						random
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-recycle"></i><br>
						recycle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-refresh"></i><br>
						refresh
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-registered"></i><br>
						registered
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-remove"></i><br>
						remove <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-reorder"></i><br>
						reorder <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-reply"></i><br>
						reply
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-reply-all"></i><br>
						reply-all
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-retweet"></i><br>
						retweet
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-road"></i><br>
						road
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-rocket"></i><br>
						rocket
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-rss"></i><br>
						rss
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-rss-square"></i><br>
						rss-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-s15"></i><br>
						s15 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-search"></i><br>
						search
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-search-minus"></i><br>
						search-minus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-search-plus"></i><br>
						search-plus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-send"></i><br>
						send <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-send-o"></i><br>
						send-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-server"></i><br>
						server
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-share"></i><br>
						share
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-share-alt"></i><br>
						share-alt
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-share-alt-square"></i><br>
						share-alt-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-share-square"></i><br>
						share-square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-share-square-o"></i><br>
						share-square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-shield"></i><br>
						shield
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-ship"></i><br>
						ship
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-shopping-bag"></i><br>
						shopping-bag
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-shopping-basket"></i><br>
						shopping-basket
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-shopping-cart"></i><br>
						shopping-cart
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-shower"></i><br>
						shower
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sign-in"></i><br>
						sign-in
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sign-language"></i><br>
						sign-language
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sign-out"></i><br>
						sign-out
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-signal"></i><br>
						signal
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-signing"></i><br>
						signing <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sitemap"></i><br>
						sitemap
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sliders"></i><br>
						sliders
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-smile-o"></i><br>
						smile-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-snowflake-o"></i><br>
						snowflake-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-soccer-ball-o"></i><br>
						soccer-ball-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort"></i><br>
						sort
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-alpha-asc"></i><br>
						sort-alpha-asc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-alpha-desc"></i><br>
						sort-alpha-desc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-amount-asc"></i><br>
						sort-amount-asc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-amount-desc"></i><br>
						sort-amount-desc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-asc"></i><br>
						sort-asc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-desc"></i><br>
						sort-desc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-down"></i><br>
						sort-down <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-numeric-asc"></i><br>
						sort-numeric-asc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-numeric-desc"></i><br>
						sort-numeric-desc
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sort-up"></i><br>
						sort-up <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-space-shuttle"></i><br>
						space-shuttle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-spinner"></i><br>
						spinner
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-spoon"></i><br>
						spoon
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-square"></i><br>
						square
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-square-o"></i><br>
						square-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star"></i><br>
						star
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star-half"></i><br>
						star-half
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star-half-empty"></i><br>
						star-half-empty <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star-half-full"></i><br>
						star-half-full <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star-half-o"></i><br>
						star-half-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-star-o"></i><br>
						star-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sticky-note"></i><br>
						sticky-note
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sticky-note-o"></i><br>
						sticky-note-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-street-view"></i><br>
						street-view
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-suitcase"></i><br>
						suitcase
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-sun-o"></i><br>
						sun-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-support"></i><br>
						support <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tablet"></i><br>
						tablet
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tachometer"></i><br>
						tachometer
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tag"></i><br>
						tag
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tags"></i><br>
						tags
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tasks"></i><br>
						tasks
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-taxi"></i><br>
						taxi
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-television"></i><br>
						television
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-terminal"></i><br>
						terminal
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer"></i><br>
						thermometer <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-0"></i><br>
						thermometer-0 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-1"></i><br>
						thermometer-1 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-2"></i><br>
						thermometer-2 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-3"></i><br>
						thermometer-3 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-4"></i><br>
						thermometer-4 <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-empty"></i><br>
						thermometer-empty
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-full"></i><br>
						thermometer-full
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-half"></i><br>
						thermometer-half
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-quarter"></i><br>
						thermometer-quarter
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thermometer-three-quarters"></i><br>
						thermometer-three-quarters
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thumb-tack"></i><br>
						thumb-tack
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thumbs-down"></i><br>
						thumbs-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thumbs-o-down"></i><br>
						thumbs-o-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thumbs-o-up"></i><br>
						thumbs-o-up
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-thumbs-up"></i><br>
						thumbs-up
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-ticket"></i><br>
						ticket
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-times"></i><br>
						times
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-times-circle"></i><br>
						times-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-times-circle-o"></i><br>
						times-circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-times-rectangle"></i><br>
						times-rectangle <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-times-rectangle-o"></i><br>
						times-rectangle-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tint"></i><br>
						tint
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-down"></i><br>
						toggle-down <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-left"></i><br>
						toggle-left <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-off"></i><br>
						toggle-off
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-on"></i><br>
						toggle-on
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-right"></i><br>
						toggle-right <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-toggle-up"></i><br>
						toggle-up <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-trademark"></i><br>
						trademark
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-trash"></i><br>
						trash
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-trash-o"></i><br>
						trash-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tree"></i><br>
						tree
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-trophy"></i><br>
						trophy
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-truck"></i><br>
						truck
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tty"></i><br>
						tty
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-tv"></i><br>
						tv <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-umbrella"></i><br>
						umbrella
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-universal-access"></i><br>
						universal-access
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-university"></i><br>
						university
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-unlock"></i><br>
						unlock
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-unlock-alt"></i><br>
						unlock-alt
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-unsorted"></i><br>
						unsorted <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-upload"></i><br>
						upload
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user"></i><br>
						user
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-circle"></i><br>
						user-circle
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-circle-o"></i><br>
						user-circle-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-o"></i><br>
						user-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-plus"></i><br>
						user-plus
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-secret"></i><br>
						user-secret
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-user-times"></i><br>
						user-times
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-users"></i><br>
						users
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-vcard"></i><br>
						vcard <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-vcard-o"></i><br>
						vcard-o <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-video-camera"></i><br>
						video-camera
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-volume-control-phone"></i><br>
						volume-control-phone
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-volume-down"></i><br>
						volume-down
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-volume-off"></i><br>
						volume-off
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-volume-up"></i><br>
						volume-up
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-warning"></i><br>
						warning <span class="text-muted">(alias)</span>
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-wheelchair"></i><br>
						wheelchair
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-wheelchair-alt"></i><br>
						wheelchair-alt
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-wifi"></i><br>
						wifi
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-window-close"></i><br>
						window-close
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-window-close-o"></i><br>
						window-close-o
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-window-maximize"></i><br>
						window-maximize
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-window-minimize"></i><br>
						window-minimize
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-window-restore"></i><br>
						window-restore
					</div>
					<div class="iconDiv">
						<i class="icon fa fa-wrench"></i><br>
						wrench
					</div>
				</div>
				<section id="accessibility">
					<h3 class="page-header">Accessibility Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-american-sign-language-interpreting"></i><br>
							american-sign-language-interpreting
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-asl-interpreting"></i><br>
							asl-interpreting <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-assistive-listening-systems"></i><br>
							assistive-listening-systems
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-audio-description"></i><br>
							audio-description
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-blind"></i><br>
							blind
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-braille"></i><br>
							braille
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc"></i><br>
							cc
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-deaf"></i><br>
							deaf
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-deafness"></i><br>
							deafness <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hard-of-hearing"></i><br>
							hard-of-hearing <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-low-vision"></i><br>
							low-vision
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-question-circle-o"></i><br>
							question-circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-sign-language"></i><br>
							sign-language
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-signing"></i><br>
							signing <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-tty"></i><br>
							tty
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-universal-access"></i><br>
							universal-access
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-volume-control-phone"></i><br>
							volume-control-phone
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wheelchair"></i><br>
							wheelchair
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wheelchair-alt"></i><br>
							wheelchair-alt
						</div>
					</div>
				</section>
				<section id="hand">
					<h3 class="page-header">Hand Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-hand-grab-o"></i><br>
							hand-grab-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-lizard-o"></i><br>
							hand-lizard-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-down"></i><br>
							hand-o-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-left"></i><br>
							hand-o-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-right"></i><br>
							hand-o-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-up"></i><br>
							hand-o-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-paper-o"></i><br>
							hand-paper-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-peace-o"></i><br>
							hand-peace-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-pointer-o"></i><br>
							hand-pointer-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-rock-o"></i><br>
							hand-rock-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-scissors-o"></i><br>
							hand-scissors-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-spock-o"></i><br>
							hand-spock-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-stop-o"></i><br>
							hand-stop-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-thumbs-down"></i><br>
							thumbs-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-thumbs-o-down"></i><br>
							thumbs-o-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-thumbs-o-up"></i><br>
							thumbs-o-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-thumbs-up"></i><br>
							thumbs-up
						</div>
					</div>
				</section>
				<section id="transportation">
					<h3 class="page-header">Transportation Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-ambulance"></i><br>
							ambulance
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-automobile"></i><br>
							automobile <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bicycle"></i><br>
							bicycle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bus"></i><br>
							bus
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cab"></i><br>
							cab <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-car"></i><br>
							car
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fighter-jet"></i><br>
							fighter-jet
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-motorcycle"></i><br>
							motorcycle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-plane"></i><br>
							plane
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rocket"></i><br>
							rocket
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ship"></i><br>
							ship
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-space-shuttle"></i><br>
							space-shuttle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-subway"></i><br>
							subway
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-taxi"></i><br>
							taxi
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-train"></i><br>
							train
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-truck"></i><br>
							truck
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wheelchair"></i><br>
							wheelchair
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wheelchair-alt"></i><br>
							wheelchair-alt
						</div>
					</div>
				</section>
				<section id="gender">
					<h3 class="page-header">Gender Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-genderless"></i><br>
							genderless
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-intersex"></i><br>
							intersex <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mars"></i><br>
							mars
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mars-double"></i><br>
							mars-double
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mars-stroke"></i><br>
							mars-stroke
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mars-stroke-h"></i><br>
							mars-stroke-h
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mars-stroke-v"></i><br>
							mars-stroke-v
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mercury"></i><br>
							mercury
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-neuter"></i><br>
							neuter
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-transgender"></i><br>
							transgender
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-transgender-alt"></i><br>
							transgender-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-venus"></i><br>
							venus
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-venus-double"></i><br>
							venus-double
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-venus-mars"></i><br>
							venus-mars
						</div>
					</div>
				</section>
				<section id="file-type">
					<h3 class="page-header">File Type Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-file"></i><br>
							file
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-archive-o"></i><br>
							file-archive-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-audio-o"></i><br>
							file-audio-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-code-o"></i><br>
							file-code-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-excel-o"></i><br>
							file-excel-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-image-o"></i><br>
							file-image-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-movie-o"></i><br>
							file-movie-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-o"></i><br>
							file-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-pdf-o"></i><br>
							file-pdf-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-photo-o"></i><br>
							file-photo-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-picture-o"></i><br>
							file-picture-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-powerpoint-o"></i><br>
							file-powerpoint-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-sound-o"></i><br>
							file-sound-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-text"></i><br>
							file-text
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-text-o"></i><br>
							file-text-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-video-o"></i><br>
							file-video-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-word-o"></i><br>
							file-word-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-zip-o"></i><br>
							file-zip-o <span class="text-muted">(alias)</span>
						</div>
					</div>
				</section>
				<section id="spinner">
					<h3 class="page-header">Spinner Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-circle-o-notch"></i><br>
							circle-o-notch
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cog"></i><br>
							cog
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gear"></i><br>
							gear <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-refresh"></i><br>
							refresh
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-spinner"></i><br>
							spinner
						</div>
					</div>
				</section>
				<section id="form-control">
					<h3 class="page-header">Form Control Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-check-square"></i><br>
							check-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-check-square-o"></i><br>
							check-square-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-circle"></i><br>
							circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-circle-o"></i><br>
							circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dot-circle-o"></i><br>
							dot-circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-minus-square"></i><br>
							minus-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-minus-square-o"></i><br>
							minus-square-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-plus-square"></i><br>
							plus-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-plus-square-o"></i><br>
							plus-square-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-square"></i><br>
							square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-square-o"></i><br>
							square-o
						</div>
					</div>
				</section>
				<section id="payment">
					<h3 class="page-header">Payment Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-cc-amex"></i><br>
							cc-amex
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-diners-club"></i><br>
							cc-diners-club
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-discover"></i><br>
							cc-discover
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-jcb"></i><br>
							cc-jcb
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-mastercard"></i><br>
							cc-mastercard
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-paypal"></i><br>
							cc-paypal
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-stripe"></i><br>
							cc-stripe
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-visa"></i><br>
							cc-visa
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-credit-card"></i><br>
							credit-card
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-credit-card-alt"></i><br>
							credit-card-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-wallet"></i><br>
							google-wallet
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-paypal"></i><br>
							paypal
						</div>
					</div>
				</section>
				<section id="chart">
					<h3 class="page-header">Chart Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-area-chart"></i><br>
							area-chart
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bar-chart"></i><br>
							bar-chart
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bar-chart-o"></i><br>
							bar-chart-o <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-line-chart"></i><br>
							line-chart
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pie-chart"></i><br>
							pie-chart
						</div>
					</div>
				</section>
				<section id="currency">
					<h3 class="page-header">Currency Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-bitcoin"></i><br>
							bitcoin <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-btc"></i><br>
							btc
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cny"></i><br>
							cny <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dollar"></i><br>
							dollar <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-eur"></i><br>
							eur
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-euro"></i><br>
							euro <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gbp"></i><br>
							gbp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gg"></i><br>
							gg
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gg-circle"></i><br>
							gg-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ils"></i><br>
							ils
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-inr"></i><br>
							inr
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-jpy"></i><br>
							jpy
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-krw"></i><br>
							krw
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-money"></i><br>
							money
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rmb"></i><br>
							rmb <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rouble"></i><br>
							rouble <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rub"></i><br>
							rub
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ruble"></i><br>
							ruble <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rupee"></i><br>
							rupee <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-shekel"></i><br>
							shekel <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-sheqel"></i><br>
							sheqel <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-try"></i><br>
							try
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-turkish-lira"></i><br>
							turkish-lira <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-usd"></i><br>
							usd
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-won"></i><br>
							won <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yen"></i><br>
							yen <span class="text-muted">(alias)</span>
						</div>
					</div>
				</section>
				<section id="text-editor">
					<h3 class="page-header">Text Editor Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-align-center"></i><br>
							align-center
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-align-justify"></i><br>
							align-justify
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-align-left"></i><br>
							align-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-align-right"></i><br>
							align-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bold"></i><br>
							bold
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chain"></i><br>
							chain <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chain-broken"></i><br>
							chain-broken
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-clipboard"></i><br>
							clipboard
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-columns"></i><br>
							columns
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-copy"></i><br>
							copy <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cut"></i><br>
							cut <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dedent"></i><br>
							dedent <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-eraser"></i><br>
							eraser
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file"></i><br>
							file
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-o"></i><br>
							file-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-text"></i><br>
							file-text
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-file-text-o"></i><br>
							file-text-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-files-o"></i><br>
							files-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-floppy-o"></i><br>
							floppy-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-font"></i><br>
							font
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-header"></i><br>
							header
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-indent"></i><br>
							indent
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-italic"></i><br>
							italic
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-link"></i><br>
							link
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-list"></i><br>
							list
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-list-alt"></i><br>
							list-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-list-ol"></i><br>
							list-ol
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-list-ul"></i><br>
							list-ul
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-outdent"></i><br>
							outdent
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-paperclip"></i><br>
							paperclip
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-paragraph"></i><br>
							paragraph
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-paste"></i><br>
							paste <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-repeat"></i><br>
							repeat
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rotate-left"></i><br>
							rotate-left <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rotate-right"></i><br>
							rotate-right <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-save"></i><br>
							save <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-scissors"></i><br>
							scissors
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-strikethrough"></i><br>
							strikethrough
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-subscript"></i><br>
							subscript
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-superscript"></i><br>
							superscript
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-table"></i><br>
							table
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-text-height"></i><br>
							text-height
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-text-width"></i><br>
							text-width
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-th"></i><br>
							th
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-th-large"></i><br>
							th-large
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-th-list"></i><br>
							th-list
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-underline"></i><br>
							underline
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-undo"></i><br>
							undo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-unlink"></i><br>
							unlink <span class="text-muted">(alias)</span>
						</div>
					</div>
				</section>
				<section id="directional">
					<h3 class="page-header">Directional Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-angle-double-down"></i><br>
							angle-double-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-double-left"></i><br>
							angle-double-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-double-right"></i><br>
							angle-double-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-double-up"></i><br>
							angle-double-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-down"></i><br>
							angle-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-left"></i><br>
							angle-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-right"></i><br>
							angle-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angle-up"></i><br>
							angle-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-down"></i><br>
							arrow-circle-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-left"></i><br>
							arrow-circle-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-o-down"></i><br>
							arrow-circle-o-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-o-left"></i><br>
							arrow-circle-o-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-o-right"></i><br>
							arrow-circle-o-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-o-up"></i><br>
							arrow-circle-o-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-right"></i><br>
							arrow-circle-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-circle-up"></i><br>
							arrow-circle-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-down"></i><br>
							arrow-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-left"></i><br>
							arrow-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-right"></i><br>
							arrow-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrow-up"></i><br>
							arrow-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrows"></i><br>
							arrows
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrows-alt"></i><br>
							arrows-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrows-h"></i><br>
							arrows-h
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-arrows-v"></i><br>
							arrows-v
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-down"></i><br>
							caret-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-left"></i><br>
							caret-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-right"></i><br>
							caret-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-square-o-down"></i><br>
							caret-square-o-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-square-o-left"></i><br>
							caret-square-o-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-square-o-right"></i><br>
							caret-square-o-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-square-o-up"></i><br>
							caret-square-o-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-caret-up"></i><br>
							caret-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-circle-down"></i><br>
							chevron-circle-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-circle-left"></i><br>
							chevron-circle-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-circle-right"></i><br>
							chevron-circle-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-circle-up"></i><br>
							chevron-circle-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-down"></i><br>
							chevron-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-left"></i><br>
							chevron-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-right"></i><br>
							chevron-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chevron-up"></i><br>
							chevron-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-exchange"></i><br>
							exchange
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-down"></i><br>
							hand-o-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-left"></i><br>
							hand-o-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-right"></i><br>
							hand-o-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hand-o-up"></i><br>
							hand-o-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-long-arrow-down"></i><br>
							long-arrow-down
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-long-arrow-left"></i><br>
							long-arrow-left
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-long-arrow-right"></i><br>
							long-arrow-right
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-long-arrow-up"></i><br>
							long-arrow-up
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-toggle-down"></i><br>
							toggle-down <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-toggle-left"></i><br>
							toggle-left <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-toggle-right"></i><br>
							toggle-right <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-toggle-up"></i><br>
							toggle-up <span class="text-muted">(alias)</span>
						</div>
					</div>
				</section>
				<section id="video-player">
					<h3 class="page-header">Video Player Icons</h3>
					<div class="row fontawesome-icon-list">
						<div class="iconDiv">
							<i class="icon fa fa-arrows-alt"></i><br>
							arrows-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-backward"></i><br>
							backward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-compress"></i><br>
							compress
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-eject"></i><br>
							eject
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-expand"></i><br>
							expand
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fast-backward"></i><br>
							fast-backward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fast-forward"></i><br>
							fast-forward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-forward"></i><br>
							forward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pause"></i><br>
							pause
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pause-circle"></i><br>
							pause-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pause-circle-o"></i><br>
							pause-circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-play"></i><br>
							play
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-play-circle"></i><br>
							play-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-play-circle-o"></i><br>
							play-circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-random"></i><br>
							random
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-step-backward"></i><br>
							step-backward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-step-forward"></i><br>
							step-forward
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stop"></i><br>
							stop
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stop-circle"></i><br>
							stop-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stop-circle-o"></i><br>
							stop-circle-o
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-youtube-play"></i><br>
							youtube-play
						</div>
					</div>
				</section>
				<section id="brand">
					<h3 class="page-header">Brand Icons</h3>
					<div class="row fontawesome-icon-list margin-bottom-lg">
						<div class="iconDiv">
							<i class="icon fa fa-500px"></i><br>
							500px
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-adn"></i><br>
							adn
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-amazon"></i><br>
							amazon
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-android"></i><br>
							android
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-angellist"></i><br>
							angellist
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-apple"></i><br>
							apple
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bandcamp"></i><br>
							bandcamp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-behance"></i><br>
							behance
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-behance-square"></i><br>
							behance-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bitbucket"></i><br>
							bitbucket
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bitbucket-square"></i><br>
							bitbucket-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bitcoin"></i><br>
							bitcoin <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-black-tie"></i><br>
							black-tie
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bluetooth"></i><br>
							bluetooth
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-bluetooth-b"></i><br>
							bluetooth-b
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-btc"></i><br>
							btc
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-buysellads"></i><br>
							buysellads
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-amex"></i><br>
							cc-amex
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-diners-club"></i><br>
							cc-diners-club
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-discover"></i><br>
							cc-discover
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-jcb"></i><br>
							cc-jcb
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-mastercard"></i><br>
							cc-mastercard
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-paypal"></i><br>
							cc-paypal
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-stripe"></i><br>
							cc-stripe
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-cc-visa"></i><br>
							cc-visa
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-chrome"></i><br>
							chrome
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-codepen"></i><br>
							codepen
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-codiepie"></i><br>
							codiepie
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-connectdevelop"></i><br>
							connectdevelop
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-contao"></i><br>
							contao
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-css3"></i><br>
							css3
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dashcube"></i><br>
							dashcube
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-delicious"></i><br>
							delicious
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-deviantart"></i><br>
							deviantart
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-digg"></i><br>
							digg
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dribbble"></i><br>
							dribbble
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-dropbox"></i><br>
							dropbox
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-drupal"></i><br>
							drupal
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-edge"></i><br>
							edge
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-eercast"></i><br>
							eercast
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-empire"></i><br>
							empire
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-envira"></i><br>
							envira
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-etsy"></i><br>
							etsy
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-expeditedssl"></i><br>
							expeditedssl
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fa"></i><br>
							icon fa <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-facebook"></i><br>
							facebook
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-facebook-f"></i><br>
							facebook-f <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-facebook-official"></i><br>
							facebook-official
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-facebook-square"></i><br>
							facebook-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-firefox"></i><br>
							firefox
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-first-order"></i><br>
							first-order
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-flickr"></i><br>
							flickr
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-font-awesome"></i><br>
							font-awesome
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fonticons"></i><br>
							fonticons
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-fort-awesome"></i><br>
							fort-awesome
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-forumbee"></i><br>
							forumbee
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-foursquare"></i><br>
							foursquare
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-free-code-camp"></i><br>
							free-code-camp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ge"></i><br>
							ge <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-get-pocket"></i><br>
							get-pocket
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gg"></i><br>
							gg
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gg-circle"></i><br>
							gg-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-git"></i><br>
							git
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-git-square"></i><br>
							git-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-github"></i><br>
							github
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-github-alt"></i><br>
							github-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-github-square"></i><br>
							github-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gitlab"></i><br>
							gitlab
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gittip"></i><br>
							gittip <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-glide"></i><br>
							glide
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-glide-g"></i><br>
							glide-g
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google"></i><br>
							google
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-plus"></i><br>
							google-plus
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-plus-circle"></i><br>
							google-plus-circle <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-plus-official"></i><br>
							google-plus-official
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-plus-square"></i><br>
							google-plus-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-google-wallet"></i><br>
							google-wallet
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-gratipay"></i><br>
							gratipay
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-grav"></i><br>
							grav
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-hacker-news"></i><br>
							hacker-news
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-houzz"></i><br>
							houzz
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-html5"></i><br>
							html5
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-imdb"></i><br>
							imdb
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-instagram"></i><br>
							instagram
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-internet-explorer"></i><br>
							internet-explorer
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ioxhost"></i><br>
							ioxhost
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-joomla"></i><br>
							joomla
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-jsfiddle"></i><br>
							jsfiddle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-lastfm"></i><br>
							lastfm
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-lastfm-square"></i><br>
							lastfm-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-leanpub"></i><br>
							leanpub
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-linkedin"></i><br>
							linkedin
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-linkedin-square"></i><br>
							linkedin-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-linode"></i><br>
							linode
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-linux"></i><br>
							linux
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-maxcdn"></i><br>
							maxcdn
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-meanpath"></i><br>
							meanpath
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-medium"></i><br>
							medium
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-meetup"></i><br>
							meetup
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-mixcloud"></i><br>
							mixcloud
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-modx"></i><br>
							modx
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-odnoklassniki"></i><br>
							odnoklassniki
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-odnoklassniki-square"></i><br>
							odnoklassniki-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-opencart"></i><br>
							opencart
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-openid"></i><br>
							openid
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-opera"></i><br>
							opera
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-optin-monster"></i><br>
							optin-monster
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pagelines"></i><br>
							pagelines
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-paypal"></i><br>
							paypal
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pied-piper"></i><br>
							pied-piper
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pied-piper-alt"></i><br>
							pied-piper-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pied-piper-pp"></i><br>
							pied-piper-pp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pinterest"></i><br>
							pinterest
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pinterest-p"></i><br>
							pinterest-p
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-pinterest-square"></i><br>
							pinterest-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-product-hunt"></i><br>
							product-hunt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-qq"></i><br>
							qq
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-quora"></i><br>
							quora
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ra"></i><br>
							ra <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-ravelry"></i><br>
							ravelry
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-rebel"></i><br>
							rebel
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-reddit"></i><br>
							reddit
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-reddit-alien"></i><br>
							reddit-alien
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-reddit-square"></i><br>
							reddit-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-renren"></i><br>
							renren
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-resistance"></i><br>
							resistance <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-safari"></i><br>
							safari
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-scribd"></i><br>
							scribd
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-sellsy"></i><br>
							sellsy
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-share-alt"></i><br>
							share-alt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-share-alt-square"></i><br>
							share-alt-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-shirtsinbulk"></i><br>
							shirtsinbulk
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-simplybuilt"></i><br>
							simplybuilt
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-skyatlas"></i><br>
							skyatlas
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-skype"></i><br>
							skype
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-slack"></i><br>
							slack
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-slideshare"></i><br>
							slideshare
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-snapchat"></i><br>
							snapchat
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-snapchat-ghost"></i><br>
							snapchat-ghost
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-snapchat-square"></i><br>
							snapchat-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-soundcloud"></i><br>
							soundcloud
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-spotify"></i><br>
							spotify
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stack-exchange"></i><br>
							stack-exchange
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stack-overflow"></i><br>
							stack-overflow
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-steam"></i><br>
							steam
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-steam-square"></i><br>
							steam-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stumbleupon"></i><br>
							stumbleupon
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-stumbleupon-circle"></i><br>
							stumbleupon-circle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-superpowers"></i><br>
							superpowers
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-telegram"></i><br>
							telegram
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-tencent-weibo"></i><br>
							tencent-weibo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-themeisle"></i><br>
							themeisle
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-trello"></i><br>
							trello
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-tripadvisor"></i><br>
							tripadvisor
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-tumblr"></i><br>
							tumblr
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-tumblr-square"></i><br>
							tumblr-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-twitch"></i><br>
							twitch
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-twitter"></i><br>
							twitter
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-twitter-square"></i><br>
							twitter-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-usb"></i><br>
							usb
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-viacoin"></i><br>
							viacoin
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-viadeo"></i><br>
							viadeo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-viadeo-square"></i><br>
							viadeo-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-vimeo"></i><br>
							vimeo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-vimeo-square"></i><br>
							vimeo-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-vine"></i><br>
							vine
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-vk"></i><br>
							vk
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wechat"></i><br>
							wechat <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-weibo"></i><br>
							weibo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-weixin"></i><br>
							weixin
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-whatsapp"></i><br>
							whatsapp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wikipedia-w"></i><br>
							wikipedia-w
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-windows"></i><br>
							windows
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wordpress"></i><br>
							wordpress
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wpbeginner"></i><br>
							wpbeginner
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wpexplorer"></i><br>
							wpexplorer
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-wpforms"></i><br>
							wpforms
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-xing"></i><br>
							xing
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-xing-square"></i><br>
							xing-square
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-y-combinator"></i><br>
							y-combinator
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-y-combinator-square"></i><br>
							y-combinator-square <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yahoo"></i><br>
							yahoo
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yc"></i><br>
							yc <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yc-square"></i><br>
							yc-square <span class="text-muted">(alias)</span>
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yelp"></i><br>
							yelp
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-yoast"></i><br>
							yoast
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-youtube"></i><br>
							youtube
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-youtube-play"></i><br>
							youtube-play
						</div>
						<div class="iconDiv">
							<i class="icon fa fa-youtube-square"></i><br>
							youtube-square
						</div>
					</div><br>
					<br>
				</section>
		</div>
		<?php include($baseURL."footer.php")?>

	</body>
</html>

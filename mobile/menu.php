<?php
	############################################################################
	#
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Menu - mobile
	#
	############################################################################
	#
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################


	$availableDays = "";
	$firstDbDay = "";
	$lastDbDay = "";
	$availableYears = array();

	$result = mysqli_query($con,"
		SELECT Year(DateTime), Month(DateTime), Day(DateTime)
		FROM alldata
		Group BY Year(DateTime), Month(DateTime), Day(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		if($firstDbDay==""){
			$firstDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
		}
		$currentDate = $row['Year(DateTime)']."-".$row['Month(DateTime)']."-".$row['Day(DateTime)'];
		$availableDays .= "\"".$currentDate."\",";
		$lastDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
	}

	$result = mysqli_query($con,"
		SELECT DISTINCT Year(DateTime)
		FROM alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		array_push($availableYears,$row['Year(DateTime)']);
	}



	###################################################################################################################
	###################################################################################################################
?>

	<script src="<?php echo $pageURL.$path?>mobile/menu.js"></script>
	<style>
		#cssmenu,
		#cssmenu ul,
		#cssmenu ul li,
		#cssmenu ul li a,
		#cssmenu #menu-button {

		  margin: 0;
		  padding: 0;
		  border: 0;
		  list-style: none;
		  line-height: 1;
		  display: block;
		  position: relative;
		  -webkit-box-sizing: border-box;
		  -moz-box-sizing: border-box;
		  box-sizing: border-box;
		}
		#cssmenu:after,
		#cssmenu > ul:after {
		  content: ".";
		  display: block;
		  clear: both;
		  visibility: hidden;
		  line-height: 0;
		  height: 0;
		}
		#cssmenu #menu-button {
		  display: none;
		}
		#cssmenu {
		  font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		  background: #<?php echo $color_schemes[$design2]['900']?>;
		  z-index:10;
		}
		#cssmenu > ul > li {
		  float: left;
		}
		#cssmenu.align-center > ul {
		  font-size: 0;
		  text-align: center;
		}
		#cssmenu.align-center > ul > li {
		  display: inline-block;
		  float: none;
		}
		#cssmenu.align-center ul ul {
		  text-align: left;
		}
		#cssmenu.align-right > ul > li {
		  float: right;
		}
		#cssmenu > ul > li > a {
		  padding: 17px;
		  font-size: 12px;
		  letter-spacing: 1px;
		  text-decoration: none;
		  color: #dddddd;
		  font-weight: 700;
			<?php 
				if($menuLinksUpper){
			?>
					text-transform: uppercase;
					font-size: 0.8em;
			<?php 
				}
				else{
			?>
					font-size: 0.9em;
			<?php
				}
			?>
		}
		#cssmenu > ul > li:hover > a {
		  color: #ffffff;
		}
		#cssmenu > ul > li.has-sub > a {
		  padding-right: 30px;
		}
		#cssmenu > ul > li.has-sub > a:after {
		  position: absolute;
		  top: 22px;
		  right: 11px;
		  width: 8px;
		  height: 2px;
		  display: block;
		  background: #dddddd;
		  content: '';
		}
		#cssmenu > ul > li.has-sub > a:before {
		  position: absolute;
		  top: 19px;
		  right: 14px;
		  display: block;
		  width: 2px;
		  height: 8px;
		  background: #dddddd;
		  content: '';
		  -webkit-transition: all .15s ease;
		  -moz-transition: all .15s ease;
		  -ms-transition: all .15s ease;
		  -o-transition: all .15s ease;
		  transition: all .15s ease;
		}
		#cssmenu > ul > li.has-sub:hover > a:before {
		  top: 23px;
		  height: 0;
		}
		#cssmenu ul ul {
		  position: absolute;
		  left: -9999px;
		}
		#cssmenu.align-right ul ul {
		  text-align: right;
		}
		#cssmenu ul ul li { 
		  height: 0;
		  -webkit-transition: all .15s ease;
		  -moz-transition: all .15s ease;
		  -ms-transition: all .15s ease;
		  -o-transition: all .15s ease;
		  transition: all .15s ease;
		}
		#cssmenu li:hover > ul {
		  left: auto;
		}
		#cssmenu.align-right li:hover > ul {
		  left: auto;
		  right: 0;
		}
		#cssmenu li:hover > ul > li {
		  height: 35px;
		}
		#cssmenu ul ul ul {
		  margin-left: 100%;
		  top: 0;
		}
		#cssmenu.align-right ul ul ul {
		  margin-left: 0;
		  margin-right: 100%;
		}
		#cssmenu ul ul li a {
		  border-bottom: 1px solid rgba(150, 150, 150, 0.15);
		  padding: 11px 15px;
		  width: 170px;
		  font-size: 13px;
		  text-decoration: none;
		  color: #dddddd;
		  font-weight: 400;
		  background: #<?php echo $color_schemes[$design2]['700']?>;
		}
		#cssmenu ul ul li:last-child > a,
		#cssmenu ul ul li.last-item > a {
		  border-bottom: 0;
		}
		#cssmenu ul ul li:hover > a,
		#cssmenu ul ul li a:hover {
		  color: #ffffff;
		}
		#cssmenu ul ul li.has-sub > a:after {
		  position: absolute;
		  top: 16px;
		  right: 11px;
		  width: 8px;
		  height: 2px;
		  display: block;
		  background: #dddddd;
		  content: '';
		}
		#cssmenu.align-right ul ul li.has-sub > a:after {
		  right: auto;
		  left: 11px;
		}
		#cssmenu ul ul li.has-sub > a:before {
		  position: absolute;
		  top: 13px;
		  right: 14px;
		  display: block;
		  width: 2px;
		  height: 8px;
		  background: #dddddd;
		  content: '';
		  -webkit-transition: all .15s ease;
		  -moz-transition: all .15s ease;
		  -ms-transition: all .15s ease;
		  -o-transition: all .15s ease;
		  transition: all .15s ease;
		}
		#cssmenu.align-right ul ul li.has-sub > a:before {
		  right: auto;
		  left: 14px;
		}
		#cssmenu ul ul > li.has-sub:hover > a:before {
		  top: 17px;
		  height: 0;
		}
		@media all and (max-width: 768px), only screen and (-webkit-min-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (min--moz-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (-o-min-device-pixel-ratio: 2/1) and (max-width: 1024px), only screen and (min-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (min-resolution: 192dpi) and (max-width: 1024px), only screen and (min-resolution: 2dppx) and (max-width: 1024px) {
		  #cssmenu {
			width: 100%;
		  }
		  #cssmenu ul {
			width: 100%;
			display: none;
		  }
		  #cssmenu.align-center > ul {
			text-align: left;
		  }
		  #cssmenu ul li {
			width: 100%;
			border-top: 1px solid rgba(120, 120, 120, 0.2);
		  }
		  #cssmenu ul ul li,
		  #cssmenu li:hover > ul > li {
			height: auto;
		  }
		  #cssmenu ul li a,
		  #cssmenu ul ul li a {
			width: 100%;
			border-bottom: 0;
		  }
		  #cssmenu > ul > li {
			float: none;
		  }
		  #cssmenu ul ul li a {
			padding-left: 25px;
		  }
		  #cssmenu ul ul ul li a {
			padding-left: 35px;
		  }
		  #cssmenu ul ul li a {
			color: #dddddd;
			background: #<?php echo $color_schemes[$design2]['700']?>;
		  }
		  #cssmenu ul ul li:hover > a,
		  #cssmenu ul ul li.active > a {
			color: #ffffff;
		  }
		  #cssmenu ul ul,
		  #cssmenu ul ul ul,
		  #cssmenu.align-right ul ul {
			position: relative;
			left: 0;
			width: 100%;
			margin: 0;
			text-align: left;
		  }
		  #cssmenu > ul > li.has-sub > a:after,
		  #cssmenu > ul > li.has-sub > a:before,
		  #cssmenu ul ul > li.has-sub > a:after,
		  #cssmenu ul ul > li.has-sub > a:before {
			display: none;
		  }
		  #cssmenu #menu-button {
			display: block;
			padding: 17px;
			color: #dddddd;
			cursor: pointer;
			font-size: 12px;
			text-transform: uppercase;
			font-weight: 700;
		  }
		  #cssmenu #menu-button:after {
			position: absolute;
			top: 22px;
			right: 17px;
			display: block;
			height: 4px;
			width: 20px;
			border-top: 2px solid #dddddd;
			border-bottom: 2px solid #dddddd;
			content: '';
		  }
		  #cssmenu #menu-button:before {
			position: absolute;
			top: 16px;
			right: 17px;
			display: block;
			height: 2px;
			width: 20px;
			background: #dddddd;
			content: '';
		  }
		  #cssmenu #menu-button.menu-opened:after {
			top: 23px;
			border: 0;
			height: 2px;
			width: 15px;
			background: #ffffff;
			-webkit-transform: rotate(45deg);
			-moz-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			-o-transform: rotate(45deg);
			transform: rotate(45deg);
		  }
		  #cssmenu #menu-button.menu-opened:before {
			top: 23px;
			background: #ffffff;
			width: 15px;
			-webkit-transform: rotate(-45deg);
			-moz-transform: rotate(-45deg);
			-ms-transform: rotate(-45deg);
			-o-transform: rotate(-45deg);
			transform: rotate(-45deg);
		  }
		  #cssmenu .submenu-button {
			position: absolute;
			z-index: 99;
			right: 0;
			top: 0;
			display: block;
			border-left: 0px solid rgba(120, 120, 120, 0.2);
			height: 46px;
			width: 46px;
			cursor: pointer;
		  }
		  #cssmenu .submenu-button.submenu-opened {
			background: #<?php echo $color_schemes[$design2]['700']?>;
		  }
		  #cssmenu ul ul .submenu-button {
			height: 34px;
			width: 34px;
		  }
		  #cssmenu .submenu-button:after {
			position: absolute;
			top: 22px;
			right: 19px;
			width: 8px;
			height: 2px;
			display: block;
			background: #dddddd;
			content: '';
		  }
		  #cssmenu ul ul .submenu-button:after {
			top: 15px;
			right: 13px;
		  }
		  #cssmenu .submenu-button.submenu-opened:after {
			background: #ffffff;
		  }
		  #cssmenu .submenu-button:before {
			position: absolute;
			top: 19px;
			right: 22px;
			display: block;
			width: 2px;
			height: 8px;
			background: #dddddd;
			content: '';
		  }
		  #cssmenu ul ul .submenu-button:before {
			top: 12px;
			right: 16px;
		  }
		  #cssmenu .submenu-button.submenu-opened:before {
			display: none;
		  }
		}
		#mobileHomeIcon{
			width: 100%;
			max-width:30px;
			opacity: 0.8;
			cursor: pointer;
		}
		#mobileHomeIcon:hover{
			opacity:1;
		}
		@media only screen and (max-width: 768px) {
				#cssmenu ul ul{
					display: none
				} 
		}
	</style>
	<div id='cssmenu'>
		<ul>
			 	<li>
						<a href='<?php echo $pageURL.$path?>indexMobile.php' style="padding:6px 10px">
								<img src='<?php echo $pageURL.$path?>icons/home.png' onclick="window.location='<?php echo $pageURL.$path?>indexMobile.php'" alt="" id="mobileHomeIcon">
						</a>
				</li>
				<li>
				<a href='#'><?php echo lang('weather station','c')?></a>
				<ul>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/daySelect.php'><?php echo lang('day report','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/monthSelect.php'><?php echo lang('month report','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/yearSelect.php'><?php echo lang('annual report','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/graph.php'><?php echo lang('interactive graphs','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/day.php'><?php echo lang('day calculations','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/night.php'><?php echo lang('night calculations','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/rainSeasons.php'><?php echo lang('rain seasons','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/seasonsIndex.php'><?php echo lang('seasons','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/table.php'><?php echo lang('interactive table','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/liveData.php'><?php echo lang('current data','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/live.php'><?php echo lang('gauges','c')?></a></li>
						<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/history.php'><?php echo lang('history','c')?></a></li>
						<li><a href='#'><?php echo lang('trends','c')?></a>
						<ul>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=T'><?php echo lang('temperature','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=A'><?php echo lang('apparent temperature','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=D'><?php echo lang('dewpoint','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=H'><?php echo lang('humidity','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=P'><?php echo lang('pressure','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=W'><?php echo lang('wind speed','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=G'><?php echo lang('wind gust','c')?></a></li>
								<?php
									if($solarSensor){
								?>
										<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/trends.php?var=S'><?php echo lang('solar radiation','c')?></a></li>
								<?php
									}
								?>
						</ul>
					</li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/station/redirect.php?url=climateIndices.php'><?php echo lang('climate','c')?></a></li>
				</ul>
			</li>
			<li>
				<a href='#'><?php echo lang('weather','c')?></a>
				<ul>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/forecast/index.php'><?php echo lang("outlook",'c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/metar/index.php'>METAR</a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/maps/global.php'><?php echo lang('map','c')?></a></li>
				</ul>
		   </li>
		   <li><a href='<?php echo $pageURL.$path?>mobile/pages/climate/map.php'><?php echo lang('climate','c')?></a></li>
		    <li>
				<a href='#'><?php echo lang('astronomy','c')?></a>
				<ul>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/astronomy/moonPhase.php'><?php echo lang('moon phase','c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/astronomy/sun.php'><?php echo lang('sunrise','c')?>/<?php echo lang('sunset','c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/astronomy/annualSolarMax.php'><?php echo lang('maximum potential solar radiation','c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/astronomy/astroCalendar.php'><?php echo lang('astronomy calendar','c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/astronomy/equisol.php'><?php echo lang('solstice and equinox','c')?></a></li>
				</ul>
				</li>
		   <li>
				<a href='#'><?php echo lang('info','c')?></a>
				<ul>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/other/aboutStation.php'><?php echo lang('weather station','c')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/other/aboutLocation.php'><?php echo $stationLocation?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/other/aboutPage.php'><?php echo lang('WWW','u')?></a></li>
					<li><a href='<?php echo $pageURL.$path?>mobile/pages/other/links.php'><?php echo lang('links','c')?></a></li>
				</ul>
		   </li>
			 	<?php
					if($_SESSION['user']=="admin"){
				?>
						<li>
							<a href='#'><?php echo lang('admin','c')?></a>
							<ul>
								<li><a href='<?php echo $pageURL.$path?>admin/index.php'><?php echo lang('control panel','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>install/setup.php'><?php echo lang('main setup','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>admin/homepageStart.php?type=desktop'><?php echo lang('desktop homepage','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>admin/homepageStart.php?type=mobile'><?php echo lang('mobile homepage','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>admin/blockSetup.php'><?php echo lang('block setup','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>admin/pluginSetup.php'><?php echo lang('plugin setup','c')?></a></li>
								<li><a href='<?php echo $pageURL.$path?>admin/menu/menuTabs.php'><?php echo lang('menu setup','c')?></a></li>
								
							</ul>
						</li>
				<?php 
					}
				?>
		</ul>
	</div>

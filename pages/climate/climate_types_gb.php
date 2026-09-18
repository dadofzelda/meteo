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
	#	Climate classification
	#
	# 	A script which calculates climate type based on temperature and
	#	precipitation and generates description for each.
	#
	#############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	include("../../config.php");
	
	function Koppen($hemisphere,$t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12,$r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12){

		if($hemisphere=="N"){
			$t_summer = array($t4,$t5,$t6,$t7,$t8,$t9);
			$t_winter = array($t10,$t11,$t12,$t1,$t2,$t3);
			$r_summer = array($r4,$r5,$r6,$r7,$r8,$r9);
			$r_winter = array($r10,$r11,$r12,$r1,$r2,$r3);
		}
		if($hemisphere=="S"){
			$t_winter = array($t4,$t5,$t6,$t7,$t8,$t9);
			$t_summer = array($t10,$t11,$t12,$t1,$t2,$t3);
			$r_winter = array($r4,$r5,$r6,$r7,$r8,$r9);
			$r_summer = array($r10,$r11,$r12,$r1,$r2,$r3);
		}

		$r = array_sum($r_summer)+array_sum($r_winter); // annual rain
		$t = (array_sum($t_summer)+array_sum($t_winter))/12; // annual average temperature


		$temps = array($t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12);
		$rains = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12);

		$temps_sorted = array($t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12);
		sort($temps_sorted);
		$rains_sorted = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12);
		sort($rains_sorted);
		
		$first = "";
		$second = "";
		$third = "";

		// Determine 1st letter, default is B type
		if(min($temps)>=18){
			$first = "A";
		}
		if((max($temps)>=10)&&(min($temps)<18)&&(min($temps)>-3)){
			$first = "C";
		}
		if((max($temps)>=10)&&(min($temps)<-3)){
			$first = "D";
		}
		if(max($temps)<10){
			$first = "E";
		}
		if((array_sum($r_summer)>=(0.7*$r))&&($r<(20*$t+280))){
			$first = "B";
		}
		if((array_sum($r_winter)>=(0.7*$r))&&($r<(20*$t))){
			$first = "B";
		}
		if((array_sum($r_winter)<=(0.7*$r))&&(array_sum($r_summer)<=(0.7*$r))&&($r<(20*$t+140))){
			$first = "B";
		}

		// Determine second letter
		if($first=="A"){
			if($rains_sorted[0]>=60){
				$second = "f";
			}
			else if(($rains_sorted[0]<60)&&($rains_sorted[0]>=(100-($r/25)))){
				$second = "m";
			}
			else{
				$second = "w";
			}
		}
		if($first=="B"){
			if(array_sum($r_summer)>=(0.7*$r)){
				$r_limit = 20*$t + 280;
			}
			else if(array_sum($r_winter)>=(0.7*$r)){
				$r_limit = 20*$t;
			}
			else{
				$r_limit = 20*$t+140;
			}
			if($r<(0.5*$r_limit)){
				$second = "W";
			}
			if($r>=(0.5*$r_limit)){
				$second = "S";
			}
		}
		if($first=="C"){
			if((min($r_summer)<30)&&(min($r_summer)<((1/3)*(max($r_winter))))){
				$second = "s";
			}
			else if(min($r_winter)<(0.1*max($r_summer))){
				$second = "w";
			}
			else{
				$second = "f";
			}
		}
		if($first=="D"){
			if((min($r_summer)<30)&&(min($r_summer)<((1/3)*(max($r_winter))))){
				$second = "s";
			}
			else if(min($r_winter)<(0.1*max($r_summer))){
				$second = "w";
			}
			else{
				$second = "f";
			}
		}
		if($first=="E"){
			if(max($temps)>0){
				$second = "T";
			}
			if(max($temps)<=0){
				$second = "F";
			}
		}

		// determine third letter
		if($first=="B"){
			if($t>=18){
				$third = "h";
			}
			if($t<18){
				$third = "k";
			}
		}
		if($first=="C"){
			if(max($temps)>=22){
				$third = "a";
			}
			if(($temps_sorted[8]>10)&&(max($temps)<22)){
				$third = "b";
			}
			if(($temps_sorted[8]<10)&&(max($temps)>10)&&(max($temps)<22)){
				$third = "c";
			}
		}
		if($first=="D"){
			if(max($temps)>=22){
				$third = "a";
			}
			if(($temps_sorted[8]>10)&&(max($temps)<22)){
				$third = "b";
			}
			if(($temps_sorted[8]<10)&&(max($temps)>10)&&(max($temps)<22)){
				$third = "c";
			}
			if($temps_sorted[0]<-38){
				$third = "d";
			}
		}
		
		//finalize
		$type = $first."".$second."".$third;
		
		return $type;
	}

	function trewartha($hemisphere,$t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12,$r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12){

		if($hemisphere=="N"){
			$t_summer = array($t4,$t5,$t6,$t7,$t8,$t9);
			$t_winter = array($t10,$t11,$t12,$t1,$t2,$t3);
			$r_summer = array($r4,$r5,$r6,$r7,$r8,$r9);
			$r_winter = array($r10,$r11,$r12,$r1,$r2,$r3);
		}
		if($hemisphere=="S"){
			$t_winter = array($t4,$t5,$t6,$t7,$t8,$t9);
			$t_summer = array($t10,$t11,$t12,$t1,$t2,$t3);
			$r_winter = array($r4,$r5,$r6,$r7,$r8,$r9);
			$r_summer = array($r10,$r11,$r12,$r1,$r2,$r3);
		}

		$r = array_sum($r_summer)+array_sum($r_winter); // annual rain
		$t = (array_sum($t_summer)+array_sum($t_winter))/12; // annual average temperature

		$temps = array($t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12);
		$rains = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12);

		$temps_sorted = array($t1,$t2,$t3,$t4,$t5,$t6,$t7,$t8,$t9,$t10,$t11,$t12);
		sort($temps_sorted);
		$rains_sorted = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12);
		sort($rains_sorted);
		
		$first = "";
		$second = "";
		$third = "";
		
		$patton_threshold = 2.3*$t-0.64*(array_sum($r_winter)/$r)+41;

		// Determine 1st letter
		if((min($temps)>=18)&&(($r/10)>=$patton_threshold)){
			$first = "A";
		}
		if(($r/10)<$patton_threshold){
			$first = "B";
			if((0.5*$patton_threshold<$r/10)){
				$second = "S";
			}
			if(($r/10)<0.5*$patton_threshold){
				$second = "W";
			}
		}
		$condition1 = 0;
		for($i=0;$i<count($temps);$i++){
			if($temps[$i]>=10){
				$condition1++;
			}
		}
		if((min($temps)<18)&&($condition1>=8)){
			$first = "C";
		}
		if(($condition1<8)&&($condition1>=4)){
			$first = "D";
		}
		if(($condition1<4)&&($condition1>=1)){
			$first = "E";
		}
		if(max($temps)<10){
			$first = "F";
		}

		// Determine second letter
		$condition2 = 0;
		$condition3 = 0;
		$condition4 = 0;
		for($i=0;$i<count($rains);$i++){
			if($rains[$i]>=60){
				$condition2++;
			}
		}
		for($i=0;$i<count($r_summer);$i++){
			if($r_summer[$i]<60){
				$condition3++;
			}
		}
		for($i=0;$i<count($r_winter);$i++){
			if($r_winter[$i]<60){
				$condition4++;
			}
		}
		if($first=="A"){
			if($condition3>2){
				$second = "w";
			}
			if($condition4>2){
				$second = "s";
			}
			else{
				$second = "r";
			}
		}
		if($first=="C"){
			if((min($r_summer)<30)&&(min($r_summer)<((1/3)*(max($r_winter))))&&($r<890)){
				$second = "s";
			}
			else if(min($r_winter)<(0.1*max($r_summer))){
				$second = "w";
			}
			else{
				$second = "f";
			}
		}
		if($first=="D"){
			if(min($temps)>0){
				$second = "O";
			}
			else{
				$second = "C";
			}
		}
		if($first=="E"){
			if(min($temps)>-10){
				$second = "O";
			}
			if(min($temps)<=-10){
				$second = "C";
			}
		}
		if($first=="F"){
			if(max($temps)>0){
				$second = "T";
			}
			if(max($temps)<=0){
				$second = "I";
			}
		}
		
		//determine additinal parameters
		if(max($temps)>=35){
			$third = "i";
		}
		if((max($temps)<35)&&(max($temps)>=28)){
			$third = "h";
		}
		if((max($temps)<28)&&(max($temps)>=23)){
			$third = "a";
		}
		if((max($temps)<23)&&(max($temps)>=18)){
			$third = "b";
		}
		if((max($temps)<18)&&(max($temps)>=10)){
			$third = "l";
		}
		if((max($temps)<10)&&(max($temps)>=0)){
			$third = "k";
		}
		if((max($temps)<0)&&(max($temps)>=-10)){
			$third = "o";
		}
		if((max($temps)<-10)&&(max($temps)>=-25)){
			$third = "c";
		}
		if((max($temps)<-25)&&(max($temps)>=-40)){
			$third = "d";
		}
		if(max($temps)<-40){
			$third = "e";
		}
		
		if(min($temps)>=35){
			$fourth = "i";
		}
		if((min($temps)<35)&&(min($temps)>=28)){
			$fourth = "h";
		}
		if((min($temps)<28)&&(min($temps)>=23)){
			$fourth = "a";
		}
		if((min($temps)<23)&&(min($temps)>=18)){
			$fourth = "b";
		}
		if((min($temps)<18)&&(min($temps)>=10)){
			$fourth = "l";
		}
		if((min($temps)<10)&&(min($temps)>=0)){
			$fourth = "k";
		}
		if((min($temps)<0)&&(min($temps)>=-10)){
			$fourth = "o";
		}
		if((min($temps)<-10)&&(min($temps)>=-25)){
			$fourth = "c";
		}
		if((min($temps)<-25)&&(min($temps)>=-40)){
			$fourth = "d";
		}
		if(min($temps)<-40){
			$fourth = "e";
		}
		
		//finalize
		$type = $first."".$second."".$third."".$fourth;
		
		return $type;
	}

	function climateDesc($climate){
		global $climate_name;
		global $climate_color;
		global $climate_text_color;
		global $Koppen_desc;
		global $pageURL;
		global $path;
		
		switch($climate){
			case "Af":
				$climate_name = "Tropical rainforest climate";
				$climate_color = "#EAF1DD";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>A</b> – average temperature of coldest month higher than 18 °C</li><li><b>f</b> – precipitation of all months at least 60 mm</li></ul><p>This type of climate, sometimes also called equatorial climate, is typical for regions close to the equator, most often 5 to 10 degrees south or north from it. It is usually is areas covered with tropical rainforests. The climate is characterized by absence of a dry period, all months having precipitation amount of at least 60 mm. There are also no seasons during the year. The temperature and precipitation distribution remains more less the same throughout the year and the same also applies for the duration of day and night, which remains constant due to the latitude near the equator. Differences between temperatures during the day and night can be significant. The average temperature during the day remains high all year round, quite often above 30 °C. Also thunderstorms are quite common and most intense rains usually occur late in the afternoon and in the evening.</p><p>This climate is quite common in Southeast Asia, central Africa and South America. Tropical microclimate, however, is also found in some other regions such as for example the northern part of Queensland in Australia or the eastern part of Madagascar. </p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/af.png' style='width:500px' alt='' alt=''></td></tr></table></div>";
				break;
			case "Am":
				$climate_name = "Tropical monsoon climate";
				$climate_color = "#C2D69A";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>A</b> – average temperature of coldest month higher than 18 °C</li><li><b>m</b> – precipitation of driest month less than 60 mm, but greater than or equal to 100-0.04r (where r is the total average precipitation in mm)</li></ul><p>Tropical monsoon climate is characterized by alternating dry and wet periods, similar to the  tropical climate of savannas. The temperature range during the day remains more less constant throughout the year and the driest month usually is the one right after or close to the winter solstice in that particular hemisphere.</p><p>This type of climate is most often encountered in South and Central America, some parts of South and Southeastern Asia, western and central part of Africa, the Caribbean and parts of North America.</p><p>Most significant factor that determines the climate are the monsoons. These are seasonal winds changing direction during the year. For example in Asia during the summer, wind blows from the ocean to land, in the winter it is the opposite.</p><p>Temperatures are high all year and precipitation can be lower during some months, overall however, the total annual precipitation is often greater than that of equatorial climate (Af), where it is just more evenly distributed. The dry period is usually much shorter than the wet one.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/am.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Aw":
				$climate_name = "Tropical savanna climate with dry winter";
				$climate_color = "#75923C";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>A</b> – average temperature of coldest month higher than 18 °C</li><li><b>w</b> – precipitation during driest month less than 60 mm and less than 100-0.04r (where r is total annual rainfall in mm). Areas with this type of climate have the dry period during “winter”, i.e. during the time of the year with short days and longer nights.</li></ul><p>Tropical savanna climate is characterized by substantial variation during the year. There is a dry period and wet period. The dry period is longer than in case of the tropical monsoon climate (Am). Temperatures remain high all year round, but with greater daily amplitude. Total annual rainfall is usually less than in case of equatorial or tropical monsoon climate. The dry period is during “winter” – i.e. time of the year with shorter days and longer nights.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/aw.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "As":
				$climate_name = "Tropical savanna climate with dry summer";
				$climate_color = "#75923C";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>A</b> – average temperature of coldest month higher than 18 °C</li><li><b>s</b> – precipitation during driest month less than 60 mm and less than 100-0.04r (where r is total annual rainfall in mm). Areas with this type of climate have the dry period during “summer”, i.e. during the time of the year with longer days and shorter nights.</li></ul><p>Tropical savanna climate is characterized by substantial variation during the year. There is a dry period and wet period. The dry period is longer than in case of the tropical monsoon climate (Am). Temperatures remain high all year round, but with greater daily amplitude. Total annual rainfall is usually less than in case of equatorial or tropical monsoon climate. The dry period is during “summer” – i.e. time of the year with longer days and shorter nights.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/as.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "BSh":
				$climate_name = "Hot semi-arid climate";
				$climate_color = "#FDE9D9";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>B</b> – 70 % or more of the total annual rainfall is during the summer half of the year and r (r = average total annual precipitation in mm) is less than 20t + 280 (t = average annual temperature in °C), or 70 % or more of total annual precipitation is during the winter half of the year and r is less than 20t, or no half of the year has less than 70 % of total annual rainfall and r less than 20t + 140. Summer half of the year in the Northern hemisphere is from April to September, on the Southern hemisphere from October to March.</li><li><b>S</b> – r is less than half of the upper limit of B type classification parameter</li><li><b>h</b> - t is equal to or greater than 18 °C</li></ul><p>The hot semi-arid climate is found in tropical and subtropical regions. Summers tend to be hot, sometimes extremely hot and winters mild. This climate type is commonly found for example in western Africa, India, parts of Mexico and some regions in Southern USA. It is usually found deep inland as a continuation of the tropical desert climate.</p><p>It is also characterized by large temperature range and relatively low precipitation.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/bsh.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "BSk":
				$climate_name = "Cold semi-arid climate";
				$climate_color = "#FAC090";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>B</b> – 70 % or more of the total annual rainfall is during the summer half of the year and r (r = average total annual precipitation in mm) is less than 20t + 280 (t = average annual temperature in °C), or 70 % or more of total annual precipitation is during the winter half of the year and r is less than 20t, or no half of the year has less than 70 % of total annual rainfall and r less than 20t + 140. Summer half of the year in the Northern hemisphere is from April to September, on the Southern hemisphere from October to March.</li><li><b>S</b> – r is less than the upper limit of B type classification parameter</li><li><b>k</b> - t is less than 18 °C</li></ul><p>Cold semi-arid climate is most commonly found in the temperate zone inland, relatively far from the ocean. Although the summers are warm, they are substantially colder than in the case of the hot semi-arid climate type (BSh). The difference can be seen particularly during the winter, when temperatures can drop quite low. The daily temperature amplitude is quite high and the differences between day and night can be more than 20 °C.</p><p>Regions with this type of climate, which are at higher altitudes tend to have drier winters and wetter summers, while for areas at lower elevation, it is the opposite – drier summers and wetter winters.</p><p>Most often one can see this climate type in Asia and North America, but it also includes for example some regions in Spain, parts of South America, Southern Australia and New Zealand.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/bsk.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "BWh":
				$climate_name = "Hot desert climate";
				$climate_color = "#FFFF99";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>B</b> – 70 % or more of the total annual rainfall is during the summer half of the year and r (r = average total annual precipitation in mm) is less than 20t + 280 (t = average annual temperature in °C), or 70 % or more of total annual precipitation is during the winter half of the year and r is less than 20t, or no half of the year has less than 70 % of total annual rainfall and r less than 20t + 140. Summer half of the year in the Northern hemisphere is from April to September, on the Southern hemisphere from October to March.</li><li><b>W</b> – r is less than the upper limit for B type classification parameter</li><li><b>h</b> - t is greater than or equal to 18 °C</li></ul><p>The desert climate is characterized by extremely small rainfall. In general it includes areas, which have such little precipitation, that there is hardly any vegetation at all.</p><p>The total annual precipitation is at most 250 mm, but quite often much less and regions with no rainfall during the entire year are not an exception. In specific cases this climate type is also assigned to places with more than 250 mm of rainfall annually, but where more water is lost by evapotranspiration than falls with rain (for example some parts in Arizona).</p><p>Hot desert climate is also defined by very high temperatures all year round. Most commonly these regions are around the 30 ° latitude on both hemispheres. Temperatures can be extreme, exceeding 40 °C and sometimes even above 45 °C. However, due to the fact they are usually far inland, the differences between day and night can also be extreme and during night it is sometimes even freezing. Areas with BWh climate type are the ones where the absolutely highest temperatures on Earth have been measured (Death Valley, some parts in Libya etc.).</p><p>The most well-known region with this climate is the largest desert on Earth, the Sahara in Africa. Other areas with this climate include the deserts in Libya, parts in southern Africa (Namib desert, Kalahari desert), some parts of Mexico, large areas especially in the central Australia and also some regions in the Middle East.</p><p>Very low precipitation also means very little cloudiness, some regions can have clouds only less than 30 days per year.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/bwh.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "BWk":
				$climate_name = "Cold desert climate";
				$climate_color = "#D1CC00";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>B</b> – 70 % or more of the total annual rainfall is during the summer half of the year and r (r = average total annual precipitation in mm) is less than 20t + 280 (t = average annual temperature in °C), or 70 % or more of total annual precipitation is during the winter half of the year and r is less than 20t, or no half of the year has less than 70 % of total annual rainfall and r less than 20t + 140. Summer half of the year in the Northern hemisphere is from April to September, on the Southern hemisphere from October to March.</li><li><b>W</b> - r is less than the upper limit for B type classification parameter</li><li><b>k</b> - t is lower than 18 °C</li></ul><p>The desert climate is characterized by extremely small rainfall. In general it includes areas, which have such little precipitation, that there is hardly any vegetation at all.</p><p>The total annual precipitation is at most 250 mm, but quite often much less and regions with no rainfall during the entire year are not an exception. In specific cases this climate type is also assigned to places with more than 250 mm of rainfall annually, but where more water is lost by evapotranspiration than falls with rain (for example some parts in Arizona).</p><p>Although the climate type has the word “cold” in it, this term is rather relative. Summers can still be very and sometimes extremely hot (temperatures, however, do not reach such high values as in the case of hot desert climate type BWh). What is different from the BWh type is the occurrence of also colder periods, sometimes extremely cold with temperatures well below freezing point.</p><p>Basically all regions with this climate type are found in Asia . Usually in the lee of high mountain ranges, often at high altitudes. The most well-known region with this climate type is probably the Asian Gobi desert in Mongolia. Summers are very hot, while winters extremely cold, the annual temperature range is very high and the same is true for the day/night temperature differences as well. Apart from central Asia (Mongolia, China, India), the BWk climate is also assigned to some parts in Western USA and South America.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/bwk.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Cfa":
				$climate_name = "Humid subtropical climate";
				$climate_color = "#F2DDDC";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year and do not fulfill classification criteria for s or w type</li><li><b>a</b> - temperature of warmest month above 22 °C</li></ul><p>The Cfa climate type is typical for areas on the Eastern coasts of continents, mostly between 20th and 30th latitude. The average temperature of the warmest month is higher than 22 °C and the temperature throughout the year range from usually -3 to 18 °C. In the winter, however, in some cases it can be well below freezing point.</p><p>Precipitation is relatively evenly distributed during the entire year. Humidity tends to be higher during the summer half year, when the conditions resemble the humid tropical climate. The total annual rainfall can be quite variable, most commonly between 650 and 2500 mm. Thunderstorms during the summer are frequent.</p><p>Cfa climate type can be found for example in the Southeastern USA, Northern part of Argentina, Uruguay and Southern part of Brazil, Southern Japan and South of China.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/cfa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Cfb":
				$climate_name = "Oceanic climate";
				$climate_color = "#D99795";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year and do not fulfill classification criteria for s or w type</li><li><b>b</b> - temperature of each of the four warmest months is higher than 10°C, but the temperature of the warmest month is less than 22 °C</li></ul><p>The Cfb climate type is an oceanic type of climate with mild winter and evenly distributed precipitation. Average temperature of all months is less than 22 °C, which is what differentiates it from the Cfa type. Meanwhile at least four months have average temperature of 10 °C or more.</p><p>Just like precipitation, also temperatures tend to be relatively constant and differences between day and night are not very big. Precipitation throughout the year are frequent, but not very intense and sometimes even many wetdays in a row can occur. In the winter and in the autumn are common fogs and during the year also thunderstorms.</p><p>Winters are mild (temperatures only very rarely drops below 0 °C and if, then only very slightly) and summers nicely warm. Frontal activity causes lot of cloudiness and relatively high humidity in the autumn, winter and spring.</p><p>The total annual rainfall ranges usually between 500 and 2500 mm and in the winter it sometimes also snows. </p><p>Temperate oceanic climate Cfb can be found for example in the United Kingdom, Western part of France, Germany, on the West coast of Europe, Northern part of Spain, some parts of South America (Southern and Northern tips), in Southeast Australia and New Zealand. </p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/cfb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Cfc":
				$climate_name = "Subarctic oceanic climate";
				$climate_color = "#953735";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year and do not fulfill classification criteria for s or w type</li><li><b>c</b> - average temperature of one to three months greater than or equal to 10 °C, but warmest month with temperature less than 22 °C</li></ul><p>Subarctic oceanic climate differs from the Cfb by the fact that it is in general colder. Just like in case of Cfb, the average monthly temperature of all months is less than 22°C, in this case however, the temperature only exceeds 10 °C in one to three months.</p><p>Daily temperature range is very small and this is true throughout the entire year. Precipitation is evenly distributed to all months.</p><p>In the summer, the temperature only rarely rises above 20 °C and at night drops to 5 to 10 °C. In the winter it can get quite cold and temperatures in the range of -5 to -10 °C are nothing unusual.</p><p>Cfc climate type is relatively rare, most often it is found on islands and coasts in the vicinity of the polar circle. This includes for example Iceland, Aleut islands, Faroe Islands, Shetlands or Auckland islands.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/cfc.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Csa":
				$climate_name = "Hot-summer Mediterranean climate";
				$climate_color = "#F2DDDC";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>s</b> – precipitation in the driest month during the summer half year is less than 30 mm and less than one third of the precipitation in the month with highest precipitation in the winter half year.</li><li><b>a</b> - temperature of the warmest month is higher than 22 °C</li></ul><p>Cs climate type is characterized by a dry summer and wet winter. In the case of the Csa subtype, the summer tends to be very warm. The average temperature of the warmest month is above 22 °C. The wettest month in the winter usually has three times as much precipitation than the driest month in the summer, which usually has less than 30 mm of rainfall. Summers are therefore quite dry, with relatively little cloudiness and often longer periods with no rain at all. Winters on the other hand are wet and very rarely it snows as well.</p><p>Temperatures in the summer reach very high values between 30 and 40 °C. In the winter it is very unusual for the temperatures to fall below freezing point.</p><p>The continental Mediterranean climate (Csa) is sometimes also referred to as hot Mediterranean climate. The higher temperatures are the result of the fact that the places are usually deeper inland. The Csa subtype is more common than the other Cs climates and usually represents what most people imagine under the term “Mediterranean climate”.</p><p>It can be found for example around the Mediterranean sea in Europe, in Southwest Australia, Southwestern part of South Africa, some parts in Central Asia and in Northern California. Other places also include some microregions such as the Spanish capital Madrid, Italian Rome, American Los Angeles or Perth in Australia.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/csa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Csb":
				$climate_name = "Warm-summer Mediterranean climate";
				$climate_color = "#D99795";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>s</b> – precipitation of the driest month in the summer half year is lower than 30 mm and less than one third of the precipitation during the wettest month of the winter half year. </li><li><b>b</b> - temperature of each of the four warmest months is higher than 10 °C, but the temperature of the warmest month is less than 22 °C</li></ul><p>The Cs climate type is characterized by dry summers and wet winters. Summer temperatures however are in this case not as high as in case of the Csa subtype. The average temperature of the warmest month is not higher than 22 °C.</p><p>In comparison with the continental Csa subtype, the Csb type is less common.</p><p>Most of precipitation is observed in the winter, which is usually mild, but with possible colder spans. In the summer it is dry, but nicely warm and sunny, which however also increases the risk of wild fires.</p><p>The Csb climate can be found for example in some regions on the West coast of the USA (for example San Francisco), in the Southwestern part of South America, some parts in Spain and Portugal, Turkey or South of Africa (Cape Town).</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/csb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Cwa":
				$climate_name = "Humid subTropical climate with dry winter";
				$climate_color = "#F2DDDC";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>w</b> – precipitation in the driest month of the winter half year is less than one tenth of the precipitation during the wettest month in the summer half-year.</li><li><b>a</b> - temperature of the warmest month is higher than 22 °C</li></ul><p>The Cw climate type is in general characterized by the fact that most precipitation occurs in the summer. The temperature of the warmest month is above 10 °C and the coldest month is between -3 and 18 °C. In the summer the temperature of the warmest month is above 22 °C.</p><p>Summers are wet and warm, winters mild and dry, often with periods with complete absence of precipitation. Especially pronounced difference between summers and winters can be observed in regions affected by the monsoons, for example in some parts of Southeast Asia. Most of the summer precipitation falls during thunderstorms and heavy rains.</p><p>Cwa climate type can be found in inland parts of Central and East Africa (Angola, Northeast Zimbabwe, parts of Mozambique, South Congo, Southwest Tanzania, Malawi, Zambia), in some parts of South America and close to the Andes in Northwest Argentina.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/cwa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Cwb":
				$climate_name = "Humid subtropical highland climate";
				$climate_color = "#D99795";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>C</b> – temperature of warmest month greater than or equal to 10 °C and temperature of coldest month less than 18 °C, but higher than -3 °C</li><li><b>w</b> – precipitation in the driest month of the winter half-year is less than one tenth of the amount during the wettest month of the summer half-year..</li><li><b>b</b> - temperature of each of the four warmest months is higher than 10°C, but the temperature of the warmest month is less than 22 °C</li></ul><p> The Cw climate type is in general characterized by precipitation occurring mostly in the summer. Temperature of the warmest month is above 10 °C and coldest month has a temperature in the range between -3 and + 18 °C. Summers are mild, four of the warmest months have temperature above 10 °C, but never more than 22 °C.</p><p>This subtype is in fact an alternative of the oceanic climate (Cf type), but typical for regions at higher altitudes, often also microregions within other climate types, such as the tropical one, where the difference is associated with the elevation.</p><p>Temperatures in the summer are not as high as in the case of Cwa, but winters are also not very cold and snow only rare.</p><p>Cwb subtype can be found for example in Central America (Mexico City), parts of East, South and Southeast Africa, close to the Atlas mountain range, in some mountains in South Europe and Southeast Asia, including the Himalayas, some parts in Australia, where, however, the temperatures are slightly higher and summers drier in comparison to what is typical for the rest of the regions classified under this climate subtype (summer maxima can very rarely be even around 40 °C).</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/cwb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dfa":
				$climate_name = "Warm continental humid climate";
				$climate_color = "#DBE5F1";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>f</b> - precipitation is relatively evenly distributed throughout the year.</li><li><b>a</b> - temperature of the warmest month is higher than 22 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p>This warmest alternative of the D climate type has precipitation evenly distributed throughout the year. The highest temperatures in the summer quite often reach very high values between 30 and 40 °C, in the winter however, they can drop to values in the range of – 10 to -25 °C. Regions assigned to the Dfa climate type in Europe tend to be drier than those with the same climate type in North America. The warmest month is usually July, sometimes August. The coldest month, with temperature below -3 °C, is most often January.</p><p>The Dfa subtype can be found in some parts of Central and Central East USA (for example Chicago, Boston, New York City or Pittsburgh), in Southern Ukraine, some parts of Russia or China.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dfa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dfb":
				$climate_name = "Mild continental humid climate";
				$climate_color = "#B8CCE4";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year.</li><li><b>b</b> - temperature of each of the four warmest months is higher than 10°C, but the temperature of the warmest month is less than 22 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses. </p><p>Just like the Dfa type, precipitation is evenly distributed throughout the year. In comparison to the Dfa however, summer is slightly colder and the warmest month does not have average temperature above 22 °C. The highest summer temperatures are still usually between 30 and 35 °C, in the winter it drops to -10 to -35 °C.</p><p>Precipitation is higher in the summer, but the difference is not very large. Most of the precipitation in the summer occurs during thunderstorms and so quite often there are longer dry periods between them.</p><p>This Dfb category includes for example parts of Northern U.S. (for example Buffalo or Calgary), Southern part of Canada (for example Ottawa), Southern part of Russia (for example Vladivostok), Western part of Central Europe or Southern part of Scandinavia (for example Helsinki).</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dfb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dfc":
				$climate_name = "Subarctic humid climate";
				$climate_color = "#538ED5";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year.</li><li><b>c</b> - temperature of one to three months is higher than or equal to 10 °C, but the warmest month temperature is lower than 22 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p>This subtype is sometimes also referred to as boreal climate of the taiga. It is characterized by usually very long and cold winters and short, cooler summers. One of the unique characteristics is the extremely large difference between temperatures during summer and winter. In the winter, the minimum temperature can fall even below -40 °C, while in the summer the maximum temperature value is usually around 30 °C. Summers usually last only three to four months and to be classified as Dfc, there has to be at least one month with average temperature above 10 °C. Winter usually means five to seven months with temperatures below freezing point. Spring and autumn are relatively short and the temperature change between seasons quite fast.</p><p>In case of the Dfc subtype, there are no distinctive dry or wet periods. Precipitation is usually higher during the summer and consists of more less intense rains, thunderstorms are relatively rare. The total annual precipitation ranges mostly between 120 and 500 mm.</p><p>Dfc subtype is by far the most frequent subarctic climate type and can be found in latitudes between 50 and 70 ° on the Northern hemisphere. This includes for example most of Siberia, Kamchatka, parts of Scotland, Northern part of Scandinavia, Alaska, large regions in Canada, Northern Mongolia etc. However, it can also be found in microregions at very high altitudes, for example in the Alps in France, Germany, Switzerland, Italy or Austria, in central Romania, some mountains in Turkey, American Rocky Mountains or White Mountains, parts of China, India etc.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dfc.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dfd":
				$climate_name = "Extremely cold subarctic humid climate";
				$climate_color = "#17375D";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>f</b> – precipitation is relatively evenly distributed throughout the year.</li><li><b>d</b> - temperature of the coldest month lower than -38 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> The Dfd type is an extreme version of the subarctic climate, which is defined by the temperature of the coldest month lower than -38 °C. Despite this extreme cold however, there is still short, but very warm summer and the temperature of the warmest month is higher than 10 °C, which is what differentiates Dfd subtype from the polar climate.</p><p>Precipitation is evenly distributed and usually between 100 and 500 mm annually. Humidity is relatively low all year round.</p><p>Unlike the Dfc subtype, which is the second most common climate type on Earth, the Dfd climate is quite rare. It basically covers only some parts of Northeast Siberia.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dfd.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dsa":
				$climate_name = "Hot continental climate with dry summer";
				$climate_color = "#DBE5F1";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>s</b> – precipitation of the driest month during the summer is less than 30 mm and less than one third of the wettest month during the winter half-year.</li><li><b>a</b> - temperature of the warmest month is higher than 22 °C</li></ul><p> In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> This subtype is characterized by very warm summers, where temperature of the warmest month is above 22 °C. Meanwhile, the coldest month however, has average temperature of less than -3 °C. In the winter the temperatures usually drop to around -5 to -20 °C. In the summer it can be even more than 35 °C and the daily temperature variation gets larger.</p><p>Summers tend to be dry and warm. Most precipitation is observed in the winter or the beginning of spring.</p><p>Dsa subtype is not very common and can be found usually in regions bordering the Mediterranean climate, but at higher altitudes. This includes for example some parts in Western U.S. or some areas in Mongolia.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dsa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dsb":
				$climate_name = "Mild continental climate with dry summer";
				$climate_color = "#C5D9F1";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>s</b> – precipitation of the driest month during the summer is less than 30 mm and less than one third of the wettest month during the winter half-year.</li><li><b>b</b> - temperature of each of the four warmest months is higher than 10°C, but the temperature of the warmest month is less than 22 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> Dsb subtype is characterized by relatively warm summers, which are significantly drier than the rest of the year. Highest summer temperatures usually range around 30 to 35 °C, in the winter on the other hand, drop to approximately -10 to -15 °C. In comparison to the Dsa subtype, this type is slightly colder and none of the months has an average temperature of more than 22 °C. Still however, at least four months of the year have average temperature higher than 10 °C.</p><p>Summers are warm and dry. Most precipitation is observed in the winter or beginning of spring.</p><p>Dsb type is more common than Dsa, but still not very frequent and includes for example some parts in Western U.S. or in Turkey.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dsb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dsc":
				$climate_name = "Subarctic continental climate with dry summer";
				$climate_color = "#538ED5";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>s</b> – precipitation of the driest month during the summer is less than 30 mm and less than one third of the wettest month during the winter half-year.</li><li><b>c</b> - temperature of one to three months higher than or equal to 10 °C, but average temperature of the warmest month less than 22 °C</li></ul><p>In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> This subtype is characterized by long and usually very cold winters and short, cooler summers. What is typical are extremely large differences between temperatures of the individual seasons. In the winter the temperatures can drop below -40 °C, in the summer on the other hand, values close to 30 °C are not uncommon. Summers usually last only three to four months, but in order to be classified as Dsc, it is necessary that at least one month has an average temperature of more than 10 °C. Winter usually means five to seven months with temperatures below freezing point. Spring and autumn are relatively short and the temperature change from winter to summer and vice versa quite fast.</p><p>Most precipitation is observed during winter or beginning of spring.</p><p>This climate type is extremely rare and can basically be found only in some parts of Canada, Alaska and a few high-altitude areas in U.S., Korean peninsula and Russia.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dsc.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dwa":
				$climate_name = "Warm continental climate with dry winter";
				$climate_color = "#DBE5F1";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>w</b> – precipitation of the driest month during the winter half-year are less than one tenth of the precipitation during the wettest month of the summer half-year.</li><li><b>a</b> - temperature of the warmest month above 22 °C </li></ul><p>> In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> The Dwa subtype is characterized by very warm summers, with warmest month having average temperature of more than 22 °C. In the meantime however, the average temperature of the coldest month is not more than -3 °C. The winter minimum temperatures usually range between -5 to -20 °C and during summer can reach even 35 °C and daily temperature variation is very large as well.</p><p>Winters are dry and most precipitation is observed in the summer, which also means there is usually not much snow in the winter. Dwa subtype can be found in some parts of Eastern Asia affected by the monsoon activity. In particular it includes some parts of Eastern China and most of the Korean peninsula.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dwa.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dwb":
				$climate_name = "Mild continental climate with dry winter";
				$climate_color = "#C5D9F1";
				$climate_text_color = "black";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>w</b> – precipitation of the driest month during the winter half-year are less than one tenth of the precipitation during the wettest month of the summer half-year.</li><li><b>b</b> - temperature of each of the four warmest months is higher than 10°C, but the temperature of the warmest month is less than 22 °C</li></ul><p> In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses.</p><p> Dwb subtype is characterized by very warm summers, which are also much drier than the rest of the year. Highest summer temperatures usually range between 30 to 35 °C and in the winter it can get as cold as -10 to -15 °C. In comparison to the Dwa subtype it is colder and there is no month with average temperature above 22 °C. Still, at least four months have an average temperature higher than 10 °C.</p><p>It includes some parts in Southern China, close to the Himalayas, Central and Southeast China and the Southeastern tip of Russia.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dwb.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "Dwc":
				$climate_name = "Subarctic continental climate with dry winter";
				$climate_color = "#538ED5";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>D</b> – temperature of the warmest month is higher than or equal to 10 °C and temperature of the coldest month is less than or equal to -3 °C.</li><li><b>w</b> – precipitation of the driest month during the winter half-year are less than one tenth of the precipitation during the wettest month of the summer half-year.</li><li><b>c</b> - temperature of one to three months greater than or equal to 10 °C, but temperature of the warmest month less than 22 °C </li></ul><p> In general it can be said that the D climate type can only be found in the Northern Hemisphere, northwards from the C climate zones, closer to the North Pole. The average temperature during the warmest month is higher than or equal to 10 °C, but in the winter the coldest month has an average value of less than -3 °C. In the winter it often snows and it tends to be quite windy and cold due to the polar and Arctic air masses. </p><p> Dwc subtype is characterized by usually very cold winters and short, colder summers. What is typical are extremely large differences between temperatures in the summer and in the winter. Winter minima can be as low as -40 °C, while summer maxima can reach approximately 30 °C. Summers usually last only three to four months, but in order to be classified as Dwc, it is necessary that at least one month has an average temperature of more than 10 °C. Winters usually last five to seven months and during those months the temperature is below freezing point. Spring and autumn are relatively short meaning the change of temperature from winter to summer and vice versa is quite fast.</p><p>Winter tends to be dry and most precipitation is observed during the summer, quite often in the form of several intense thunderstorms. Due to the dry winter, despite the very low temperatures, there is usually not very much snow. Dwc climate type includes for example most of Northern Mongolia, some parts in Southeast Russia, Central China or Alaska.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/dwc.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "ET":
				$climate_name = "Polar Tundra";
				$climate_color = "#000000";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>E</b> – average temperature of the warmest month less than 10 °C </li><li><b>T</b> – temperature of the warmest month between 0 to 10 °C </li></ul><p> The defining feature of polar climate is the very low temperatures all year round. What is also typical are large differences between individual seasons, but not as profound as in the bordering subarctic zones. The average temperature of the warmest month is above freezing point, but below 10 °C. This means that during this time of the year, the snow melts, but in regions with annual average temperature of less than -9 °C, the soil remains frozen (permafrost) the whole year. Such low temperatures mean no trees can be found here and vegetation only consists of smaller species.</p><p>Winters are usually long and cold and precipitation is observed almost entirely in a form of dry snow. The total annual precipitation is typically less than 350 mm (under some specific conditions it can be much more however).</p><p>Summers are mild with daily maxima around 15 to 18 °C and due to the relative vicinity of the poles, the days are long. Also the cloudiness is quite high. Long, dark and cold winters mean temperatures drop to as low as -20 to -50 °C.</p><p>.Polar climate type can be found in the Northernmost part of Canada, coastal areas of Greenland, Northern Alaska and the Northernmost parts of Russia. However, it also includes areas of very high altitudes such as the Himalayas in Nepal.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/et.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			case "EF":
				$climate_name = "Polar Ice Cap";
				$climate_color = "#60497B";
				$climate_text_color = "white";
				$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><span style='font-size:1.6em'>Traditional Koppen classification: ".$climate." (".$climate_name.")</span><br><br><table class='tableSpacing2Padding2' style='width:100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Official definition:<br><ul><li><b>E</b> – average temperature of the warmest month less than 10 °C </li><li><b>F</b> – average temperature of all months below freezing point</li></ul><p> The ice-cap climate can be found in the coldest places on Earth. The temperature hardly ever exceeds 0 °C and the average temperature of all months is negative. In some areas, the daily maximum can be approximately 5 °C, but only for a very short period of time. Daily variation of temperatures is quite small.</p><p>This climate type can be found in the regions close to the both poles and includes most of Greenland, the Southernmost tip of South America, Northernmost tip of Russia, Antarctica and the highest-altitude places on Earth.</p><p>Such low temperatures mean there is no vegetation and the whole area is covered with snow and ice. In rare cases, the warmer regions can have a few weeks during the year, when ferns and lichens appear. Animal species found here are those that completely rely on food from the ocean.</p><p>Unlike the ET climate, the snow does not melt even during the summer and so glaciers form, which are constantly moving. In the winter temperatures fall well below -20 °C, in regions more inland to less than -65 °C. The lowest temperatures are observed at the end of the polar night.</p><p>Annual precipitation amount is very small and usually ranges only between 50 to 500 mm, the higher value being for coastal areas.</p><p>What is also common are very strong winds, which even further lower the apparent temperature.</p></td><td style='width:500px'><img src='".$pageURL.$path."imgs/climateImgs/maps/ef.png' style='width:500px' alt=''></td></tr></table></div>";
				break;
			default:
				$climate_name = "";
		}
	}

	function climateDesctrewartha($climate){
		global $climate_name_trewartha;
		global $climate_color_trewartha;
		global $climate_text_color_trewartha;
		global $Trewartha_desc;
		global $pageURL;
		global $path;
		
		$first = substr($climate, 0, 1);
		$second = substr($climate,1,1);
		$third = substr($climate,2,1);
		$fourth = substr($climate,3,1);
		
		if($first=="A"){
			if($second=="r"){
				$climate_name_trewartha = "Tropical climate with evenly distributed precipitation";
				$climate_color_trewartha = "#EAF1DD";
				$climate_text_color_trewartha = "black";
			}
			if($second=="w"){
				$climate_name_trewartha = "Tropical climate with dry winter";
				$climate_color_trewartha = "#C2D69A";
				$climate_text_color_trewartha = "black";
			}
			if($second=="s"){
				$climate_name_trewartha = "Tropical climate with dry summer";
				$climate_color_trewartha = "#C2D69A";
				$climate_text_color_trewartha = "black";
			}
		}
		if($first=="B"){
			if($second=="S"){
				$climate_name_trewartha = "Steppe climate";
				$climate_color_trewartha = "#FDE9D9";
				$climate_text_color_trewartha = "black";
			}
			if($second=="W"){
				$climate_name_trewartha = "Desert climate";
				$climate_color_trewartha = "#FFFF99";
				$climate_text_color_trewartha = "black";
			}
		}
		if($first=="C"){
			if($second=="s"){
				$climate_name_trewartha = "Subtropical climate with dry summer";
				$climate_color_trewartha = "#953735";
				$climate_text_color_trewartha = "black";
			}
			if($second=="w"){
				$climate_name_trewartha = "Subtropical climate with dry winter";
				$climate_color_trewartha = "#D99795";
				$climate_text_color_trewartha = "black";
			}
			if($second=="f"){
				$climate_name_trewartha = "Subtropical climate with evenly distributed precipitation";
				$climate_color_trewartha = "#F2DDDC";
				$climate_text_color_trewartha = "black";
			}
		}
		if($first=="D"){
			if($second=="O"){
				$climate_name_trewartha = "Temperate oceanic climate";
				$climate_color_trewartha = "#95B6F0";
				$climate_text_color_trewartha = "black";
			}
			if($second=="C"){
				$climate_name_trewartha = "Temperate continental climate";
				$climate_color_trewartha = "#4281ED";
				$climate_text_color_trewartha = "white";
			}
		}
		if($first=="E"){
			if($second=="O"){
				$climate_name_trewartha = "Boreal oceanic climate";
				$climate_color_trewartha = "#E1A2E8";
				$climate_text_color_trewartha = "black";
			}
			if($second=="C"){
				$climate_name_trewartha = "Boreal continental climate";
				$climate_color_trewartha = "#D911F0";
				$climate_text_color_trewartha = "white";
			}
		}
		if($first=="F"){
			if($second=="T"){
				$climate_name_trewartha = "Polar tundra climate";
				$climate_color_trewartha = "#C2C2C2";
				$climate_text_color_trewartha = "black";
			}
			if($second=="I"){
				$climate_name_trewartha = "Polar ice cap climate";
				$climate_color_trewartha = "#000000";
				$climate_text_color_trewartha = "white";
			}
		}
		if($third=="i"){
			$climate_name_trewartha = $climate_name_trewartha." with extremely hot summer";
		}
		if($third=="h"){
			$climate_name_trewartha = $climate_name_trewartha." with very hot summer";
		}
		if($third=="a"){
			$climate_name_trewartha = $climate_name_trewartha." with hot summer";
		}
		if($third=="b"){
			$climate_name_trewartha = $climate_name_trewartha." with warm summer";
		}
		if($third=="l"){
			$climate_name_trewartha = $climate_name_trewartha." with mild summer";
		}
		if($third=="k"){
			$climate_name_trewartha = $climate_name_trewartha." with cool summer";
		}
		if($third=="o"){
			$climate_name_trewartha = $climate_name_trewartha." with cold summer";
		}
		if($third=="c"){
			$climate_name_trewartha = $climate_name_trewartha." with very cold summer";
		}
		if($third=="d"){
			$climate_name_trewartha = $climate_name_trewartha." with extremely cold summer";
		}
		if($third=="e"){
			$climate_name_trewartha = $climate_name_trewartha." with freezing summer";
		}
		
		if($fourth=="i"){
			$climate_name_trewartha = $climate_name_trewartha." with extremely hot winter";
		}
		if($fourth=="h"){
			$climate_name_trewartha = $climate_name_trewartha." with very hot winter";
		}
		if($fourth=="a"){
			$climate_name_trewartha = $climate_name_trewartha." with hot winter";
		}
		if($fourth=="b"){
			$climate_name_trewartha = $climate_name_trewartha." with warm winter";
		}
		if($fourth=="l"){
			$climate_name_trewartha = $climate_name_trewartha." with mild winter";
		}
		if($fourth=="k"){
			$climate_name_trewartha = $climate_name_trewartha." with cool winter";
		}
		if($fourth=="o"){
			$climate_name_trewartha = $climate_name_trewartha." with cold winter";
		}
		if($fourth=="c"){
			$climate_name_trewartha = $climate_name_trewartha." with very cold winter";
		}
		if($fourth=="d"){
			$climate_name_trewartha = $climate_name_trewartha." with extremely cold winter";
		}
		if($fourth=="e"){
			$climate_name_trewartha = $climate_name_trewartha." with freezing winter";
		}
		if($first=="A"){
			if($second=="r"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Just like in the Koppen classification, tropical climate is based on the average temperature of all months being higher than 18 °C. This basically means that all regions classified as A in the Trewartha classification, are places with warm weather all year round.</p><p>The Ar subtype is characterized by evenly distributed precipitation throughout the year. There are no regular drought or wet periods. The Ar climate type is sometimes also referred to as the climate of the rainforests. It is most commonly found around the equator in areas covered with tropical rainforests. Typical is high humidity and sufficient precipitation. Seasonal variability is very small and because these places are located close to the equator, also the day and night length do not change very much during the year.</p>";
			}
			if($second=="w"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Just like in the Koppen classification, tropical climate is based on the average temperature of all months being higher than 18 °C. This basically means that all regions classified as A in the Trewartha classification, are places with warm weather all year round.</p><p>The Aw subtype is characterized by a drier period during the “winter” – the term winter here refers to the part of the year with shorter days and longer nights. This climate type (sometimes also called the tropical climate of the savannas) is typical for savannas, most commonly found in Africa, Asia and South and Central America.</p><p>In general there are four savanna climate types:<ul><li>clearly distinguishable dry and wet periods of approximately same lengths. Most of precipitation falls during the wet period, during the dry period it rains only very little if at all.</li><li>longer dry period followed by a relatively short wet period. In this case there are typically seven or more months of drought followed by five or less months with rain. This creates a relatively large variability and includes places with almost equal length dry and wet periods (5 and 7 moths), but also places with a very long drought and then very short wet period, which, however, is typical with very intense heavy rains.</li><li>the third type is a relatively long wet period and a shorter dry period, in other words the exact opposite of the previous type. There are at least seven wet months and maximum of five dry months.</li><li>the fourth type is very rare and is characterized by dry periods, during which however, it can sometimes also rain and so the difference between the dry and wet period is smaller than in the previous three types.</li></ul></p>";
			}
			if($second=="s"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Just like in the Koppen classification, tropical climate is based on the average temperature of all months being higher than 18 °C. This basically means that all regions classified as A in the Trewartha classification, are places with warm weather all year round.</p><p>The As subtype is characterized by dry period during the “summer” – this means during the period of longer days and shorter nights. It is a relatively rare subtype and just like the Aw subtype (dry winter), it is sometimes referred to as tropical climate of the savannas.</p><p> In general there are four savanna climate types:<ul><li>clearly distinguishable dry and wet periods of approximately same lengths. Most of precipitation falls during the wet period, during the dry period it rains only very little if at all.</li><li>longer dry period followed by a relatively short wet period. In this case there are typically seven or more months of drought followed by five or less months with rain. This creates a relatively large variability and includes places with almost equal length dry and wet periods (5 and 7 moths), but also places with a very long drought and then very short wet period, which, however, is typical with very intense heavy rains.</li><li>the third type is a relatively long wet period and a shorter dry period, in other words the exact opposite of the previous type. There are at least seven wet months and maximum of five dry months.</li><li>the fourth type is very rare and is characterized by dry periods, during which however, it can sometimes also rain and so the difference between the dry and wet period is smaller than in the previous three types.</li></ul></p>";
			}
		}
		if($first=="B"){
			if($second=="S"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>The climate types from the group B of the classification are arid (deserts) and semi-arid areas. The BS subtype is sometimes referred to as the climate of the steppes and in comparison to the BW subtype, it is slightly less dry. It can also be defined as area, where the precipitation is lower than potential evapotranspiration, but the difference is not very big. For classification it is first necessary to determine the precipitation threshold based on the average annual temperature and annual distribution and amount of precipitation. In Koppen classification there are two subtypes of BS, the BSh and BSk. BSh is a hot semi-arid climate, BSk is cold semi-arid climate.</p><p>Semi-arid regions are characterized by bushes and grasslands. In case of the hot semi-arid zones, the summer tends to be extremely hot and winters very mild. Snow is extremely rare. Hot semi-arid climate can be found for example in Western Africa, India, parts of Mexico and some states in the U.S. The cold semi-arid zones are usually located more inland and also have relatively warm summers, but colder than the hot semi-arid zones. Winters are colder and often accompanied by snow. This type is also common for areas at higher altitudes and typical is bigger day-night temperature variation. In general it can be said that areas with cold semi-arid climate that are at higher altitudes tend to have drier summers and more precipitation during spring, autumn and winter. Cold semi-arid regions can be found for example in Central Asia, on the West of the U.S., in some parts of Northern Australia, parts of Spain or Southern part of South America.</p>";
			}
			if($second=="W"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Desert, in other words arid, climate is characterized by extremely small precipitation. In Koppen classification it is further subdivided into types h and k. Deserts can be both hot and cold. It could be said that it basically includes areas, which although are not classified as polar regions, are in some aspects quite similar to them, especially by the almost complete absence of vegetation due to water insufficiency.</p><p>Arid regions have annual precipitation of less than 250 mm, but often much less and there are even areas where there are years with absolutely no rain at all.</p><p>Hot desert climate is most often found at around 30 ° latitude, bordering the subtropical zones. There is minimal cloudiness all year round and temperatures are extremely high, often above 40 °C, in some places even more than 45 °C. It in fact includes the warmest places on Earth, however, this is not to say it is not cold here at all. Due to the minimal cloud cover, it cools down very rapidly after sunset and sometimes even freezes in the early morning hours, just before sunrise. Hot deserts are found especially in Northern Africa, the most well-known is the largest desert on Earth, the Sahara desert. Then there are also hot deserts in the Middle East, Central and West Australia or some parts of Southwest U.S. and Southwest Africa.</p><p>Cold desert regions have a slightly misleading name because the word “cold” must be considered in context – summers here are still extremely hot and dry. In the winter however, temperatures drop quite significantly, sometimes well below freezing point. Cold desert regions are often found in places with higher altitude. Regions with this climate type are for example in Central Asia, the most well-known is the Mongolian Gobi desert.</p>";
			}
		}
		if($first=="C"){
			if($second=="s"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Subtropical in the Koppen and Trewartha classification is defined as a region, where at least eight months has an average temperature of more than 10 °C, but the coldest month has an average temperature of less than 18 °C. The individual subtypes then differ in their annual precipitation distribution. In Trewartha classification there is a condition of maximum annual precipitation of 900 mm.</p><p>In general the subtropical climate is usually found in areas polewards from the tropical zones. It can also be found inside tropical zones at places with higher altitude. Typical are for example citrus trees or palm trees.</p><p>The Cs subtype in the Trewartha classification represents a subtropical climate with dry summer, sometimes referred to as the Mediterranean climate. It is characterized by hot and dry summers and mild winters, during which most of the precipitation is observed. High pressure is the dominating factor in the summer, when precipitation is very small and if there is some, it is usually in the form of very short, but intense thunderstorms. In the winter it can sometimes snow, especially at higher altitudes, but in general the winters are mild.</p><p>This climate type is most common in western coasts of continents in the middle latitudes, for example it includes areas on the West coast of the U.S. such as California, Southern Europe or Southwest tip of Australia.</p>";
			}
			if($second=="w"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Subtropical in the Koppen and Trewartha classification is defined as a region, where at least eight months has an average temperature of more than 10 °C, but the coldest month has an average temperature of less than 18 °C. The individual subtypes then differ in their annual precipitation distribution. In Trewartha classification there is a condition of maximum annual precipitation of 900 mm.</p><p>In general the subtropical climate is usually found in areas polewards from the tropical zones. It can also be found inside tropical zones at places with higher altitude. Typical are for example citrus trees or palm trees.</p><p>The Cw subtype is characterized by more precipitation in the summer and relatively dry and mild winter. In regions with this climate type, which are affected by the monsoons, there is often very intense precipitation during the summer and in contrast very profound dry periods during winter, which can potentially have quite negative effects for agriculture. Most of the summer precipitation falls during shorter intense thunderstorms.</p><p>Subtropical climate with dry winter can be found for example in some parts of Central South America, in the center of Southern part of Africa and also in some parts of India and Southeast Asia.</p>";
			}
			if($second=="f"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Subtropical in the Koppen and Trewartha classification is defined as a region, where at least eight months has an average temperature of more than 10 °C, but the coldest month has an average temperature of less than 18 °C. The individual subtypes then differ in their annual precipitation distribution. In Trewartha classification there is a condition of maximum annual precipitation of 900 mm.</p><p>In general the subtropical climate is usually found in areas polewards from the tropical zones. It can also be found inside tropical zones at places with higher altitude. Typical are for example citrus trees or palm trees.</p><p>The Cf subtype is characterized by precipitation evenly distributed throughout the year. </p><p>It can be found for example in Eastern part of South America, in large areas in Southeast U.S., Northern Italy, the Southwest tip of Russia, in Southeast Asia, some parts of Japan or East coast of Australia.</p>";
			}
		}
		if($first=="D"){
			if($second=="O"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>In the Trewartha classification, the category D is like in the Koppen classification, assigned to regions in the temperate zone. The criterion for this type of climate is four to seven months with average temperature of more than 10 °C. In general it is further subdivided into two subtypes: oceanic and continental. These differ in their average temperature of the coldest month.</p><p>Temperate climate is characterized by four distinct seasons and there are substantial differences between the temperatures in the summer and in the winter, but the absolute values are not so extreme and the change is gradual.</p><p>In case of the oceanic type, winter is milder than that of the continental type and the temperature of the coldest month usually above 0 °C. The weather conditions are to a large extent influenced by the nearby ocean, which causes smaller temperature variation between the individual seasons. Typical is Western wind and so we often find this type of climate on Western coasts of continents, such as for example in Western Europe or Western coast of the U.S., in latitudes between 40 and 60 ° in America and 40 and 65 ° in Europe. These regions tend to be densely inhabited.</p>";
			}
			if($second=="C"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>In the Trewartha classification, the category D is like in the Koppen classification, assigned to regions in the temperate zone. The criterion for this type of climate is four to seven months with average temperature of more than 10 °C. In general it is further subdivided into two subtypes: oceanic and continental. These differ in their average temperature of the coldest month.</p><p>Temperate climate is characterized by four distinct seasons and there are substantial differences between the temperatures in the summer and in the winter, but the absolute values are not so extreme and the change is gradual.</p><p>The continental subtype is typical for regions located more inland and this also means the temperature variation is larger than that in case of the oceanic subtype. The coldest month has an average temperature of less than 0 °C. Summers are usually warmer, while winters colder with snow being quite common.</p><p>Continental D climate type can be found for example in parts of Northeast U.S., Central and Eastern Europe and usually these areas are quite densely inhabited.</p>";
			}
		}
		if($first=="E"){
			if($second=="O"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Boreal climate is sometimes also referred to as subarctic. It is colder all year round and characterized by a very large temperature amplitude throughout the year, in other words very big difference between temperatures in the summer and in the winter. Winters often experience temperatures as low as – 30 °C. In the summer on the other hand, values of even more than 30 °C are not uncommon. These warm summers however only last very short, not more than three months. The criterion that is used for boreal climate classification is at least one month with average temperature of at least 10 °C. Five to seven months, the average temperature is below freezing point and the soil is frozen, in some areas permanently (permafrost).</p><p>Boreal climate usually has relatively little annual precipitation not exceeding 400 mm. Places further from the coast tend to have most of precipitation in the winter months,  in areas near the coast most precipitation is usually observed during autumn.</p><p>Typical vegetation includes conifers, ferns and several other tree species, which can withstand the very low temperatures in the winter. Even though the overall species diversity is smaller, vegetation is abundant, which can be seen for example in large forested areas referred to as taiga. In fact, taigas are the absolute largest tree covered regions on Earth and can be found especially in Russia and Canada.</p><p>Just like in case of the temperate climate C, the Trewartha classification divides this category into two subtypes – the oceanic and continental. The oceanic type (EO) does not have such extreme differences between individual seasons. Boreal oceanic climate can be found for example in some parts of Scotland, Kamchatka and in regions closer to the coast in Canada, Siberia, Alaska and Northern Europe.</p>";
			}
			if($second=="C"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Boreal climate is sometimes also referred to as subarctic. It is colder all year round and characterized by very large temperature amplitude throughout the year, in other words very big difference between temperatures in the summer and in the winter. Winters often experience temperatures as low as – 30 °C. In the summer on the other hand, values of even more than 30 °C are not uncommon. These warm summers however only last very short, not more than three months. The criterion that is used for boreal climate classification is at least one month with average temperature of at least 10 °C. Five to seven months, the average temperature is below freezing point and the soil is frozen, in some areas permanently (permafrost).</p><p>Boreal climate usually has relatively little annual precipitation not exceeding 400 mm. Places further from the coast tend to have most of precipitation in the winter months,  in areas near the coast most precipitation is usually observed during autumn.</p><p>Typical vegetation includes conifers, ferns and several other tree species, which can withstand the very low temperatures in the winter. Even though the overall species diversity is smaller, vegetation is abundant, which can be seen for example in large forested areas referred to as taiga. In fact, taigas are the absolute largest tree covered regions on Earth and can be found especially in Russia and Canada.</p><p>Just like in case of the temperate climate C, the Trewartha classification divides this category into two subtypes – the oceanic and continental. The continental subtype (EC) is characterized by extreme differences between temperatures in the summer and in the winter. An example of this is the Russian Oymyakon, where in July temperatures can be above 30 °C, while in January, it can even drop below -50 °C, absolute record being around -70 °C! In general therefore we can find boreal continental climate in large inland areas on the Northern hemisphere in Canada and Russia.</p>";
			}
		}
		if($first=="F"){
			if($second=="T"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Polar climate includes the coldest places on Earth. The general criterion for this classification is average monthly temperature of all months less than 10 °C. In Trewartha classification this type is further subdivided into two subtypes – polar tundra climate and ice cap climate.</p><p>Polar regions are characterized by absence of the summer as we know it. Each month has an average temperature of less than 10 °C, quite often much less. In total, the Polar Regions include approximately 20 % of land. Summers are characterized by very long days and the opposite is true for the winter, when it is dark for most part of the day if not the entire day.</p><p>The tundra subtype is slightly warmer than the ice cap type. At least one month in the year, the average temperature is above freezing point. For trees however, this is still too cold, so the vegetation here is usually small bushes or ferns. Precipitation tends to be very small.</p><p>Tundra can be found for example in the northernmost part of Canada, West and North coast of Alaska, the northern tip of Europe and Russia, coastal areas in Greenland and also a very narrow strip along the coast of Antarctica.</p>";
			}
			if($second=="I"){
				$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><span style='font-size:1.6em'>Modern Trewartha classification: ".$climate." (".$climate_name_trewartha.")</span><br><br><p>Polar climate includes the coldest places on Earth. The general criterion for this classification is average monthly temperature of all months less than 10 °C. In Trewartha classification this type is further subdivided into two subtypes – polar tundra climate and ice cap climate.</p><p> Polar regions are characterized by absence of the summer as we know it. Each month has an average temperature of less than 10 °C, in case of the ice-cap subtype, it is even less than 0 °C.</p><p>Ice-cap polar climate is characterized by very low temperatures all year round and many places lie behind the arctic circle, meaning there is the polar day and polar night. Average temperature in the entire year is below freezing point and the areas are permanently covered with snow and ice. Only very few animal species live here, which rely on gathering food from the see (for example polar bears or penguins). No plants can grow here.</p><p>Ice cap polar climate includes areas in most of Antarctica and inland Greenland. The absolute minimum temperature recorded on Earth was recorded in the polar station in Vostok, Antarctica (-89.2 °C). Satellite images however suggest that in some parts of this coldest continent, the temperatures are during specific weather conditions even lower.</p>";
			}
		}
		if($third=="i"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>extremely hot summer: warmest month with average temperature above 35 °C</li>";
		}
		if($third=="h"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>very hot summer: warmest month with average temperature between 28 and 35 °C</li>";
		}
		if($third=="a"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>hot summer: warmest month with average temperature between 23 and 28 °C</li>";
		}
		if($third=="b"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>warm summer: warmest month with average temperature between 18 and 23 °C</li>";
		}
		if($third=="l"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>mild summer: warmest month with average temperature between 10 and 18 °C</li>";
		}
		if($third=="k"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>cool summer: warmest month with average temperature between 0 and 10 °C</li>";
		}
		if($third=="o"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>cold summer: warmest month with average temperature between 0 and -10 °C</li>";
		}
		if($third=="c"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>very cold summer: warmest month with average temperature between -10 and -25 °C</li>";
		}
		if($third=="d"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>extremely cold summer: warmest month with average temperature between -25 and -40 °C</li>";
		}
		if($third=="e"){
			$Trewartha_desc = $Trewartha_desc."<ul><li>freezing summer: warmest month with average temperature below -40 °C</li>";
		}
		
		if($fourth=="i"){
			$Trewartha_desc = $Trewartha_desc."<li>extremely hot winter: coldest month with average temperature above 35 °C</li></ul></div>";
		}
		if($fourth=="h"){
			$Trewartha_desc = $Trewartha_desc."<li>very hot winter: coldest month with average temperature between 28 and 35 °C</li></ul></div>";
		}
		if($fourth=="a"){
			$Trewartha_desc = $Trewartha_desc."<li>hot winter: coldest month with average temperature between 23 and 28 °C</li></ul></div>";
		}
		if($fourth=="b"){
			$Trewartha_desc = $Trewartha_desc."<li>warm winter: coldest month with average temperature between 18 and 23 °C</li></ul></div>";
		}
		if($fourth=="l"){
			$Trewartha_desc = $Trewartha_desc."<li>mild winter: coldest month with average temperature between 10 and 18 °C</li></ul></div>";
		}
		if($fourth=="k"){
			$Trewartha_desc = $Trewartha_desc."<li>cool winter: coldest month with average temperature between 0 and 10 °C</li></ul></div>";
		}
		if($fourth=="o"){
			$Trewartha_desc = $Trewartha_desc."<li>cold winter: coldest month with average temperature between -10 and 0 °C</li></ul></div>";
		}
		if($fourth=="c"){
			$Trewartha_desc = $Trewartha_desc."<li>very cold winter: coldest month with average temperature between -10 and -25 °C</li></ul></div>";
		}
		if($fourth=="d"){
			$Trewartha_desc = $Trewartha_desc."<li>extremely cold winter: coldest month with average temperature between -25 and -40 °C</li></ul></div>";
		}
		if($fourth=="e"){
			$Trewartha_desc = $Trewartha_desc."<li>freezing winter: coldest month with average temperature below -40 °C</li></ul></div>";
		}
	}
?>
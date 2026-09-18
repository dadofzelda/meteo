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
			$climate_name = "Ekvatoriální podnebí";
			$climate_color = "#C8E6C9";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>A</b> – průměrná teplota nejchladnějšího měsíce vyšší než 18 °C</li><li><b>f</b> – srážky každý měsíc minimálně 60 mm</li></ul><p>Tento typ klimatu, někdy také označovaný jako podnebí tropických deštných pralesů, je charakteristický pro oblasti poblíž rovníku, nejčastěji v rozsahu 5 až 10 ° od něj. Obvykle jsou to území porostlá deštnými pralesy. Klima tropických pralesů je typické absencí období sucha, všechny měsíce mívají průměrnou hodnotu srážek vyšší než 60 mm. Rovněž zde nerozlišujeme jednotlivá roční období. Rozložení teplot i srážek bývá po většinu roku víceméně stejná a to samé platí vzhledem k poloze u rovníku i o délce dne a noci. Větší rozdíly můžou být zaznamenávány mezi dnem a nocí. Po celý rok jsou denní teploty velmi vysoké, velmi často přesahující 30 °C. Zaznamenávána zde bývá velká bouřková aktivita a nejintenzivnější déšť nejčastěji v pozdním odpoledni a večer.</p><p>Nejčastěji se s tímto typem klimatu setkáme v jihovýchodní Asii, střední Africe a Jižní Americe. Tropické mikroklima se ale vyskytuje i v některých dalších konkrétních oblastech, jako například v severní části Queenslandu v Austrálii nebo ve východním cípu Madagaskaru.</p></td><td width='500px'><img src='/images/climate/af.png' width='500px'></td></tr></table></div>";
			break;
		case "Am":
			$climate_name = "Tropické monzunové podnebí";
			$climate_color = "#4CAF50";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>A</b> – průměrná teplota nejchladnějšího měsíce vyšší než 18 °C</li><li><b>m</b> – srážky v nejsušší měsíc méně než 60 mm, ale rovné nebo větší než 100-0,04r (r = průměrné celkové roční srážky v mm)</li></ul><p>Tropické monzunové podnebí je typické střídáním období sucha a období dešťů, podobně jako je tomu v tropickém klimatu savan. Během roku se zde rozsahy denních teplot příliš nemění a nejsušším měsícem bývá většinou v měsíci nebo krátce po zimním slunovratu na dané polokouli.</p><p>Nejčastěji se s tímto typem podnebí setkáváme v jižní a střední Americe, dále pak také v některých částech jižní a jihovýchodní Asie, západní a střední Africe, Karibiku a Severní Americe.</p><p>Nejvýznamnější je pro tento typ podnebí monzunové proudění. Jedná se o sezonní změnu ve směru větru. V Asii fouká v letním období vítr směrem od oceánu na pevninu, v zimě pak je to naopak z pevniny směrem k oceánu.</p><p>Teploty jsou po celý rok vysoké a srážky sice můžou v některé měsíce být nižší, celkově však ročně často převyšují celkový úhrn oblastí ekvatoriálního klimatu (Af). Období sucha bývá výrazně kratší než období dešťů.</p></td><td width='500px'><img src='/images/climate/am.png' width='500px'></td></tr></table></div>";
			break;
		case "Aw":
			$climate_name = "Tropické klima savan se suchou zimou";
			$climate_color = "#2E7D32";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>A</b> – průměrná teplota nejchladnějšího měsíce vyšší než 18 °C</li><li><b>w</b> – srážky v nejsušší měsíc menší než 60 mm a menší než 100-0,04r (r = průměrné celkové roční srážky v mm). Období sucha je v době „zimy“ při kratších dnech a delších nocích.</li></ul><p>Tropické klima savan je charakteristické výraznou změnou v průběhu roku, kdy zaznamenáváme období sucha a období dešťů. Období sucha je delší než v případě tropického monzunového podnebí (Am). Teploty jsou po celý rok vysoké, avšak s většími rozdíly v jejich denním rozsahu v průběhu roku. Celkový srážkový úhrn bývá nižší než v případě ekvatoriálního nebo tropického monzunového podnebí. Období sucha v tomto případě spadá na zimu.</p></td><td width='500px'><img src='/images/climate/aw.png' width='500px'></td></tr></table></div>";
			break;
		case "As":
			$climate_name = "Tropické klima savan se suchým létem";
			$climate_color = "#008C69";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>A</b> – průměrná teplota nejchladnějšího měsíce vyšší než 18 °C</li><li><b>s</b> – srážky v nejsušší měsíc menší než 60 mm a menší než 100-0,04r (r = průměrné celkové roční srážky v mm). Období sucha je v době „léta“ při delších dnech a kratších nocích.</li></ul><p>Tropické klima savan je charakteristické výraznou změnou v průběhu roku, kdy zaznamenáváme období sucha a období dešťů. Období sucha je delší než v případě tropického monzunového podnebí (Am). Teploty jsou po celý rok vysoké, avšak s většími rozdíly v jejich denním rozsahu v průběhu roku. Celkový srážkový úhrn bývá nižší než v případě ekvatoriálního nebo tropického monzunového podnebí. Období sucha v tomto případě spadá na léto.</p></td><td width='500px'><img src='/images/climate/as.png' width='500px'></td></tr></table></div>";
			break;
		case "BSh":
			$climate_name = "Teplé stepní klima";
			$climate_color = "#FFD54F";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>B</b> – 70 % nebo více z celkových ročních srážek spadá do letní poloviny roku a r (r = průměrné celkové roční srážky v mm) je méně než 20t+280 (t = průměrná roční teplota ve °C), nebo 70 % nebo více z celkových ročních srážek spadá do zimní poloviny roku a r je méně než 20t, nebo žádná polovina roku nemá 70 % nebo více ročních srážek a r menší než 20t+140. Za letní polovinu roku je na severní polokouli považováno období od dubna do září, na jižní od října do března.</li><li><b>S</b> – r je menší než polovina horního limitu klasifikace typu B</li><li><b>h</b> - t je rovné nebo větší než 18 °C</li></ul><p>S podnebím typu teplého stepního klimatu se setkáváme v tropických a subtropických oblastech. Léta bývají někdy až extrémně horká a zimy mírné. Typické oblasti s tímto klimatem zahrnují například západní Afriku, Indii, část Mexika nebo některé oblasti na jihu USA. Jedná se většinou o území hluboko ve vnitrozemí, navazující na tropické pouštní klima.</p><p>Typické jsou velké teplotní výkyvy a relativně nízká míra srážek.</p></td><td width='500px'><img src='/images/climate/bsh.png' width='500px'></td></tr></table></div>";
			break;
		case "BSk":
			$climate_name = "Chladné stepní klima";
			$climate_color = "#FF8F00";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>B</b> – 70 % nebo více z celkových ročních srážek spadá do letní poloviny roku a r (r = průměrné celkové roční srážky v mm) je méně než 20t+280 (t = průměrná roční teplota ve °C), nebo 70 % nebo více z celkových ročních srážek spadá do zimní poloviny roku a r je méně než 20t, nebo žádná polovina roku nemá 70 % nebo více ročních srážek a r menší než 20t+140. Za letní polovinu roku je na severní polokouli považováno období od dubna do září, na jižní od října do března.</li><li><b>S</b> – r je menší než polovina horního limitu klasifikace typu B</li><li><b>k</b> - t je nižší než 18 °C</li></ul><p>Chladné stepní klima se vyskytuje nejčastěji v mírném pásu v kontinentálním vnitrozemí a relativně daleko od moře. Léta sice bývají teplá, ale chladnější než v případě teplého stepního klimatu (Bsh). Rozdíl je pak především v období zimy, kdy v tomto typu můžou teploty klesat velmi nízko. Teplotní výkyvy mezi dnem a nocí jsou výrazné, někdy i více než 20 °C.</p><p>Oblasti vyšších nadmořských výšek s tímto typem klimatu mívají suché zimy a vlhká léta, naopak oblasti položené níže spíše suchá léta a vlhké zimy.</p><p>Nejčastěji se s tímto typem klimatu setkáváme v Asii a Severní Americe, patří sem ale i například některé oblasti ve Španělsku, částech Jižní Ameriky, jižní Austrálie a Nový Zéland.</p></td><td width='500px'><img src='/images/climate/bsk.png' width='500px'></td></tr></table></div>";
			break;
		case "BWh":
			$climate_name = "Teplé pouštní klima";
			$climate_color = "#FFF9C4";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>B</b> – 70 % nebo více z celkových ročních srážek spadá do letní poloviny roku a r (r = průměrné celkové roční srážky v mm) je méně než 20t+280 (t = průměrná roční teplota ve °C), nebo 70 % nebo více z celkových ročních srážek spadá do zimní poloviny roku a r je méně než 20t, nebo žádná polovina roku nemá 70 % nebo více ročních srážek a r menší než 20t+140. Za letní polovinu roku je na severní polokouli považováno období od dubna do září, na jižní od října do března.</li><li><b>W</b> – r je nižší než polovina horní hranice pro klasifikaci B</li><li><b>h</b> - t je rovné nebo vyšší než 18 °C</li></ul><p>Pro pouštní klima jsou typické extrémně nízké hodnoty srážkového úhrnu. Do pouštního klimatu obecně spadají oblasti, které neřadíme mezi polární klima, ale kde je srážek tak málo, že zde v podstatě není vůbec žádná vegetace.</p><p>Ročně v těchto oblastech spadne většinou maximálně 250 mm, často však i mnohem méně a výjimkou nejsou ani oblasti, kde některé roky neprší vůbec. Výjimečně řadíme mezi pouštní klima i oblasti, kde sice spadne více než 250 mm srážek ročně, ale více vody se ztratí procesem evapotranspirace (například některé oblasti v Arizoně).</p><p>Teplé pouštní klima se kromě velmi nízkých srážek vyznačuje dále také velmi vysokými teplotami v průběhu roku. Tyto oblasti najdeme nejčastěji kolem 30 ° severní nebo jižní šířky. Teploty jsou vysoké a některé měsíce extrémní, 40 °C není výjimkou a v létě můžou dosahovat i více než 45 °C. Protože se však většinou jedná o oblasti hluboko ve vnitrozemí je potřeba počítat s velkými denními výkyvy a v noci může v těchto oblastech často i mrznout. Právě místa s BWh typem klimatu jsou těmi, kde byly naměřeny historicky nejvyšší teploty na Zemi (Údolí smrti v USA, území v Libyi atd.).</p><p>Nejznámější oblastí s tímto typem klimatu je největší světová poušť, africká Sahara. Dále toto podnebí panuje například v Libyjské poušti, Nubijské poušti, oblastech jižní Afriky (Namibijská poušť, poušť Kalahari), v některých částech Mexika, v rozsáhlých oblastech především centrální Austrálie, ale i na několika místech na Předním Východě.</p><p>S velmi nízkou mírou srážek souvisí také velmi malá oblačnost, kdy v některých oblastech bývá méně než 30 dní v roce, kdy vůbec nějakou oblačnost pozorovat lze.</p></td><td width='500px'><img src='/images/climate/bwh.png' width='500px'></td></tr></table></div>";
			break;
		case "BWk":
			$climate_name = "Chladné pouštní klima";
			$climate_color = "#FFEB3B";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>B</b> – 70 % nebo více z celkových ročních srážek spadá do letní poloviny roku a r (r = průměrné celkové roční srážky v mm) je méně než 20t+280 (t = průměrná roční teplota ve °C), nebo 70 % nebo více z celkových ročních srážek spadá do zimní poloviny roku a r je méně než 20t, nebo žádná polovina roku nemá 70 % nebo více ročních srážek a r menší než 20t+140. Za letní polovinu roku je na severní polokouli považováno období od dubna do září, na jižní od října do března.</li><li><b>W</b> – je nižší než polovina horní hranice pro klasifikaci B</li><li><b>k</b> - t je nižší než 18 °C</li></ul><p>Pro pouštní klima jsou typické extrémně nízké hodnoty srážkového úhrnu. Do pouštního klimatu obecně spadají oblasti, které neřadíme mezi polární klima, ale kde je srážek tak málo, že zde v podstatě není vůbec žádná vegetace.</p><p>Ročně v těchto oblastech spadne většinou maximálně 250 mm, často však i mnohem méně a výjimkou nejsou ani oblasti, kde některé roky neprší vůbec. Výjimečně řadíme mezi pouštní klima i oblasti, kde sice spadne více než 250 mm srážek ročně, ale více vody se ztratí procesem evapotranspirace (například některé oblasti v Arizoně). </p><p>Ačkoliv má tento typ podnebí v názvu slovo „chladné“, musí se tento termín brát relativně. V létě zde totiž může rovněž být velmi a někdy až extrémně horko (teploty ale nedosahují tak vysokých hodnot jako v případě teplého pouštního klimatu BWh). Na rozdíl právě od teplého pouštního klimatu BWh tu ale bývají i chladné periody, někdy dokonce i extrémně chladné s teplotami hluboko pod bodem mrazu.</p><p>V podstatě téměř všechna území chladného pouštního klimatu se nachází v Asii v mírném pásu. Většinou jsou to oblasti v závětří vyšších pohoří, často ve vyšších nadmořských výškách. Nejznámějším místem je pravděpodobně asijská poušť Gobi na území Mongolska. V létě jsou tu teploty velmi vysoké, v zimě naopak extrémně nízké, roční rozsah teplot bývá extrémní, to však může platit i o denním rozsahu. Kromě centrální Asie (Mongolsko, Čína, Indie) se s BWk klimatem setkáme také v některých částech na západě USA a v Jižní Americe.</p></td><td width='500px'><img src='/images/climate/bwk.png' width='500px'></td></tr></table></div>";
			break;
		case "Cfa":
			$climate_name = "Vlhké subtropické klima";
			$climate_color = "#FFCDD2";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku a nevyhovují podmínkám typů s nebo w</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Typ podnebí Cfa je charakteristický pro území na východních pobřežích kontinentů, převážně kolem 20. a 30. rovnoběžky. Průměrná teplota v nejteplejší měsíc je vyšší než 22 °C a v průběhu roku se průměrné měsíční teploty pohybují mezi -3 a 18 °C. V zimě se však můžou teploty v některých případech dostat i pod bod mrazu.</p><p>Srážky bývají relativně rovnoměrně rozložené do celého roku. Vlhkost bývá nejvyšší v letní polovině roku, kdy se zdejší podmínky často podobají vlhkému tropickému klimatu. Roční úhrn srážek může být velmi variabilní, nejčastěji však mezi 650 a 2500 mm. V létě jsou časté bouřky.</p><p>Cfa podnebí najdeme například na jihovýchodě USA, na severu Argentiny, Uruguaye, v jižní části Brazílie, jižním Japonsku nebo na jihu Číny.</p></td><td width='500px'><img src='/images/climate/cfa.png' width='500px'></td></tr></table></div>";
			break;
		case "Cfb":
			$climate_name = "Subtropické oceánské klima";
			$climate_color = "#E57373";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku a nevyhovují podmínkám typů s nebo w</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Podnební typ Cfb je oceánickým klimatem s mírnou zimou a rovnoměrným rozložením srážkového úhrnu. Průměrná teplota všech měsíců je nižší než 22 °C, čímž se odlišuje od podnebního typu Cfa. Zároveň je zde alespoň ve čtyři měsíce průměrná teplota vyšší než 10 °C.</p><p>Podobně jako srážky jsou i teploty poměrně stálé a rozdíly mezi dnem a nocí nebývají příliš výrazné. Srážky v průběhu celého roku bývají relativně časté, ale málo intenzivní a neobvyklé nejsou periody s mnoha dny se srážkami po sobě. V zimě a na podzim jsou časté mlhy a setkáváme se zde i s bouřkami.</p><p>Zimy nejsou nijak tuhé (teplota se jen velmi výjimečně dostává pod bod mrazu a nikdy ne výrazně) a léta příjemně teplá. Frontální aktivita způsobuje relativně velkou oblačnost a vysokou vlhkost na podzim, v zimě a na jaře.</p><p>Celkový roční úhrn srážek se pohybuje nejčastěji v rozmezí 500 a 2500 mm a v zimě se můžou ojediněle vyskytovat srážky sněhové.</p><p>Mírné oceánské podnebí Cfb najdeme například ve Velké Británii, v západní části Francie, Německa, na západním pobřeží Evropy a severní části Španělska, v některých oblastech Jižní Ameriky (na severním a jižním cípu), v jihovýchodní Austrálii a na Novém Zélandu.</p></td><td width='500px'><img src='/images/climate/cfb.png' width='500px'></td></tr></table></div>";
			break;
		case "Cfc":
			$climate_name = "Subpolární oceánské klima";
			$climate_color = "#D32F2F";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku a nevyhovují podmínkám typů s nebo w</li><li><b>c</b> - teplota jednoho až tří měsíců je vyšší nebo rovna 10 °C, ale nejteplejší měsíc má teplotu nižší než 22 °C</li></ul><p>Subpolární oceánské podnebí se od mírného oceánického podnebí Cfb liší v prvé řadě tím, že je zde celkově chladněji. Stejně jako v případě Cfb je tu průměrná měsíční teplota ve všechny měsíce vždy nižší než 22 °C, v tomto případě však je průměrná měsíční teplota vyšší než 10 °C pouze v jeden až tři měsíce.</p></p>Denní výkyvy teplot jsou velmi malé a to samé platí i pro rozsah v rámci roku. Srážky jsou rovnoměrně rozložené do všech měsíců.</p><p>V létě se teploty jen málokdy vyšplhají nad 20 °C a v noci se pohybují kolem 5 až 10 °C. V zimním období se může poměrně výrazně ochladit a ničím výjimečným nejsou teploty v rozmezí -5 a -10 °C se zimními denními maximy do 10 °C.</p><p>Cfc typ je relativně vzácný, nejčastější se s ním setkáváme na ostrovech a pobřežích v blízkosti polárního kruhu. Jmenovat můžeme například Island, Aleutské ostrovy, Faerské ostrovy, Sheltlandy a Aucklandské ostrovy.</p></td><td width='500px'><img src='/images/climate/cfc.png' width='500px'></td></tr></table></div>";
			break;
		case "Csa":
			$climate_name = "Vnitrozemské středozemní klima";
			$climate_color = "#F8BBD0";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>s</b> – srážky v nejsušší měsíc během letní poloviny roku jsou nižší než 30 mm a nižší než jedna třetina měsíce s nejvyššími srážkami v zimní polovině roku.</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Pro podnebí typu Cs je charakteristické suché léto a vlhká zima. V případě Csa je navíc typická relativně velmi teplá perioda v době léta. Průměrná teplota nejteplejšího měsíce v roce je vyšší než 22 °C. Nejdeštivější měsíc v zimě má většinou přibližně třikrát vyšší úhrn srážek než nejsušší měsíc, který bývá v létě a většinou do 30 mm. Léta jsou tedy suchá, s relativně malou oblačností a často delšími periodami zcela bez deště. Zimy naopak jsou deštivé a velmi ojediněle i se sněžením.</p><p>Teploty dosahují v létě často vysokých hodnot mezi 30 a 40 °C, v zimě jen málokdy méně než 0 °C.</p><p>Někdy bývá vnitrozemské středozemní klima (Csa) také označováno jako horké středozemní klima. Vyšší teploty souvisí právě s umístěním většinou více ve vnitrozemí. Vnitrozemské středozemní klima je častější než ostatní Cs typy a představuje to, co si většina představí pod pojmem „středozemní klima“.</p><p>Oblasti s vnitrozemským středozemním klimatem zahrnují například okolí Středozemního moře v Evropě, jihozápad Austrálie, jihozápadní část jižní Afriky, některá území ve střední Asii a ve vnitrozemí severní Kalifornie. Řadíme sem ale i některé mikrooblasti, například španělskou metropoli Madrid, italský Řím, americké Los Angeles nebo australský Perth.</p></td><td width='500px'><img src='/images/climate/csa.png' width='500px'></td></tr></table></div>";
			break;
		case "Csb":
			$climate_name = "Pobřežní středozemní klima";
			$climate_color = "#F06292";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>s</b> – srážky v nejsušší měsíc během letní poloviny roku jsou nižší než 30 mm a nižší než jedna třetina měsíce s nejvyššími srážkami v zimní polovině roku.</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Pro podnebí typu Cs je charakteristické suché léto a vlhká zima. Letní teploty však nedosahují tak vysokých hodnot jako v případě typu Csa. Průměrná teplota nejteplejšího měsíce není vyšší než 22 °C.</p><p>V porovnání s vnitrozemským středozemním klimatem Csa je Csb typ méně častý.</p><p>Srážky se vyskytují především v zimě, které jsou mírné, avšak s možností dočasného výraznějšího poklesu teplot. V době léta tu bývá nejen sucho, ale také příjemně teplo a slunečno a to představuje například vysoké riziko lesního požárů.</p><p>Do kategorie Csb řadíme některé oblasti na západním pobřeží USA (například San Francisco), na jihozápadě Jižní Ameriky, části Španělska a Portugalska a Turecka nebo na jihu Afriky (Kapské město).</p></td><td width='500px'><img src='/images/climate/csb.png' width='500px'></td></tr></table></div>";
			break;
		case "Cwa":
			$climate_name = "Vlhké subtropické podnebí se suchou zimou";
			$climate_color = "#FF8A65";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>w</b> – srážky v nejsušší měsíc zimní poloviny roku jsou nižší než jedna desetina množství měsíce s nejvyššími srážkami v letní polovině roku.</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Podnebí typu Cw se obecně vyznačují tím, že srážky se vyskytují především v době léta. Teplota nejteplejšího měsíce je vyšší než 10 °C a nejchladnější měsíc má teplotu v rozmezí -3 až 18 °C. V létě je teplota v nejteplejší měsíc vyšší než 22 °C.</p><p>Léta jsou tedy vlhká a teplá, zimy mírné a suché, často s delšími periodami bez srážek. Obzvláště výrazný srážkový rozdíl mezi létem a zimou je například v oblastech ovlivněných monzuny, například v některých částech Jihovýchodní Asie. Většina letních srážek spadne během bouřek a intenzivnějších dešťů.</p><p>Cwa podnebí najdeme v rozsáhlé míře ve vnitrozemí střední a východní Afriky (Angola, severovýchod Zimbabwe, oblasti Mozambiku, jižní Kongo, jihozápadní Tanzanie, Malawi, Zambie), dále také v částech Jižní Ameriky v blízkosti And v severozápadní Argentině.</p></td><td width='500px'><img src='/images/climate/cwa.png' width='500px'></td></tr></table></div>";
			break;
		case "Cwb":
			$climate_name = "Subtropické horské podnebí";
			$climate_color = "#E64A19";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>C</b> – teplota nejteplejšího měsíce je rovna nebo vyšší než 10 °C a teplota nejchladnějšího měsíce je nižší než 18 °C, ale vyšší než -3 °C</li><li><b>w</b> – srážky v nejsušší měsíc zimní poloviny roku jsou nižší než jedna desetina množství měsíce s nejvyššími srážkami v letní polovině roku.</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Podnebí typu Cw se obecně vyznačují tím, že srážky se vyskytují především v době léta. Teplota nejteplejšího měsíce je vyšší než 10 °C a nejchladnější měsíc má teplotu v rozmezí -3 až 18 °C. Léta jsou mírnější než v případě typu Cwa, teplota čtyř nejteplejších měsíců je sice vyšší než 10 °C, ale nikdy se nepohybuje nad 22 °C.</p><p>Subtropické horské podnebí je v podstatě horskou variantou oceánického klimatu (typ Cf), srážky však nejsou rozloženy rovnoměrně, ale převažují v letní část roku. Jedná se tedy většinou o oblasti vyšší nadmořské výšky, často také jako mikrooblasti v rámci jinak tropických území, podnebí by zde tedy bylo tropické, ale rozdíl je dán právě nadmořskou výškou.</p><p>Teploty v létě nedosahují tak vysokých hodnot jako v případě Cwa, zimy však nejsou nijak tuhé a sníh jen ojedinělý.</p><p>S Cwb typem klimatu se setkáváme například ve střední Americe (Mexico City), v částech východní, jižní a jihovýchodní Afriky, v blízkosti pohoří Atlas, v některých horských oblastech jižní Evropy a Jihovýchodní Asie, včetně části Himálají, dále také na několika místech v Austrálii, kde jsou teploty mírně vyšší a léta sušší, než je typické pro zbytek území klasifikovaných v této klimatické kategorii (letní maxima se zde ve výjimečných případech dostávají i nad 40 °C).</p></td><td width='500px'><img src='/images/climate/cwb.png' width='500px'></td></tr></table></div>";
			break;
		case "Dfa":
			$climate_name = "Teplé celoročně vlhké kontinentální podnebí";
			$climate_color = "#C5CAE9";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku.</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Tato nejteplejší varianta celoročně vlhkého kontinentálního (mikrotermálního) klimatu je charakteristická právě nejteplejším měsícem s teplotou vyšší než 22 °C. Stejně jako ostatní oblasti typu Df jsou typické rovnoměrně rozložené srážky po celý rok.</p><p>Nejvyšší teploty v létě obvykle dosahují velmi vysokých hodnot v rozmezí 30 až 40 °C, v zimě ale můžou klesat až na hodnoty kolem -10 až -25 °C. Dfa oblasti v Evropě bývají sušší než v případě Dfa oblastí v Severní Americe. Nejteplejším měsícem většinou bývá červenec, někdy srpen. V lednu klesají teploty pod -3 °C.</p><p>Dfa typ klimatu najdeme v některých částech střední a středovýchodní USA (například Chicago, Boston nebo New York City, Pittsburgh), na jihu Ukrajiny, v některých částech Ruska, Číny.</p></td><td width='500px'><img src='/images/climate/dfa.png' width='500px'></td></tr></table></div>";
			break;
		case "Dfb":
			$climate_name = "Mírné celoročně vlhké kontinentální podnebí";
			$climate_color = "#7986CB";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku.</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Podobně jako v případě typu Dfa platí, že srážky jsou u tohoto typu rovnoměrně rozložené po celý rok. Na rozdíl od Dfa je však léto mírnější a nejteplejší měsíc nedosahují průměrné hodnoty nad 22 °C. Nejvyšší letní teploty se pohybují v intervalu kolem 30 až 35 °C, v zimě klesají teploty k -10 až -35 °C.</p><p>Srážkově je sice bohatší léto, ale rozdíl není dostatečně výrazný pro zařazení do alternativní kategorie. Pro léto jsou charakteristické spíše intenzivnější srážky spojené s bouřkami, prokládané obdobími sucha.</p><p>Do kategorie Dfb spadá například území v severní části USA (například Buffalo, Calgary), jižní část Kanady (například Ottawa), jih Ruska (například Vladivostok), západ střední Evropy, jižní část Skandinávie (například Helsinky).</p></td><td width='500px'><img src='/images/climate/dfb.png' width='500px'></td></tr></table></div>";
			break;
		case "Dfc":
			$climate_name = "Subpolární celoročně vlhké podnebí";
			$climate_color = "#3949AB";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku.</li><li><b>c</b> - teplota jednoho až tří měsíců je vyšší nebo rovna 10 °C, ale nejteplejší měsíc má teplotu nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Chladné celoročně vlhké kontinentální podnebí se někdy také označuje jako tzv. boreální podnebí nebo klima tajgy. Je typické dlouhými a většinou velmi chladnými zimami a krátkými, chladnějšími léty. Charakteristické jsou obrovské rozdíly teplot v rámci jednotlivých ročních období. V zimě můžou teploty padat i na hranici kolem -40 °C, v létě naopak šplhat až ke 30 °C. Léta jsou většinou dlouhá pouze tři až čtyři měsíce, ale pro klasifikaci Dfc je nutné, aby alespoň jeden měsíc v roce byla průměrná teplota vyšší než 10 °C. V období zimy je typických pět až sedm po sobě jdoucích měsíců s teplotami pod bodem mrazu. Jaro i podzim jsou relativně krátké a změna teploty velmi rychlá.</p><p>V případě typu Dfc se v průběhu roku nesetkáváme s nějakým výraznějším obdobím sucha nebo dešťů. Srážkově většinou mírně převažuje období léta, nejčastěji formou méně intenzivních delších dešťů, bouřky jsou vzácnější. Roční úhrn srážek je nejčastěji v rozmezí 120 až 500 mm.</p><p>Typ Dfc je zdaleka nejčastějším typem subarktického klimatu a najdeme ho v oblastech kolem 50. až 70. rovnoběžky na severní polokouli (převážná část Sibiře, poloostrov Kamčatka, část Skotska, severní části Skandinávie, na Aljašce, ve velké části Kanady, v severním Mongolsku atd.) a dále v některých mikrooblastech ve vysokých nadmořských výškách (například ve výše položených územích Alp ve Francii, Německu, Švýcarsku, Itálii a Rakousku, v centrálním Rumunsku, některých horských oblastech Turecka, v Pyrenejích, v některých amerických pohořích jako například Rocky Mountains nebo White Mountains, částech Číny a Indie atd.).</p></td><td width='500px'><img src='/images/climate/dfc.png' width='500px'></td></tr></table></div>";
			break;
		case "Dfd":
			$climate_name = "Extrémně chladné subpolární celoročně vlhké podnebí";
			$climate_color = "#283593";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>f</b> – srážky jsou relativně rovnoměrně rozložené v průběhu celého roku.</li><li><b>d</b> - teplota nejchladnějšího měsíce je nižší než -38 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Typ Dfd je extrémním typem subarktického podnebí s teplotou nejchladnějšího měsíce nižší než -38 °C. Přesto zde však může být během velmi krátkého léta velmi teplo a v nejteplejší měsíc dosahuje průměrná teplota hodnoty vyšší než 10 °C, což Dfd klima odlišuje od typického klimatu polárního.</p><p>Srážky jsou rovnoměrně rozložené a většinou se pohybují od 100 do 500 mm za rok a vlhkost bývá celoročně relativně nízká.</p><p>Na rozdíl od Dfc klimatu, který představuje druhý nejrozšířenější typ na Zemi, je Dfd klima relativně vzácné. Oblasti, které sem řadíme, najdeme pouze v některých částech severovýchodní Sibiře.</p></td><td width='500px'><img src='/images/climate/dfd.png' width='500px'></td></tr></table></div>";
			break;
		case "Dsa":
			$climate_name = "Teplé kontinentální podnebí se suchým létem";
			$climate_color = "#BBDEFB";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>s</b> – srážky v nejsušší měsíc během letní poloviny roku jsou nižší než 30 mm a nižší než jedna třetina měsíce s nejvyššími srážkami v zimní polovině roku.</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Pro tento typ je charakteristické velmi teplé léto, kdy teplota nejteplejšího měsíce překračuje hodnotu 22 °C. Zároveň však nejchladnější zimní měsíc není teplota vyšší než -3 °C. V zimě teploty klesají nejníže k hodnotám většinou mezi -5 až -20 °C. V létě se může teplota dostat i nad hodnotu 35 °C a zvyšují se denní amplitudy teplot.</p><p>Léta bývají suchá a teplá. Nejvíce srážek připadá na zimu, případně jaro.</p><p>Tento typ podnebí není příliš častý a většinou ho najdeme v oblastech přilehlých středozemnímu klimatu ve vyšších nadmořských výškách. Konkrétně se jedná například o oblasti na západě USA nebo v částech Mongolska.</p></td><td width='500px'><img src='/images/climate/dsa.png' width='500px'></td></tr></table></div>";
			break;
		case "Dsb":
			$climate_name = "Mírné kontinentální podnebí se suchým létem";
			$climate_color = "#64B5F6";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>s</b> – srážky v nejsušší měsíc během letní poloviny roku jsou nižší než 30 mm a nižší než jedna třetina měsíce s nejvyššími srážkami v zimní polovině roku.</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami. </p><p>Dsb typ se vyznačuje relativně teplými léty, které jsou navíc výrazně sušší než zbylá část roku. Nejvyšší letní teploty se pohybují nejčastěji kolem 30 až 35 °C, v zimě naopak klesají k přibližně -10 až -15 °C. V porovnání s Dsa typem zde je chladněji, žádný měsíc v roce není průměrná teplota vyšší než 22 °C, přesto však alespoň čtyři měsíce v roce je průměrná měsíční teplota nad 10 °C.</p><p>Léta bývají suchá a teplá. Nejvíce srážek připadá na zimu, případně jaro.</p><p>Dsb typ je rozšířenější než teplejší varianta v podobě klimatu Dsa, přesto není příliš častý a oblasti sem spadající se vyskytují především v některých územích na západě USA a dále v Turecku.</p></td><td width='500px'><img src='/images/climate/dsb.png' width='500px'></td></tr></table></div>";
			break;
		case "Dsc":
			$climate_name = "Subpolární kontinentální podnebí se suchým létem";
			$climate_color = "#1E88E5";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>s</b> – srážky v nejsušší měsíc během letní poloviny roku jsou nižší než 30 mm a nižší než jedna třetina měsíce s nejvyššími srážkami v zimní polovině roku.</li><li><b>c</b> - teplota jednoho až tří měsíců je vyšší nebo rovna 10 °C, ale nejteplejší měsíc má teplotu nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Je typické dlouhými a většinou velmi chladnými zimami a krátkými, chladnějšími léty. Charakteristické jsou obrovské rozdíly teplot v rámci jednotlivých ročních období. V zimě můžou teploty padat i na hranici kolem -40 °C, v létě naopak šplhat až ke 30 °C. Léta jsou většinou dlouhá pouze tři až čtyři měsíce, ale pro klasifikaci Dsc je nutné, aby alespoň jeden měsíc v roce byla průměrná teplota vyšší než 10 °C. V období zimy je typických pět až sedm po sobě jdoucích měsíců s teplotami pod bodem mrazu. Jaro i podzim jsou relativně krátké a změna teploty velmi rychlá.</p><p>Většina srážek připadá na období zimy, případně jara.</p><p>Obecně se dá říct, že se jedná o extrémně vzácný typ klimatu, se kterým se setkáváme v podstatě pouze v některých částech Kanady a Aljašky a na několika místech ve vysokých nadmořských výškách v USA, Korey a Rusku.</p></td><td width='500px'><img src='/images/climate/dsc.png' width='500px'></td></tr></table></div>";
			break;
		case "Dwa":
			$climate_name = "Teplé kontinentální podnebí se suchou zimou";
			$climate_color = "#CFD8DC";
			$climate_text_color = "black";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>w</b> – srážky v nejsušší měsíc zimní poloviny roku jsou nižší než jedna desetina množství měsíce s nejvyššími srážkami v letní polovině roku.</li><li><b>a</b> - teplota nejteplejšího měsíce je vyšší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami.</p><p>Pro tento typ je charakteristické velmi teplé léto, kdy teplota nejteplejšího měsíce překračuje hodnotu 22 °C. Zároveň však nejchladnější zimní měsíc není teplota vyšší než -3 °C. V zimě teploty klesají nejníže k hodnotám většinou mezi -5 až -20 °C. V létě se může teplota dostat i nad hodnotu 35 °C a zvyšují se denní amplitudy teplot.</p><p>Zimy bývají suché a srážky převažují v době léta, což také znamená, že zde nebývá příliš mnoho sněhu. Dwa typ podnebí je typický pro některé oblasti ve východní Asii ovlivněné monzunovou aktivitou. Konkrétně se jedná o části na východě Číny a převážná část korejského poloostrova.</p></td><td width='500px'><img src='/images/climate/dwa.png' width='500px'></td></tr></table></div>";
			break;
		case "Dwb":
			$climate_name = "Mírné kontinentální podnebí se suchou zimou";
			$climate_color = "#90A4AE";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>w</b> – srážky v nejsušší měsíc zimní poloviny roku jsou nižší než jedna desetina množství měsíce s nejvyššími srážkami v letní polovině roku.</li><li><b>b</b> - teplota každého ze čtyř nejteplejších měsíců je vyšší než 10 °C, ale nejteplejší měsíc je nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami. </p><p>Dwb typ se vyznačuje relativně teplými léty, které jsou navíc výrazně sušší než zbylá část roku. Nejvyšší letní teploty se pohybují nejčastěji kolem 30 až 35 °C, v zimě naopak klesají k přibližně -10 až -15 °C. V porovnání s Dwa typem zde je chladněji, žádný měsíc v roce není průměrná teplota vyšší než 22 °C, přesto však alespoň čtyři měsíce v roce je průměrná měsíční teplota nad 10 °C.</p><p>Podobně jako v případě Dwa klimatu, zimy jsou chladné, ale relativně suché a díky tomu tu bývá jen málo sněhu. Dwb typ podnebí je typický pro některé oblasti na jihu Číny v blízkosti Himálají, v centrální Číně, na jejím severovýchodě a dále v nejjihovýchodnějším cípu Ruska.</p></td><td width='500px'><img src='/images/climate/dwb.png' width='500px'></td></tr></table></div>";
			break;
		case "Dwc":
			$climate_name = "Subpolární kontinentální podnebí se suchou zimou";
			$climate_color = "#546E7A";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>D</b> – teplota nejteplejšího měsíce vyšší nebo rovna 10 °C a teplota nejchladnějšího měsíce rovna nebo nižší než -3 °C</li><li><b>w</b> – srážky v nejsušší měsíc zimní poloviny roku jsou nižší než jedna desetina množství měsíce s nejvyššími srážkami v letní polovině roku.</li><li><b>c</b> - teplota jednoho až tří měsíců je vyšší nebo rovna 10 °C, ale nejteplejší měsíc má teplotu nižší než 22 °C</li></ul><p>Obecně pro podnebí typu D platí, že se s ním setkáváme v podstatě výhradně na severní polokouli ve středních zeměpisných šířkách, směrem k pólům od oblastí typu C. Průměrná teplota nejteplejšího měsíce je vyšší nebo rovna 10 °C, ale v zimě nižší než -3°C. Neobvyklé nejsou v zimě srážky sněhové, silné větry a obecně chladné počasí dané polárními a arktickými vzdušnými masami. </p><p>Je typické dlouhými a většinou velmi chladnými zimami a krátkými, chladnějšími léty. Charakteristické jsou obrovské rozdíly teplot v rámci jednotlivých ročních období. V zimě můžou teploty padat i na hranici kolem -40 °C, v létě naopak šplhat až ke 30 °C. Léta jsou většinou dlouhá pouze tři až čtyři měsíce, ale pro klasifikaci Dwc je nutné, aby alespoň jeden měsíc v roce byla průměrná teplota vyšší než 10 °C. V období zimy je typických pět až sedm po sobě jdoucích měsíců s teplotami pod bodem mrazu. Jaro i podzim jsou relativně krátké a změna teploty velmi rychlá.</p><p>Zima bývá suchá a léto vlhké a právě v období léta spadne převážná většina srážek, často během intenzivnějších bouřek. V důsledku suché zimy tu i přes velmi nízké teploty nebývá příliš mnoho sněhu. Řadíme sem například většinu severního Mongolska anebo oblasti na jihovýchodě Ruska, v centrální Číně a na Aljašce.</p></td><td width='500px'><img src='/images/climate/dwc.png' width='500px'></td></tr></table></div>";
			break;
		case "ET":
			$climate_name = "Podnebí tundry";
			$climate_color = "#000000";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>E</b> – průměrná teplota nejteplejšího měsíce v roce je nižší než 10 °C</li><li><b>T</b> – teplota nejteplejšího měsíce v roce je mezi 0 a 10 °C</li></ul><p>Hlavním znakem polárního podnebí jsou velmi nízké teploty po celý rok. Typické jsou relativně velké rozdíly teplot mezi jednotlivými ročními obdobími, ne však tak velké jako v přilehlých subarktických zónách. Průměrná teplota nejteplejšího měsíce v roce je nad bodem mrazu, ale nižší než 10 °C. Znamená to, že v tuto dobu roztává sníh, avšak v místech s průměrnou roční teplotou pod -9 °C zůstává hlouběji pod zemí trvale zmrzlý permafrost. Takto nízké teploty již neumožňují růst stromů a vegetaci tak tvoří jen menší rostlinstvo.</p><p>Zimy bývají dlouhé a tuhé a srážky téměř výhradně v podobě suchého sněhu. Roční srážkové úhrny nebývají vysoké a pohybují se většinou jen do 350 mm (v určitých specifických podmínkách můžou dosahovat i podstatně vyšších hodnot).</p><p>Léta jsou mírná s denními maximy kolem 15 až 18 °C a vzhledem k poloze blízko pólu jsou dny dlouhé, často s velkou mírou oblačnosti. V dlouhých a potemnělých zimách klesá teplota nejčastěji k -20 až -50 °C.</p><p>Polární typ podnebí najdeme v nejsevernějších oblastech Kanady, na okrajích Grónska, na severu Aljašky, v nejsevernějších oblastech Ruska, ale i v extrémně vysokých nadmořských výškách například v oblasti nepálských Himálají.</p></td><td width='500px'><img src='/images/climate/et.png' width='500px'></td></tr></table></div>";
			break;
		case "EF":
			$climate_name = "Polární podnebí";
			$climate_color = "#8600B3";
			$climate_text_color = "white";
			$Koppen_desc = "<div style='background: ".$climate_color."; color: ".$climate_text_color."; padding: 20px;'><font size='5'>Tradiční Koppenova klasifikace: ".$climate." (".$climate_name.")</font><br><br><table cellspacing='2' cellpadding='2' width='100%'><tr><td style='color: ".$climate_text_color.";text-align:justify'>Oficiální definice:<br><ul><li><b>E</b> – průměrná teplota nejteplejšího měsíce v roce je nižší než 10 °C</li><li><b>F</b> – průměrná teplota všech měsíců je pod bodem mrazu</li></ul><p>Ledové podnebí najdeme v nejchladnějších oblastech planety. Teplota takřka nikdy nevystoupí nad 0 °C a průměrná teplota všech měsíců je nižší než bod mrazu. Ve výjimečných případech může nejvyšší teplota v létě během dne vystoupat na hodnotu kolem 5 °C, ale to pouze velmi krátkodobě. Denní teplotní rozdly jsou jen velmi malé.</p><p>Tyto oblasti najdeme v okolí severního a jižního pólu, patří sem převážná většina Grónska, nejjižnější cíp Jižní Ameriky, nejsevernější cíp Ruska, celá Antarktida a ty body s nejvyšší nadmořskou výškou na Zemi.</p><p>Vzhledem k takto nízkým teplotám zde není možný růst jakékoliv vegetace a celá oblast je pokrytá sněhem a ledem (vzácně se v teplejších částech můžou několik týdnů v roce objevovat lišejníky a mechy). Živočichové zde žijící se živí potravou, kterou získávají z oceánů.</p><p>Na rozdíl od typu ET zde ani v létě sníh netaje a proto se formují právě ledovce, které jsou neustále v pohybu.  V zimě teploty běžně klesají hluboko pod -20 °C, ve vnitrozemí i méně než -65 °C. Nejnižší teploty v roce jsou zaznamenávány na konci období polární noci.</p><p>Celoroční srážkové úhrny jsou jen velmi malé a v tomto ohledu toto podnebí velmi připomíná poušť. Veškeré srážky jsou vzhledem k teplotám sněhové a ročně představují přibližně 50 až 500 mm, vyšší hodnoty připadají na oblasti blíže pobřeží.</p><p>Rovněž typické jsou velmi silné větry, které ještě více snižují pocitovou teplotu.</p></td><td width='500px'><img src='/images/climate/ef.png' width='500px'></td></tr></table></div>";
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
			$climate_name_trewartha = "Tropické podnebí s rovnoměrnými srážkami";
			$climate_color_trewartha = "#4CAF50";
			$climate_text_color_trewartha = "white";
		}
		if($second=="w"){
			$climate_name_trewartha = "Tropické podnebí se suchou zimou";
			$climate_color_trewartha = "#2E7D32";
			$climate_text_color_trewartha = "white";
		}
		if($second=="s"){
			$climate_name_trewartha = "Tropické podnebí se suchým létem";
			$climate_color_trewartha = "#C8E6C9";
			$climate_text_color_trewartha = "black";
		}
	}
	if($first=="B"){
		if($second=="S"){
			$climate_name_trewartha = "Stepní podnebí";
			$climate_color_trewartha = "#FF8F00";
			$climate_text_color_trewartha = "black";
		}
		if($second=="W"){
			$climate_name_trewartha = "Pouštní podnebí";
			$climate_color_trewartha = "#FFEB3B";
			$climate_text_color_trewartha = "black";
		}
	}
	if($first=="C"){
		if($second=="s"){
			$climate_name_trewartha = "Subtropické podnebí se suchým létem";
			$climate_color_trewartha = "#F06292";
			$climate_text_color_trewartha = "black";
		}
		if($second=="w"){
			$climate_name_trewartha = "Subtropické podnebí se suchou zimou";
			$climate_color_trewartha = "#E64A19";
			$climate_text_color_trewartha = "white";
		}
		if($second=="f"){
			$climate_name_trewartha = "Subtropické podnebí s rovnoměrnými srážkami";
			$climate_color_trewartha = "#D32F2F";
			$climate_text_color_trewartha = "white";
		}
	}
	if($first=="D"){
		if($second=="O"){
			$climate_name_trewartha = "Mirné oceánské podnebí";
			$climate_color_trewartha = "#26C9FF";
			$climate_text_color_trewartha = "black";
		}
		if($second=="C"){
			$climate_name_trewartha = "Mírné kontinentální podnebí";
			$climate_color_trewartha = "#3949AB";
			$climate_text_color_trewartha = "white";
		}
	}
	if($first=="E"){
		if($second=="O"){
			$climate_name_trewartha = "Boreální oceánské podnebí";
			$climate_color_trewartha = "#888888";
			$climate_text_color_trewartha = "white";
		}
		if($second=="C"){
			$climate_name_trewartha = "Boreální kontinentální podnebí";
			$climate_color_trewartha = "#000000";
			$climate_text_color_trewartha = "white";
		}
	}
	if($first=="F"){
		if($second=="T"){
			$climate_name_trewartha = "Polární podnebí tundry";
			$climate_color_trewartha = "#BF00FF";
			$climate_text_color_trewartha = "white";
		}
		if($second=="I"){
			$climate_name_trewartha = "Ledové polární podnebí";
			$climate_color_trewartha = "#660066";
			$climate_text_color_trewartha = "white";
		}
	}
	if($third=="i"){
		$climate_name_trewartha = $climate_name_trewartha." s extrémně horkým létem";
	}
	if($third=="h"){
		$climate_name_trewartha = $climate_name_trewartha." s velmi horkým létem";
	}
	if($third=="a"){
		$climate_name_trewartha = $climate_name_trewartha." s horkým létem";
	}
	if($third=="b"){
		$climate_name_trewartha = $climate_name_trewartha." s teplým létem";
	}
	if($third=="l"){
		$climate_name_trewartha = $climate_name_trewartha." s mírným létem";
	}
	if($third=="k"){
		$climate_name_trewartha = $climate_name_trewartha." s chladným létem";
	}
	if($third=="o"){
		$climate_name_trewartha = $climate_name_trewartha." se studeným létem";
	}
	if($third=="c"){
		$climate_name_trewartha = $climate_name_trewartha." s velmi studeným létem";
	}
	if($third=="d"){
		$climate_name_trewartha = $climate_name_trewartha." s extrémně studeným létem";
	}
	if($third=="e"){
		$climate_name_trewartha = $climate_name_trewartha." s mrazivým létem";
	}
	
	if($fourth=="i"){
		$climate_name_trewartha = $climate_name_trewartha." a extrémně horkou zimou";
	}
	if($fourth=="h"){
		$climate_name_trewartha = $climate_name_trewartha." a velmi horkou zimou";
	}
	if($fourth=="a"){
		$climate_name_trewartha = $climate_name_trewartha." a horkou zimou";
	}
	if($fourth=="b"){
		$climate_name_trewartha = $climate_name_trewartha." a teplou zimou";
	}
	if($fourth=="l"){
		$climate_name_trewartha = $climate_name_trewartha." a mírnou zimou";
	}
	if($fourth=="k"){
		$climate_name_trewartha = $climate_name_trewartha." a chladnou zimou";
	}
	if($fourth=="o"){
		$climate_name_trewartha = $climate_name_trewartha." a studenou zimou";
	}
	if($fourth=="c"){
		$climate_name_trewartha = $climate_name_trewartha." a velmi studenou zimou";
	}
	if($fourth=="d"){
		$climate_name_trewartha = $climate_name_trewartha." a extrémně studenou zimou";
	}
	if($fourth=="e"){
		$climate_name_trewartha = $climate_name_trewartha." a mrazivou zimou";
	}
	if($first=="A"){
		if($second=="r"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Jako tropické klima je stejně jako v případě Koppenovy klasifikace považována průměrná teplota vyšší než 18 °C v průběhu všech měsíců. Jinými slovy jsou tedy oblasti označované v Trewarthově klasifikaci „A“ místa, kde je permanentní teplo.</p><p>Typ Ar je charakteristický rovnoměrným rozdělením srážek v průběhu celého roku. Není zde pozorováno pravidelné delší období sucha. Někdy bývá Ar klima označováno také jako podnebí deštných pralesů. Nejčastěji sem spadají oblasti v okolí rovníku pokryté právě deštnými pralesy. Typická je relativně vysoká vlhkost a dostatek srážek. Sezónní variabilita je zde velmi malá a vzhledem k blízkosti rovníku se výrazněji nemění ani délka dne a noci.</p>";
		}
		if($second=="w"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Jako tropické klima je stejně jako v případě Koppenovy klasifikace považována průměrná teplota vyšší než 18 °C v průběhu všech měsíců. Jinými slovy jsou tedy oblasti označované v Trewarthově klasifikaci „A“ místa, kde je permanentní teplo.</p><p>V případě podtypu Aw se jedná o oblasti s výskytem období sucha v době „zimy“ – zimu je v tomto směru nutné brát obrazně, jedná se o část roku s kratším dnem a delší nocí. Tento typ klimatu (někdy také označován jako tropické podnebí savan) je typický právě pro savany, nejčastěji se s ním setkáváme v Africe, Asii a Jižní a Střední Americe.</p><p>Obecně se dá říct, že existují čtyři typy tropického podnebí savan:<ul><li>jasně ohraničené období sucha a období dešťů o přibližně stejné délce trvání. Většina ročních srážek je pozorována v období dešťů, v období sucha prší jen minimálně.</li><li>delší období sucha je následováno relativně krátkým obdobím dešťů. V tomto případě je typických sedm a více měsíců sucha a následně pět nebo méně měsíců s dešti. Setkáváme se tedy s relativně širokou škálou možností, na jednom konci jsou to místa se sedmi měsíci sucha a pěti měsíci dešťů, jen málo rozdílné od prvního typu, na straně druhé jsou to místa s dlouhým obdobím sucha a následně velmi intenzivními srážkami.</li><li>třetím typem je typ s relativně dlouhým obdobím dešťů a kratším obdobím sucha. Je tomu v tomto případě přesně naopak než u předchozího typu, zaznamenáváme tedy sedm a více měsíců dešťů a pět a méně měsíců sucha.</li><li>čtvrtý a relativně velmi vzácný podtyp je případ, kdy i v období sucha jsou zaznamenávány srážky a období dešťů není tak výrazné.</li></ul></p>";
		}
		if($second=="s"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Jako tropické klima je stejně jako v případě Koppenovy klasifikace považována průměrná teplota vyšší než 18 °C v průběhu všech měsíců. Jinými slovy jsou tedy oblasti označované v Trewarthově klasifikaci „A“ místa, kde je permanentní teplo.</p><p>V případě podtypu As se jedná o oblasti s výskytem období sucha v době „léta“ - v tomto směru je pojem „léto“ nutné brát obrazně, jedná se o část roku s kratší nocí a delším dnem. Tento podtyp tropického klima je velmi vzácný. Stejně jako analogický podtyp Aw (suchá zima), bývá toto klima označováno jako tropické klima savan.</p><p>Obecně se dá říct, že existují čtyři typy tropického podnebí savan:<ul><li>jasně ohraničené období sucha a období dešťů o přibližně stejné délce trvání. Většina ročních srážek je pozorována v období dešťů, v období sucha prší jen minimálně.</li><li>delší období sucha je následováno relativně krátkým obdobím dešťů. V tomto případě je typických sedm a více měsíců sucha a následně pět nebo méně měsíců s dešti. Setkáváme se tedy s relativně širokou škálou možností, na jednom konci jsou to místa se sedmi měsíci sucha a pěti měsíci dešťů, jen málo rozdílné od prvního typu, na straně druhé jsou to místa s dlouhým obdobím sucha a následně velmi intenzivními srážkami.</li><li>třetím typem je typ s relativně dlouhým obdobím dešťů a kratším obdobím sucha. Je tomu v tomto případě přesně naopak než u předchozího typu, zaznamenáváme tedy sedm a více měsíců dešťů a pět a méně měsíců sucha.</li><li>čtvrtý a relativně velmi vzácný podtyp je případ, kdy i v období sucha jsou zaznamenávány srážky a období dešťů není tak výrazné.</li></ul></p>";
		}
	}
	if($first=="B"){
		if($second=="S"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Do skupiny B spadají oblasti klimatu pouští a polopouští. BS podtyp bývá také označován jako podnebí stepí, semiaridní podnebí nebo podnebí polopouští, které je v porovnání s typem BW o něco méně suché. Dá se také definovat jako oblast, kde je úhrn srážek nižší než potencionální evapotranspirace, rozdíl však není příliš výrazný. Pro klasifikaci je nutné nejprve určit srážkový práh na základě průměrné roční teploty a rozložení a množství srážek. V Koppenově klasifikaci se navíc rozlišují podtypy BSh a BSk, v prvním případě se jedná o horké semiaridní podnebí, v druhém o chladné semiaridní podnebí.</p><p>Semiaridní oblasti jsou charakteristické křovinatými a travnatými porosty. V případě horkých semiaridních zón je typické extrémně teplé léto a velmi mírné zimy. Sníh je jen velmi vzácný. Nejčastějšími oblastmi s horkým semiaridním klimatem jsou západní Afrika, Indie, část Mexika a některé pohraniční oblasti států USA. Co se týče chladných semiaridních oblastí, ty jsou většinou na místech více ve vnitrozemí, se stále relativně teplými léty (avšak méně než v případě horkých semiaridních zón), ale chladnými zimami, kdy bývají běžné i srážky sněhové. Rovněž se často jedná o oblasti vyšších nadmořských výšek a typické jsou výraznější změny teploty mezi dnem a nocí, což je další odlišností od horkých semiaridních oblastí. Obecně se dá říct, že místa chladného semiaridního klimatu s vyšší nadmořskou výškou mívají sušší zimy a více srážek v létě, zatímco chladná semiaridní místa nižších nadmořských výšek jsou typické spíše suššími léty a více srážkami na jaře, na podzim a v zimě. Chladné semiaridní oblasti najdeme například v centrální Asii, na západě USA, v některých oblastech na severu Austrálie, částech Španělska nebo na jihu Jižní Ameriky.</p>";
		}
		if($second=="W"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Pouštní, nebo také aridní, podnebí je typické extrémně nízkým úhrnem srážek. V Koppenově klasifikaci se tento typ dále dělí na podtypy h a k. Pouště totiž můžou být jak horké, tak chladné. V podstatě se dá říct, že sem spadají zóny, které teplotami nelze klasifikovat jako polární oblasti, ale jsou jim v mnoha směrech velmi podobné, především pak téměř naprostou absencí jakékoliv vegetace v důsledku nedostatku vody.</p><p>Pro pouštní oblasti bývá typický roční úhrn srážek menší než 250 mm, často je to však ještě mnohem méně a existují dokonce oblasti, kde některé roky neprší vůbec.</p><p>Teplé pouštní oblasti nejčastěji najdeme na rozhraní subtropické zóny přibližně 30 ° na jih nebo na sever od rovníku. Bývá zde minimální oblačnost po celý rok a teploty bývají extrémně vysoké mnohdy nad 40 °C, v některých oblastech dokonce nad 45 °C. Jsou to nejteplejší místa na Zemi, což však neznamená, že zde nebývá i chladno. V důsledku minimální oblačnosti totiž po západu Slunce teplota rychle klesá a není nijak neobvyklý ani mráz v brzkých ranních hodinách před východem slunce. Teplé pouště se vyskytují především na severu Afriky, kde leží vůbec největší poušť světa Sahara. Dále jsou teplé pouště na Středním Východě, v centrální a západní Austrálii anebo také v některých oblastech na jihozápadě USA a jihozápadě Afriky.</p><p>Oblasti chladných pouští můžou svým názvem mást. Léta zde totiž rovněž bývají až extrémně horká a suchá. V zimě se ale teploty dostávají na velmi nízká čísla, někdy dokonce velmi výrazně pod bod mrazu. Místa chladných pouští představují často území ve vyšších nadmořských výškách a největší podíl leží v centrální Asii, nejznámější je asi mongolská poušť Gobi.</p>";
		}
	}
	if($first=="C"){
		if($second=="s"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Subtropické klima je v Koppenově i Trewarthově klasifikaci definované jako území, kde má minimálně 8 měsíců v roce průměrnou teplotu vyšší než 10 °C, ale nejchladnější měsíc má teplotu nižší než 18 °C. Podtypy se pak vzájemně odlišují na základě ročního průběhu srážek. V Trewarthově klasifikaci je podmínkou také maximální roční úhrn srážek 900 mm.</p><p>Obecně je subtropické podnebí většinou v oblastech přiléhajících k tropickým ze směru od pólů. Časté jsou také v rámci území s tropickým klimatem, ale s vyšší nadmořskou výškou. Z vegetace jsou typické například citrusovníky nebo palmy.</p><p>Podtyp Cs v Trewarthově klasifikaci označuje subtropické podnebí se suchým létem, někdy také označovaný jako Středozemní klima. Je charakteristický horkými a suchými léty a mírnými zimami, ve kterých spadne převážná část zdejších srážek. V létě dominuje vliv vysokého tlaku vzduchu a srážek je málo, ty které se vyskytují, pak většinou přichází ve formě krátkých intenzivnějších bouřek. V zimě se i zde můžou zejména ve výše položených místech objevovat srážky sněhové. Zimy však jsou teplotně spíše mírné.</p><p>Nejčastěji se středozemní podnebí nachází u západních pobřeží kontinentů ve středních zeměpisných šířkách, konkrétně se pak jedná například o oblasti na západním pobřeží USA v Kalifornii, jih Evropy a jihozápadní cíp Austrálie.</p>";
		}
		if($second=="w"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Subtropické klima je v Koppenově i Trewarthově klasifikaci definované jako území, kde má minimálně 8 měsíců v roce průměrnou teplotu vyšší než 10 °C, ale nejchladnější měsíc má teplotu nižší než 18 °C. Podtypy se pak vzájemně odlišují na základě ročního průběhu srážek. V Trewarthově klasifikaci jsou tato místa většinou také charakteristická průměrnou teplotou nejchladnějšího měsíce v rozmezí 3 až 10 °C.</p><p>Obecně je subtropické podnebí většinou v oblastech přiléhajících k tropickým ze směru od pólů. Časté jsou také v rámci území s tropickým klimatem, ale s vyšší nadmořskou výškou. Z vegetace jsou typické například citrusovníky nebo palmy.</p><p>Podtyp Cw je typický více srážkami v období léta a relativně suchou, mírnou zimou. V oblastech s tímto typem ovlivňovaných monzunovými dešti dochází velmi často k velmi intenzivním srážkám v létě a naopak velmi výrazným obdobím sucha v době zimy, což může mít i výrazně negativní vliv na zdejší zemědělství. Většina letních srážek spadne během intenzivnějších bouřek.</p><p>Subtropické podnebí se suchou zimou najdeme například v některých částech střední Jižní Ameriky, v Mexiku, dále ve středu jižní části Afriky a pak také na území Indie a v oblasti jihovýchodní Asie.</p>";
		}
		if($second=="f"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Subtropické klima je v Koppenově i Trewarthově klasifikaci definované jako území, kde má minimálně 8 měsíců v roce průměrnou teplotu vyšší než 10 °C, ale nejchladnější měsíc má teplotu nižší než 18 °C. Podtypy se pak vzájemně odlišují na základě ročního průběhu srážek.</p><p>Obecně je subtropické podnebí většinou v oblastech přiléhajících k tropickým ze směru od pólů. Časté jsou také v rámci území s tropickým klimatem, ale s vyšší nadmořskou výškou. Z vegetace jsou typické například citrusovníky nebo palmy.</p><p>Podtyp Cf je typický relativně rovnoměrným rozložením srážek v průběhu roku a někdy bývá Cf označováno také jako vlhké subtropické podnebí, právě v důsledku absence výraznější pravidelné periody sucha.</p><p>Subtropické podnebí s rovnoměrným rozložením srážek najdeme například ve východní části Jižní Ameriky, v rozsáhlém území na jihovýchodě USA, na severu Itálie, v nejzazším cípu jihozápadního Ruska, v jihovýchodní Asii, v některých částech Japonska anebo například také na východním pobřeží Austrálie.</p>";
		}
	}
	if($first=="D"){
		if($second=="O"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Písmeno D je v Trewarthově klasifikaci, stejně jako v případě Köppena, označením pro oblasti mírného pásu. Kritériem pro mírné podnebí je výskyt 4 až 7 měsíců s průměrnou teplotou vzduchu vyšší než 10 °C. Obecně se rozdělují dva typy: mírné oceánské a mírné kontinentální. Ty se vzájemně liší průměrnou teplotou nejchladnějšího měsíce.</p><p>Mírné podnebí je charakteristické typickými čtyřmi ročními obdobími, jsou zde jasné rozdíly v teplotě mezi zimou a létem, samotné absolutní hodnoty však nedosahují výrazných extrémů a přechody jsou pozvolné.</p><p>V případě mírného oceánského podnebí je zima oproti kontinentálnímu teplejší a teplota nejchladnějšího měsíce je nad bodem mrazu. Zdejší podmínky ovlivňuje blízký oceán, který způsobuje menší výkyvy teplot mezi jednotlivými ročními obdobími. Vítr vane většinou od západu, a proto je tento typ podnebí typický právě pro západní části kontinentů, jako například západní Evropu nebo západní pobřeží USA, v zeměpisných šířkách mezi 40 a 60 ° v Americe a 40 a 65 ° v Evropě. Jedná se velmi často o hustě obydlené oblasti.</p>";
		}
		if($second=="C"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Písmeno D je v Trewarthově klasifikaci, stejně jako v případě Köppena, označením pro oblasti mírného pásu. Kritériem pro mírné podnebí je výskyt 4 až 7 měsíců s průměrnou teplotou vzduchu vyšší než 10 °C. Obecně se rozdělují dva typy: mírné oceánské a mírné kontinentální. Ty se vzájemně liší průměrnou teplotou nejchladnějšího měsíce.</p><p>Mírné podnebí je charakteristické typickými čtyřmi ročními obdobími, jsou zde jasné rozdíly v teplotě mezi zimou a létem, samotné absolutní hodnoty však nedosahují výrazných extrémů a přechody jsou pozvolné.</p><p>Kontinentální varianta mírného podnebí je typická pro oblasti více ve vnitrozemí a právě větší vzdálenost od moře znamená větší teplotní rozdíly. Nejchladnější měsíc má v případě kontinentálního podtypu teplotu nižší než 0 °C. Oproti oceánskému mírnému podnebí jsou léta většinou teplejší a zimy naopak chladnější, s relativně pravidelným výskytem srážek sněhových.</p><p>S mírným kontinentálním podnebím se setkáváme například v částech severovýchodní ch USA nebo v oblastech střední a východní Evropy. Jedná se velmi často o hustě obydlené oblasti.</p>";
		}
	}
	if($first=="E"){
		if($second=="O"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Boreální podnebí se také označuje jako severské nebo subarktické. Toto označení již napovídá, že se jedná o celoročně chladnější oblasti. Charakteristický je obrovský rozdíl teplot mezi létem a zimou. Zatímco v zimě se můžou vyskytovat extrémně tuhé mrazy a teploty klesat až k hodnotám pod -30 °C, v létě naopak můžou vystoupat až nad tropických 30 °C. Tato velmi teplá letní období jsou však velmi krátká a netrvají déle než tři měsíce. Pro klasifikaci jako boreální klima (skupina D) je také podstatný alespoň jeden měsíc s průměrnou teplotou alespoň 10 °C. Pět až sedm měsíců v roce je pak průměrná teplota pod bodem mrazu a půda je zmrzlá v některých částech celoročně (permafrost).</p><p>Většinou je boreální klima typické relativně nízkým úhrnem srážek pohybujícím se do asi 400 mm. Dále od pobřeží převažují srážky v zimních měsících, u pobřežních oblastí se subarktickým podnebím naopak více srážek spadne většinou na podzim.</p><p>Typické jsou jehličnany, kapradiny a několik dalších druhů stromů, které jsou schopné snášet velmi mrazivé zimy. I když je zde však celkově menší druhová rozmanitost, vegetační porost je relativně hojný a to v podobě například rozsáhlých území označovaných jako tajga. Právě lesy tajgy jsou vůbec nejrozsáhlejšími lesními porosty na zemi, najdeme je především v oblastech Ruska a Kanady.</p><p>Stejně jako v případě mírného podnebí i boreální podnebí rozdělujeme na dva typy a to oceánské a kontinentální. Oceánská varianta (EO) nemá tak extrémní rozdíly teplot mezi jednotlivými ročními obdobími. S boreálním oceánským podnebím se můžeme setkat například v částech Skotska, na Kamčatce a dále blíže u pobřeží v oblastech Kanady, Sibiře, Aljašky nebo na severu Evropy.</p>";
		}
		if($second=="C"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Boreální podnebí se také označuje jako severské nebo subarktické. Toto označení již napovídá, že se jedná o celoročně chladnější oblasti. Charakteristický je obrovský rozdíl teplot mezi létem a zimou. Zatímco v zimě se můžou vyskytovat extrémně tuhé mrazy a teploty klesat až k hodnotám pod -30 °C, v létě naopak můžou vystoupat až nad tropických 30 °C. Tato velmi teplá letní období jsou však velmi krátká a netrvají déle než tři měsíce. Pro klasifikaci jako boreální klima (skupina D) je také podstatný alespoň jeden měsíc s průměrnou teplotou alespoň 10 °C. Pět až sedm měsíců v roce je pak průměrná teplota pod bodem mrazu a půda je zmrzlá v některých částech celoročně (permafrost).</p><p>Většinou je boreální klima typické relativně nízkým úhrnem srážek pohybujícím se do asi 400 mm. Dále od pobřeží převažují srážky v zimních měsících, u pobřežních oblastí se subarktickým podnebím naopak více srážek spadne většinou na podzim. </p><p>Typické jsou jehličnany, kapradiny a několik dalších druhů stromů, které jsou schopné snášet velmi mrazivé zimy. I když je zde však celkově menší druhová rozmanitost, vegetační porost je relativně hojný a to v podobě například rozsáhlých území označovaných jako tajga. Právě lesy tajgy jsou vůbec nejrozsáhlejšími lesními porosty na zemi, najdeme je především v oblastech Ruska a Kanady.</p><p>Stejně jako v případě mírného podnebí i boreální podnebí rozdělujeme na dva typy a to oceánské a kontinentální. Kontinentální podtyp (EC) je charakteristický extrémními rozdíly mezi teplotami v létě a v zimě. Jako příklad lze uvést ruský Ojmjakon, kde v červenci můžou teploty vyšplhat i nad 30 °C, zatímco v zimě je lednová průměrná teplota jen slabě nad -50 °C a naměřeno bylo i méně než -65 °C! Obecně tedy se setkáváme s kontinentálním boreálním podnebím ve vnitrozemských částech rozsáhlých oblastí na severu severní polokoule v Kanadě a Rusku.</p>";
		}
	}
	if($first=="F"){
		if($second=="T"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Polární podnebí tvoří nejchladnější oblasti planety. Obecně je pro tuto klasifikaci dáno kritérium průměrné teploty všech měsíců nižší než 10 °C. V klasifikaci Trewartha se tento typ podnebí dále dělí na dva typy – polární podnebí tundry a ledové polární podnebí.</p><p>V polárních oblastech je charakteristická absence léta. Každý měsíc má průměrnou teplotu do 10 °C, často ještě mnohem nižší. Celkově zaujímají polární oblasti přibližně 20 % zemského povrchu a typické jsou v létě velmi dlouhé dny a v zimě velmi dlouhé noci, extrémními příklady jsou pak polární den a polární noc.</p><p>Podtyp tundry je o něco teplejší než podtyp ledový. Platí pro něj, že alespoň jeden měsíc v roce je průměrná teplota nad bodem mrazu. Pro stromy je však tento typ podnebí již příliš chladný, proto zdejší vegetaci tvoří víceméně jen nižší keříky, mechy apod. Obvykle zde nespadne velké množství srážek.</p><p>Oblasti tundry zahrnují například nejsevernější část Kanady, západní a severní pobřeží Aljašky, nejsevernější část Evropy a Ruska, pobřežní oblasti Grónska a rovněž úzký pás podél pobřeží Antarktidy.</p>";
		}
		if($second=="I"){
			$Trewartha_desc = "<div style='background: ".$climate_color_trewartha."; color: ".$climate_text_color_trewartha."; padding: 20px;'><font size='5'>Moderní Trewarthova klasifikace: ".$climate." (".$climate_name_trewartha.")</font><br><br><p>Polární podnebí tvoří nejchladnější oblasti planety. Obecně je pro tuto klasifikaci dáno kritérium průměrné teploty všech měsíců nižší než 10 °C. V klasifikaci Trewartha se tento typ podnebí dále dělí na dva typy – polární podnebí tundry a ledové polární podnebí.</p><p>V polárních oblastech je charakteristická absence léta. Každý měsíc má průměrnou teplotu do 10 °C, v případě podtypu ledového polárního podnebí to je tato hranice dokonce jen do 0 °C. Celkově zaujímají polární oblasti přibližně 20 % zemského povrchu a typické jsou v létě velmi dlouhé dny a v zimě velmi dlouhé noci, extrémními příklady jsou pak polární den a polární noc.</p><p>Jak již výše uvedené kritérium klasifikace naznačuje, ledové polární podnebí vládne v oblastech celoročního mrazu. Průměrná teplota ani jednoho měsíce v roce nepřekračuje hodnotu 0 °C. Jedná se o extrémně chladné oblasti, které jsou zcela neobydlené a trvale pokryté vrstvou sněhu a ledu. Žije zde pouze velmi málo druhů organizmů, které se živí mořskými živočichy, jedná se například o lední medvědy nebo tučňáky. Rostliny zde nerostou žádné.</p><p>Ledové polární podnebí se na Zemi vyskytuje především v téměř celé oblasti Antarktidy a ve vnitrozemí Grónska. Byla zde naměřena vůbec nejnižší teplota v historii, konkrétně to bylo -89,2 °C na polární stanici Vostok v Antarktidě. Družicové snímky však naznačují, že v některých oblastech pravděpodobně teplota klesá za určitých podmínek ještě níže.</p>";
		}
	}
	if($third=="i"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>extrémně horké léto: nejteplejší měsíc s průměrnou teplotou nad 35 °C</li>";
	}
	if($third=="h"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>velmi horké léto: nejteplejší měsíc s průměrnou teplotou mezi 28 a 35 °C</li>";
	}
	if($third=="a"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>horké léto: nejteplejší měsíc s průměrnou teplotou mezi 23 a 28 °C</li>";
	}
	if($third=="b"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>teplé léto: nejteplejší měsíc s průměrnou teplotou mezi 18 a 23 °C</li>";
	}
	if($third=="l"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>mírné léto: nejteplejší měsíc s průměrnou teplotou mezi 10 a 18 °C</li>";
	}
	if($third=="k"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>chladné léto: nejteplejší měsíc s průměrnou teplotou mezi 0 a 10 °C</li>";
	}
	if($third=="o"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>studené léto: nejteplejší měsíc s průměrnou teplotou mezi 0 a -10 °C</li>";
	}
	if($third=="c"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>velmi studené léto: nejteplejší měsíc s průměrnou teplotou mezi -10 a -25 °C</li>";
	}
	if($third=="d"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>extrémně studené léto: nejteplejší měsíc s průměrnou teplotou mezi -25 a -40 °C</li>";
	}
	if($third=="e"){
		$Trewartha_desc = $Trewartha_desc."<ul><li>mrazivé léto: nejteplejší měsíc s průměrnou teplotou nižší než -40 °C</li>";
	}
	
	if($fourth=="i"){
		$Trewartha_desc = $Trewartha_desc."<li>extrémně horká zima: nejchladnější měsíc s průměrnou teplotou nad 35 °C</li></ul></div>";
	}
	if($fourth=="h"){
		$Trewartha_desc = $Trewartha_desc."<li>velmi horká zima: nejchladnější měsíc s průměrnou teplotou mezi 28 a 35 °C</li></ul></div>";
	}
	if($fourth=="a"){
		$Trewartha_desc = $Trewartha_desc."<li>horká zima: nejchladnější měsíc s průměrnou teplotou mezi 23 a 28 °C</li></ul></div>";
	}
	if($fourth=="b"){
		$Trewartha_desc = $Trewartha_desc."<li>teplá zima: nejchladnější měsíc s průměrnou teplotou mezi 18 a 23 °C</li></ul></div>";
	}
	if($fourth=="l"){
		$Trewartha_desc = $Trewartha_desc."<li>mírná zima: nejchladnější měsíc s průměrnou teplotou mezi 10 a 18 °C</li></ul></div>";
	}
	if($fourth=="k"){
		$Trewartha_desc = $Trewartha_desc."<li>chladná zima: nejchladnější měsíc s průměrnou teplotou mezi 0 a 10 °C</li></ul></div>";
	}
	if($fourth=="o"){
		$Trewartha_desc = $Trewartha_desc."<li>studená zima: nejchladnější měsíc s průměrnou teplotou mezi -10 a 0 °C</li></ul></div>";
	}
	if($fourth=="c"){
		$Trewartha_desc = $Trewartha_desc."<li>velmi studená zima: nejchladnější měsíc s průměrnou teplotou mezi -10 a -25 °C</li></ul></div>";
	}
	if($fourth=="d"){
		$Trewartha_desc = $Trewartha_desc."<li>extrémně studená zima: nejchladnější měsíc s průměrnou teplotou mezi -25 a -40 °C</li></ul></div>";
	}
	if($fourth=="e"){
		$Trewartha_desc = $Trewartha_desc."<li>mrazivá zima: nejchladnější měsíc s průměrnou teplotou nižší než -40 °C</li></ul></div>";
	}
}
?>
<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	if(!isset($_SESSION['user'])){
		$_SESSION['user'] = "user";
	}

	header('Content-type: text/css');
	include("../config.php");
	include($baseURL."css/design.php");

	if($gradient==""){
		$gradient = false;
	}

	if($designFont=="Bree Serif" || $designFont2=="Bree Serif"){
		echo "@import url(https://fonts.googleapis.com/css?family=Bree+Serif);";
	}
	if($designFont=="PT Sans" || $designFont2=="PT Sans"){
		echo "@import url(https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext);";
		echo "@import url(https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic-ext,latin-ext);";
	}
	if($designFont=="Roboto" || $designFont2=="Roboto"){
		echo "@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin,cyrillic-ext,latin-ext);";
	}
	if($designFont=="Dosis" || $designFont2=="Dosis"){
		echo "@import url(https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext);";
	}
	if($designFont=="Ubuntu" || $designFont2=="Ubuntu"){
		echo "@import url(https://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext);";
	}
	if($designFont=="Lobster" || $designFont2=="Lobster"){
		echo "@import url(https://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext);";
	}
	if($designFont=="Kaushan Script" || $designFont2=="Kaushan Script"){
		echo "@import url(https://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext);";
	}
	if($designFont=="Open Sans" || $designFont2=="Open Sans"){
		echo "@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext);";
	}
	if($designFont=="Play" || $designFont2=="Play"){
		echo "@import url(https://fonts.googleapis.com/css?family=Play:400,700&subset=latin,latin-ext);";
	}
	if($designFont=="Open Sans Condensed" || $designFont2=="Open Sans Condensed"){
		echo "@import url(https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,latin-ext);";
	}
	if($designFont=="Anton" || $designFont2=="Anton"){
		echo "@import url(https://fonts.googleapis.com/css?family=Anton&subset=latin,latin-ext);";
	}
	if($designFont=="Inconsolata" || $designFont2=="Inconsolata"){
		echo "@import url(https://fonts.googleapis.com/css?family=Inconsolata:400,700&subset=latin,latin-ext);";
	}
	if($designFont=="Righteous" || $designFont2=="Righteous"){
		echo "@import url(https://fonts.googleapis.com/css?family=Righteous&subset=latin,latin-ext);";
	}
	if($designFont=="Marck Script" || $designFont2=="Marck Script"){
		echo "@import url(https://fonts.googleapis.com/css?family=Marck+Script&subset=latin,latin-ext);";
	}
	if($designFont=="Poiret One" || $designFont2=="Poiret One"){
		echo "@import url(https://fonts.googleapis.com/css?family=Poiret+One&subset=latin,latin-ext);";
	}
	if($designFont=="Cutive Mono" || $designFont2=="Cutive Mono"){
		echo "@import url(https://fonts.googleapis.com/css?family=Cutive+Mono&subset=latin,latin-ext);";
	}
	if($designFont=="Patrick Hand SC" || $designFont2=="Patrick Hand SC"){
		echo "@import url(https://fonts.googleapis.com/css?family=Patrick+Hand+SC&subset=latin-ext);";
	}

	if($designFont=="Rubik" || $designFont2=="Rubik"){
		echo "@import url(https://fonts.googleapis.com/css?family=Rubik:400,500,700&subset=latin-ext);";
	}
	if($designFont=="Lato" || $designFont2=="Lato"){
		echo "@import url(https://fonts.googleapis.com/css?family=Lato:300,400,700&subset=latin-ext);";
	}
	if($designFont=="Raleway" || $designFont2=="Raleway"){
		echo "@import url(https://fonts.googleapis.com/css?family=Raleway:300,400,700&subset=latin-ext);";
	}

	if(!isset($customBgColor1)){
		$customBgColor1 = "000";
	}
	if(!isset($customBgColor2)){
		$customBgColor2 = "000";
	}
	if(!isset($customBgColor3)){
		$customBgColor3 = "000";
	}
	if(!isset($customBgColor4)){
		$customBgColor4 = "000";
	}
	if(!isset($customBgType)){
		$customBgType = "vertical";
	}
	if(!isset($customBgImg)){
		$customBgImg = "";
	}

	if(!isset($customMaxWidth)){
		$customMaxWidth = "1600px";
	}


	if($customBgColor1!=""){
		$customBgArr[] = $customBgColor1;
	}
	if($customBgColor2!=""){
		$customBgArr[] = $customBgColor2;
	}
	if($customBgColor3!=""){
		$customBgArr[] = $customBgColor3;
	}
	if($customBgColor4!=""){
		$customBgArr[] = $customBgColor4;
	}

	if(!isset($customHeadingShadow)){
		$customHeadingShadow = "none";
	}
	if(!isset($customBodyTextShadow)){
		$customBodyTextShadow = "none";
	}
	?>
	html{
		height: 100%;
	}
	body{
		width:100%;
		<?php echo generateBg($customBgArr,$customBgType,$customBgImg);?>
		margin: 0;
		padding: 0;
		font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-size: <?php echo $customGlobalFontSize?>;
		color: #<?php echo $color_schemes[$design]['100']?>;
		word-wrap: break-word;
		text-shadow: <?php echo $customBodyTextShadow?>;
	}
	#main_top{
		width: 100%;
		max-width: <?php echo $customMaxWidth?>;
		margin: 0px;
		margin-left: auto;
		margin-right: auto;
	}
	#main{
		width: 100%;
		max-width: <?php echo $customMaxWidth?>;
		margin-left: auto;
		margin-right: auto;
		min-height: 70%;
	}
	#header{
		width: 100%;
		margin-left: auto;
		margin-right: auto;
		text-align: center;
	}
	#footer{
		padding-top: 0.5%;
		padding-bottom: 0.5%;
		width: 100%;
		max-width: <?php echo $customMaxWidth?>;
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		font-size: 0.7em;
		background: #<?php echo $color_schemes[$design2]['700']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['500']?> 0%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['500']?> 0%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['500']?> 0%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['500']?>), color-stop(100, #<?php echo $color_schemes[$design2]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['500']?> 0%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['500']?> 0%, #<?php echo $color_schemes[$design2]['900']?> 100%);
		<?php
			}
		?>
		color: #<?php echo $color_schemes[$design2]['font700']?>;
	}
	#title{
		padding-top: 100px;
		color: white!important;
		font-variant: small-caps;
		font-size: 4.5em;
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-weight: bold;
		text-shadow: black 0.02em 0.02em 0.02em;
	}
	#title{
		font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif!important;
	}
	#title1{
		font-size: 3.5em!important;
		color: white!important;
	}
	#title2{
		font-size: 2.5em!important;
		color: white!important;
	}
	h1{
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-weight: bold;
		font-variant: small-caps;
		text-align: center;
		font-size: 2.5em;
		margin: 0.1em;
		margin-top: 0px;
		color: white;
		padding-top: 5px;
		padding-bottom: 10px;
	}
	h2,.h2{
		color: #<?php echo $color_schemes[$design]['200']?>;
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-weight: bold;
		text-align: center;
		font-variant: small-caps;
		font-size: 2em;
		margin: 0.05em;
	}
	h3,.h3{
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-weight: bold;
		text-align: left;
		font-size: 1.5em;
		margin-top: 0.05em;
		margin-bottom: 0.05em;
		font-variant: small-caps;
		color: #<?php echo $color_schemes[$design2]['300']?>;
	}
	h1,h2,h3,h4,.h1,.h2,.h3,.h4{
		text-shadow: <?php echo $customHeadingShadow?>;
	}
	.button, .button2 {
		padding: 8px 8px 8px 8px;
		text-decoration: none;
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		cursor: pointer;
		font-weight: bold;
		text-align: center;
	}
	.button:hover {
		text-decoration: none;
	}
	.button {
		border: 1px solid #<?php echo $color_schemes[$design]['200']?>;
		color: #<?php echo $color_schemes[$design]['100']?>;
		background: #<?php echo $color_schemes[$design]['800']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['900']?>), color-stop(50, #<?php echo $color_schemes[$design]['700']?>), color-stop(100, #<?php echo $color_schemes[$design]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
		<?php
			}
		?>
	}
	.button:hover {
		background: #<?php echo $color_schemes[$design]['600']?>;
		color: #<?php echo $color_schemes[$design]['font600']?>;
	}
	.button2 {
		border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
		color: #<?php echo $color_schemes[$design2]['100']?>;
		background: #<?php echo $color_schemes[$design2]['800']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['900']?>), color-stop(50, #<?php echo $color_schemes[$design2]['700']?>), color-stop(100, #<?php echo $color_schemes[$design2]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
		<?php
			}
		?>
	}
	.button2:hover {
		background: #<?php echo $color_schemes[$design2]['600']?>;
		color: #<?php echo $color_schemes[$design2]['font600']?>;
	}
	a {
		text-decoration: none;
		color:#FFFFFF;
		font-variant: small-caps;
		color:#<?php echo $color_schemes[$design]['200']?>;
	}
	a:link {
		color:#<?php echo $color_schemes[$design]['200']?>;
		font-variant: small-caps;
	}
	a:visited {
		color:#<?php echo $color_schemes[$design]['200']?>;
	}
	a:hover {
		-webkit-transition: all .5s ease;
		-moz-transition: all .5s ease;
		-o-transition: all .5s ease;
		transition: all .5s ease;
		color: #<?php echo $color_schemes[$design2]['200']?>;
	}
	#gotop{
		position:fixed;
		top:10px;
		right:10px;
	}
	.sup{
		vertical-align: top; font-size: 0.7em;
	}
	.sub{
		vertical-align: bottom; font-size: 0.7em;
	}
	sup, sub {
		vertical-align: baseline;
		position: relative;
		top: -0.4em;
	}
	sub { 
		top: 0.4em; 
	}
	::-webkit-scrollbar {
		margin-top: 5px;
		width: 14px;
	}
	.heading_bar {
		margin: 3px;
		font-family:"<?php echo $designFont?>","Arial Narrow",Arial,Helvetica,sans-serif;
		margin-left: auto;
		margin-right: auto;
		color: #fff;
		padding: 3px 10px;
		cursor: pointer;
		position: static;
		font-size: 1.2em;
		text-align: left;
		font-variant: small-caps;
		font-weight: bold;
		background: #<?php echo $color_schemes[$design2]['900']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['900']?>), color-stop(50, #<?php echo $color_schemes[$design2]['700']?>), color-stop(100, #<?php echo $color_schemes[$design2]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
		<?php
			}
		?>
		color: #<?php echo $color_schemes[$design2]['font900']?>;
	}
	.heading_bar:hover{
		background: #<?php echo $color_schemes[$design2]['700']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['700']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['700']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['700']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['700']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['700']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['700']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['700']?>), color-stop(50, #<?php echo $color_schemes[$design2]['500']?>), color-stop(100, #<?php echo $color_schemes[$design2]['700']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['700']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['700']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['700']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['700']?> 100%);
		<?php
			}
		?>
	}
	.content {
		padding: 5px 10px;
		margin: 1;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 10px;
		background: #<?php echo $color_schemes[$design2]['200']?>;
		color: #<?php echo $color_schemes[$design2]['font200']?>;
	}
	.table{
		width: 100%;
		padding: 0px;
		border-spacing: 0px;
		margin-top: 5px;
		margin-bottom: 5px;
	}
	.table th{
		font-weight: bold;
		font-variant: small-caps;
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		background: #<?php echo $color_schemes[$design2]['900']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['900']?>), color-stop(50, #<?php echo $color_schemes[$design2]['500']?>), color-stop(100, #<?php echo $color_schemes[$design2]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
		<?php
			}
		?>
		color: #<?php echo $color_schemes[$design2]['font900']?>;
	}
	.table th, .table td{
		padding: 5px;
	}
	td{
		text-align: center;
	}
	.table td:first-child,th:first-child  {
		text-align: left;
	}
	.table tfoot{
		height: 3px;
	}
	.table tbody tr:hover td{
		-webkit-transition: all .5s ease;
		-moz-transition: all .5s ease;
		-o-transition: all .5s ease;
		transition: all .5s ease;
	} 
	.table tr:nth-child(even) {
		background: #<?php echo $color_schemes[$design2]['400']?>;
		color: #<?php echo $color_schemes[$design2]['font400']?>;
	}
	.table tr:nth-child(odd) {
		background: #<?php echo $color_schemes[$design2]['600']?>;
		color: #<?php echo $color_schemes[$design2]['font600']?>;
	}
	.table tbody tr:hover td{
		background: #<?php echo $color_schemes[$design2]['800']?>;
		color: #<?php echo $color_schemes[$design2]['font800']?>;
	}
	.table tfoot tr{
		background: #<?php echo $color_schemes[$design2]['900']?>;
		color: #<?php echo $color_schemes[$design2]['font900']?>;
	}
	.c98{
		width: 98%;
		margin: 0 auto;
	}
	#header, #main{
		background: #<?php echo $color_schemes[$design]['900']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['900']?>), color-stop(50, #<?php echo $color_schemes[$design]['800']?>), color-stop(100, #<?php echo $color_schemes[$design]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
		<?php
			}
		?>
	}
	.line{
		width: 99%;
		border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
	}
	.textDiv{
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		text-align: justify;
	}
	#settingsDiv{
		position:absolute;
		top:5px;
		right:10px;
		cursor:pointer;
		visibility:hidden;
		z-index:99
	}
	.tableDiv{
		display: table;
	}
	.rowDiv{
		display: table-row;
	}
	.cellDiv{
		display: table-cell;
		vertical-align: middle;
	}
	.digimeg-nav-wrap {
		position: relative;
		padding-left:0;
		font-family: <?php echo $designFont2?>, sans-serif;
	}
	.digimeg-nav-wrap .digimeg-main-nav {
		display: block;
		background: #<?php echo $color_schemes[$design2]['800']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design2]['900']?>), color-stop(50, #<?php echo $color_schemes[$design2]['700']?>), color-stop(100, #<?php echo $color_schemes[$design2]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design2]['900']?> 0%, #<?php echo $color_schemes[$design2]['700']?> 50%, #<?php echo $color_schemes[$design2]['900']?> 100%);
		<?php
			}
		?>
		color: white;
	}

	.digimeg-nav-wrap .digimeg-main-nav li {
		list-style-type: none;
		display: inline;
		float: left;
		padding:0;
	}
	<?php 
		if($_SESSION['user']=="admin"){
	?>
			.digimeg-main-nav li:last-child {
				float: right;
			}
	<?php
		}
	?>
	.digimeg-nav-wrap .digimeg-main-nav li a {
		display: block;
		padding-top: 0.9em;
		padding-bottom: 0.9em;
		padding-left: 1.1em;
		padding-right: 1.1em;
		text-decoration: none;
		
		color: #<?php echo $color_schemes[$design2]['100']?>;
		position: relative;
		font-weight: bold;
		<?php 
			if($menuLinksUpper){
		?>
				text-transform: uppercase;
				font-size: 0.9em;
		<?php 
			}
			else{
		?>
				font-size: 1em;
		<?php
			}
		?>
	}
	.digimeg-nav-wrap .digimeg-main-nav li a:hover {
		color: white;
	}

	.digimeg-group:after {
		content: '.';
		visibility: hidden;
		clear: both;
		display: block;
		line-height: 0px;
	}

	.digimeg-sub-nav li {
		display: block;
		width: 100%;
		list-style-type: none;
	}
	<?php 
		if($highlightMenuHover){
	?>
			.digimeg-nav-item:hover {
				background: #<?php echo $color_schemes[$design2]['400']?>;
			}
	<?php 
		}
	?>
	.digimeg-nav-item-content {
		padding: 30px;
		list-style-type: none;
	}

	.digimeg-nav-item-content h2 {
		font-size: 30px;
		font-weight: 400;
	}

	.digimeg-nav-chunk {
		display: block;
		padding: 20px;
		float: left;
	}

	.digimeg-sub-nav {
		position: relative;
		list-style-type: none;
		z-index:20;
		padding:0;
		margin:0;
	}

	.digimeg-sub-nav>li {
		position: absolute;
		top:0px;
		display: none;
		background: #<?php echo $color_schemes[$design2]['700']?>;
		<?php
			if($gradient){
		?>
			background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['900']?>), color-stop(50, #<?php echo $color_schemes[$design]['700']?>), color-stop(100, #<?php echo $color_schemes[$design]['900']?>));
			background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
			background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['700']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
		<?php
			}
		?>
		border-bottom: 20px solid #<?php echo $color_schemes[$design2]['400']?>;
		border-top: 1px solid #<?php echo $color_schemes[$design2]['400']?>;
	}
	#homeIcon{
		width: 32px;
		padding-left: 15px;
		padding-top: 4px;
		cursor: pointer;
		color: #<?php echo $color_schemes[$design2]['100']?>;
	}
	#homeIcon:hover{
		color: white;
	}
	.iconMenu,.astro{
		opacity: 0.8;
		cursor: pointer;
	}
	.iconMenu:hover{
		opacity: 1;
	}
	#stationStatus{
		font-variant:small-caps;
		font-weight: bold;
		text-align: center;
		width: 80px;
		margin-right: auto;
		margin-left: auto;
		padding-left: 5px;
		padding-right: 5px;
		border: 1px solid white;
	}
	.tooltipster-default {
		border: 2px solid #<?php echo $color_schemes[$design]['900']?>;
		background: #<?php echo $color_schemes[$design2]['700']?>;
		color: #<?php echo $color_schemes[$design2]['font700']?>;
	}
	#monthSelect .ui-datepicker-calendar {
		display: none;
	}
	.climateIcon{
		height:60px;
		opacity: 0.7;
		cursor: pointer;
	}
	.climateIcon:hover{
		opacity: 1;
	}
	#climateWorldIcon{
		width: 130px;
		opacity: 0.7;
		cursor: pointer;
	}
	#climateWorldIcon:hover{
		opacity: 1;
	}
	.flagIcon{
		width: 20px;
		opacity: 0.9;
	}
	.astronomyIcon{
		height: 30px;
		padding-right: 10px;
		opacity: 0.7;
	}
	.hoverIcon{
		height: 40px;
		opacity: 0.8;
		cursor: pointer;
	}
	.hoverIcon:hover{
		opacity: 1;
	}
	.menuHeading{
		font-family: "<?php echo $designFont2?>",Arial Narrow,Arial,Helvetica,sans-serif;
		font-weight: bold;
		text-align: left;
		font-size: 1.5em;
		margin-top: 0.05em;
		margin-bottom: 0.05em;
		font-variant: small-caps;
		color: #<?php echo $color_schemes[$design2]['300']?>;
		display: block;
	}
	.forceDefaultDatepicker .ui-widget-content{
		border: 1px solid #<?php echo $color_schemes[$design2]['900']?>;
		background: #<?php echo $color_schemes[$design2]['200']?>;
		background-color: #<?php echo $color_schemes[$design2]['200']?>;
	}
	.zeroSpacingPadding{
		border-spacing: 0px;
	}
	.zeroSpacingPadding td{
		padding: 0px;
	}
	.subheading{
		color: white;
		padding: 5px;
		padding-top: 10px;
		font-size: 1.2em;
		font-weight: bold;
	}
	.tooltipster-default {
		border: 2px solid #<?php echo $color_schemes[$design]['900']?>;
		background: #<?php echo $color_schemes[$design2]['700']?>;
		color: #<?php echo $color_schemes[$design2]['font700']?>;
	}
	#monthSelect .ui-datepicker-calendar {
		display: none;
	}
	.admin{
		width:17px;
		cursor:pointer;
		padding-top: 0;
		color: #<?php echo $color_schemes[$design2]['100']?>;
	}
	.admin:hover{
		color: white;
	}
	.tableSpacing2Padding2{
		border-spacing: 2px;
	}
	.tableSpacing2Padding2 td{
		padding: 2px;
	}
	.exportIcon{
		width: 50px;
		opacity: 0.6;
		cursor: pointer;
	}
	.exportIcon:hover{
		opacity: 1;
	}
	.exportDiv{
		width:99%;
		margin: 0 auto;
		text-align: right;
	}
	.ui-dialog{
		z-index: 9999;
	}
	::-webkit-scrollbar-track {
		background: #<?php echo $color_schemes[$design]['700']?>;
		margin-top: 5px;
	}
	::-webkit-scrollbar-thumb {
		background: #<?php echo $color_schemes[$design]['800']?>;
		margin-top: 5px;
	}
	::-webkit-scrollbar-thumb:window-inactive {
		background: #<?php echo $color_schemes[$design]['800']?>;
	}
	.ui-widget {
		font-family: <?php echo $designFont?>,Arial,sans-serif!important;
	}
	.ui-widget .ui-widget {
		font-size: 1em;
	}
	.ui-widget input,
	.ui-widget select,
	.ui-widget textarea,
	.ui-widget button {
		font-family: <?php echo $designFont?>,Arial,sans-serif!important;
	}

	.mtSpinner {
		width: 250px;
		opacity: 0.8;
		-webkit-animation: mtSpinnerAnimation 1.6s infinite ease;
		animation: mtSpinnerAnimation 1.6s infinite ease;
	}

	@-webkit-keyframes mtSpinnerAnimation {
		from {
			-webkit-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to   {
			-webkit-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}

	@keyframes mtSpinnerAnimation {
		from {
			-webkit-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to   {
			-webkit-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}
	<?php
		function generateBg($customBgArr, $customBgType, $customBgImg){
			$bgString = "";
			if($customBgType=="vertical"){
				$bgs = implode(",",$customBgArr);
				$bgString = "height: 100%;background-repeat: no-repeat;background: ".$customBgArr[0].";background: -webkit-linear-gradient(".$bgs.");background: -o-linear-gradient(".$bgs.");background: -moz-linear-gradient(".$bgs.");background: linear-gradient(".$bgs.");background-attachment: fixed;";
			}
			else if($customBgType=="horizontal"){
				$bgs = implode(",",$customBgArr);
				$bgString = "height: 100%;background-repeat: no-repeat;background: ".$customBgArr[0].";background: -webkit-linear-gradient(left,".$bgs.");background: -o-linear-gradient(right,".$bgs.");background: -moz-linear-gradient(right,".$bgs.");background: linear-gradient(to right,".$bgs.");background-attachment: fixed;";
			}
			else if($customBgType=="radial"){
				$bgs = implode(",",$customBgArr);
				$bgString = "height: 100%;background-repeat: no-repeat;background: ".$customBgArr[0].";background: -webkit-radial-gradient(".$bgs.");background: -o-radial-gradient(".$bgs.");background: -moz-radial-gradient(".$bgs.");background: radial-gradient(".$bgs.");background-attachment: fixed;";
			}
			else if($customBgType=="diagonal"){
				$bgs = implode(",",$customBgArr);
				$bgString = "height: 100%;background-repeat: no-repeat;background: ".$customBgArr[0].";background: -webkit-linear-gradient(left top,".$bgs.");background: -o-linear-gradient(bottom right,".$bgs.");background: -moz-linear-gradient(bottom right,".$bgs.");background: linear-gradient(to bottom right,".$bgs.");background-attachment: fixed;";
			}
			else if($customBgType=="image"){
				$bgString = "background: url(".$customBgImg.");background-size: 100% 100%;background-repeat: no-repeat;background-attachment: fixed;";
			}
			else{
				$bgString = "background:black;height:100%";
			}
			return $bgString;
		}

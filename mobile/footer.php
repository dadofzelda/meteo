	<style>
		.siteSelector{
			opacity: 0.8;
			padding-left:5px;
			padding-right:5px;
		}
		.siteSelector:hover{
			opacity: 1;
		}
	</style>
	<div id="footer">
		<table style="width:100%">
			<tr>
				<td style="width:20%">
					<?php 
						if($customFooterDisplay=="dateTime"){
							echo date($dateFormat)."<br>".date($timeFormat);
						}
						if($customFooterDisplay=="date"){
							echo date($dateFormat);
						}
						if($customFooterDisplay=="time"){
							echo date($timeFormat);
						}
						if($customFooterDisplay=="custom"){
							echo $customFooterText;
						}
					?>
				</td>
				<!-- __________________________________________________________ -->
				<!-- THIS PART OF THE FOOTER MUST NOT BE MODIFIED IN ANY WAY!!! -->
				<!-- __________________________________________________________ -->
				<!-- THE MIDDLE SECTION OF THE FOOTER MUST STAY EXACTLY AS IT IS, IT IS PART OF THE METEOTEMPLATE LICENSE THAT YOU DO NOT CHANGE THE MIDDLE SECTION OF THE FOOTER, COPYRIGHT MUST REMAIN TO METEOTEMPLATE AND NO TEXT MAY BE ADDED OR DELETED. THANKS! -->
				<td style='text-align:center'>
					<div style="display:inline-block;text-align:center; margin: 0 auto;font-size:1.1em">
						<table style="margin:0 auto" cellspacing="0" cellpadding="0">
							<tr>
								<td style="text-align:right">
									© <?php echo date('Y')?> 
								</td>
								<td style="text-align:center">
									Meteotemplate
								</td>
								<td style="text-align:left">
									<img src="<?php echo $pageURL.$path?>icons/footerIcon.png" style="width:15px">
								</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:center">
									<a href="<?php echo $meteotemplateURL?>"> meteotemplate.com</a>  
								</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:center">
									Meteotemplate <?php echo number_format($templateVersion,1,'.','')." ".$versionName;?>
								</td>
							</tr>
						</table>
					</div>
				</td>
				<!-- END OF MIDDLE FOOTER SECTION -->
				<td style="width:20%;text-align:right;padding-right:2%">
					<table style="width:100%">
						<tr>
							<td style="text-align:right">
								<?php
									if(isset($customPageSearch)){
										if($customPageSearch){
								?>
											<span class="admin fa fa-search fa-fw" id="googleSearchOpener" alt="" style="font-size:2em;padding-right:15px" onclick="$('#customGoogleSearch').slideToggle()"></span>
								<?php
										}
									}
								?>
								<?php
									if(isset($addSharer)){
										if($addSharer){
								?>
											<span class="admin fa fa-share-alt fa-fw" id="shareOpener" alt="" style="font-size:2em;padding-right:15px" onclick="checkSharer()"></span>
								<?php 
										}
									}
								?>
								<span class="admin fa fa-sitemap fa-fw tooltip" id="siteMapLink" alt="" style="font-size:2em" title="<?php echo lang('sitemap','c')?>"></span>
								<span id="siteSelector" class="admin fa fa-desktop fa-fw siteSelector tooltip" style="font-size:2em" title="<?php echo lang('desktop','c')?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
			if(isset($customPageSearch)){
				if($customPageSearch){
		?>
				<style>
					.gsc-control-cse.gsc-control-cse-cs{
						background: none;
					}
					.cse .gsc-control-cse, .gsc-control-cse{
						border: 0px;
					}
					input.gsc-search-button{
						background-color: #<?php echo $color_schemes[$design2]['700']?>;
						color: #<?php echo $color_schemes[$design2]['900']?>;
					}
					input.gsc-search-button:hover, input.gsc-search-button:focus{
						background-color: #<?php echo $color_schemes[$design]['700']?>;
					}
				</style>
				<div id="customGoogleSearch" style="display:none;width:90%;margin:0 auto;text-align:left">
					<script>
						(function() {
							var cx = '<?php echo $searchCode?>';
							var gcse = document.createElement('script');
							gcse.type = 'text/javascript';
							gcse.async = true;
							gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
							var s = document.getElementsByTagName('script')[0];
							s.parentNode.insertBefore(gcse, s);
						})();
						</script>
						<gcse:search></gcse:search>
				</div>
		<?php
				}
			}
		?>
		<?php
			if(isset($addSharer)){
				if($addSharer){
		?>
					<div id="sharerDiv" style="width:98%;margin:0 auto;text-align:right;display:none">
						<img src='<?php echo $pageURL.$path?>icons/logo.png' class='mtSpinner' style='width:20px;padding-right:10px'>
					</div>
					<input type="hidden" id="sharerDivVisibility" value="0">
					<script>
						<?php 
							$sharerURL =  (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							$sharerURL = urlencode($sharerURL);
						?>
						function checkSharer(){
							sharerVisibility = $("#sharerDivVisibility").val();
							// first load
							if(sharerVisibility==0){
								$("#sharerDiv").slideToggle();
								$("#sharerDiv").load("<?php echo $pageURL.$path?>scripts/sharer.php?url=<?php echo $sharerURL?>");
								$("#sharerDivVisibility").val(1);
							}
							else{
								if(sharerVisibility==1){
									$("#sharerDivVisibility").val(2);
									$("#sharerDiv").slideToggle();
								}
								else{
									$("#sharerDiv").slideToggle();
									$("#sharerDivVisibility").val(1);
								}
							}
						}
					</script> 
		<?php
				} 
			}
		?>
	</div>
	<div id="settingsDialog" title="Settings" style="text-align:center">
		<?php
			if($userCustomColor || $userCustomFont){
		?>
			<h2 style="color:white"><?php echo lang('design','c')?></h2>
			<a href="<?php echo $pageURL.$path?>css/settingExamples.php"><?php echo lang("choose design",'c')?>...</a>
		<br><br>
		<?php
			}
		?>
		<?php
			if($userCustomUnits){
		?>
			<h2 style="color:white"><?php echo lang('units','c')?></h2>
			<table style="margin-right:auto;margin-left:auto">
				<tr>
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userTemperature" class="button">
							<option value="C">C</option>
							<option value="F">F</option>
						</select>
					</td>
					<td style="width:20%" rowspan="3">
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userWind" class="button">
							<option value="kmh">km/h</option>
							<option value="ms">m/s</option>
							<option value="mph">mph</option>
							<option value="kt">kt</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/rain.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userRain" class="button">
							<option value="mm">mm</option>
							<option value="in">in</option>
						</select>
					</td>
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userPressure" class="button">
							<option value="hpa">hPa</option>
							<option value="mmhg">mmHg</option>
							<option value="inhg">inHg</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/cloudbase.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userCloudbase" class="button">
							<option value="m">m</option>
							<option value="ft">ft</option>
						</select>
					</td>
					<td style="text-align:left">
						<img src="<?php echo $pageURL.$path?>icons/visibility.png" class="customUnitsIcon" alt="">
					</td>
					<td style="text-align:right">
						<select id="userVisibility" class="button">
							<option value="m">m</option>
							<option value="km">km</option>
							<option value="mi">mi</option>
						</select>
					</td>
				</tr>
			</table>
			<?php
				}
			?>
			<h2 style="color:white"><?php echo lang('language','c')?></h2>
			<div style="width:50%;margin:0 auto">
				<?php
					$langAvailable = array();
					$temporaryLangs = glob($baseURL."lang/*.php");
					foreach($temporaryLangs as $temporaryLang){
						$langAvailable[] = str_replace(".php","",str_replace($baseURL."lang/","",$temporaryLang));
					}
					for($i=0;$i<count($langAvailable);$i++){
						$originalLangString = lang('language'.strtoupper($langAvailable[$i]),'w');
						// original language strings 
						if(strtoupper($langAvailable[$i]=="gb")){
							$originalLangString .= " (British English)";
						}
						if(strtoupper($langAvailable[$i]=="us")){
							$originalLangString .= " (American English)";
						}
						if(strtoupper($langAvailable[$i]=="cz")){
							$originalLangString .= " (Čeština)";
						}
						if(strtoupper($langAvailable[$i]=="de")){
							$originalLangString .= " (Deutsch)";
						}
						if(strtoupper($langAvailable[$i]=="fr")){
							$originalLangString .= " (Français)";
						}
						if(strtoupper($langAvailable[$i]=="es")){
							$originalLangString .= " (Español)";
						}
						if(strtoupper($langAvailable[$i]=="it")){
							$originalLangString .= " (Italiana)";
						}
						if(strtoupper($langAvailable[$i]=="hu")){
							$originalLangString .= " (Magyar)";
						}
						if(strtoupper($langAvailable[$i]=="dk")){
							$originalLangString .= " (Dansk)";
						}
						if(strtoupper($langAvailable[$i]=="nl")){
							$originalLangString .= " (Nederlands)";
						}
						if(strtoupper($langAvailable[$i]=="no")){
							$originalLangString .= " (Norsk)";
						}
						if(strtoupper($langAvailable[$i]=="fi")){
							$originalLangString .= " (Suomalainen)";
						}
						if(strtoupper($langAvailable[$i]=="se")){
							$originalLangString .= " (Svenska)";
						}
						if(strtoupper($langAvailable[$i]=="pt")){
							$originalLangString .= " (Português)";
						}
						if(strtoupper($langAvailable[$i]=="al")){
							$originalLangString .= " (Shqip)";
						}
						if(strtoupper($langAvailable[$i]=="gr")){
							$originalLangString .= " (Ελληνικά)";
						}
						if(strtoupper($langAvailable[$i]=="pl")){
							$originalLangString .= " (Polszczyzna)";
						}
						if(strtoupper($langAvailable[$i]=="sk")){
							$originalLangString .= " (Slovenčina)";
						}
						echo "<img src='".$pageURL.$path."imgs/".$flagIconShape."/big/".strtolower($langAvailable[$i]).".png' style='width:30px' class='langIcon tooltip' onclick=\"setLang('".strtolower($langAvailable[$i])."')\" id='lang".strtolower($langAvailable[$i])."' alt='' title='".$originalLangString."'>";
					}
				?>
			</div>
			<input type="hidden" id="userLang" value="<?php echo $lang?>">
		<br><br>
		<input type="button" value="<?php echo lang('ok','u')?>" id="saveSettings" class="button">
		<input type="button" value="<?php echo lang('reset to default','u')?>" id="resetDefaults" class="button">
	</div>
	<div id="templateHelpDialog" title="<?php echo lang('help','c')?>" style="text-align:center">
		<?php 
			$infoText = file_get_contents($baseURL."admin/infoPages.txt");
			$infoText = json_decode($infoText,true);
			if(isset($infoText['help'])){
				$helpText = "<div style='width:98%;margin:0 auto;text-align:justify'><p>".$infoText['help']."</p></div>";
			}
			else{
				$helpText = "";
			}
		?>
		<h2 style="color:white"><?php echo lang('icons','c')?></h2>
		<div class="iconHelpDiv">
			<span class="mticon-temp helpIcon"></span><br><?php echo lang('temperature','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-humidity helpIcon"></span><br><?php echo lang('humidity','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-pressure helpIcon"></span><br><?php echo lang('pressure','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-wind helpIcon"></span><br><?php echo lang('wind speed','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-gust helpIcon"></span><br><?php echo lang('wind gust','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-rain helpIcon"></span><br><?php echo lang('precipitation','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-sun helpIcon"></span><br><?php echo lang('solar radiation','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-snow helpIcon"></span><br><?php echo lang('snow','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-apparent helpIcon"></span><br><?php echo lang('apparent temperature','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-dewpoint helpIcon"></span><br><?php echo lang('dew point','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-uv helpIcon"></span><br><?php echo lang('uv','u')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-visibility helpIcon"></span><br><?php echo lang('visibility','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-cloudbase helpIcon"></span><br><?php echo lang('cloudbase','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-evapotranspiration helpIcon"></span><br><?php echo lang('evapotranspiration','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-indoortemp helpIcon"></span><br><?php echo lang('indoor temperature','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-indoorhumidity helpIcon"></span><br><?php echo lang('indoor humidity','c')?>
		</div>
		<br>
		<div class="iconHelpDiv">
			<span class="mticon-sunrise helpIcon"></span><br><?php echo lang('sunrise','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-sunset helpIcon"></span><br><?php echo lang('sunset','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-moonrise helpIcon"></span><br><?php echo lang('moon rise','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-moonset helpIcon"></span><br><?php echo lang('moon set','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-time helpIcon"></span><br><?php echo lang('time','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-bars helpIcon"></span><br><?php echo lang('menu','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-hourglass-start helpIcon"></span><br><?php echo lang('from','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-hourglass-end helpIcon"></span><br><?php echo lang('to','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-window-maximize helpIcon"  style="font-size:1.75em"></span><br><?php echo lang('fullscreen','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-image helpIcon"  style="font-size:1.75em"></span><br><?php echo lang('export as image','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-gear helpIcon" style="font-size:1.75em"></span><br><?php echo lang('settings','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="fa fa-home helpIcon" style="font-size:1.75em"></span><br><?php echo lang('homepage','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-webcam helpIcon"></span><br><?php echo lang('webcam','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-table helpIcon"></span><br><?php echo lang('table','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-graph helpIcon"></span><br><?php echo lang('graph','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-map helpIcon"></span><br><?php echo lang('map','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-trendneutral helpIcon"></span><br><?php echo lang('steady','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-trendup helpIcon"></span><br><?php echo lang('rising','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-trenddown helpIcon"></span><br><?php echo lang('falling','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-warninggeneral helpIcon"></span><br><?php echo lang('warning','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-lat helpIcon"></span><br><?php echo lang('latitude','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-lon helpIcon"></span><br><?php echo lang('longitude','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-history helpIcon"></span><br><?php echo lang('history','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-solar helpIcon"></span><br><?php echo lang('solar eclipse','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-lunar helpIcon"></span><br><?php echo lang('lunar eclipse','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-gps helpIcon"></span><br>GPS
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-elevation helpIcon"></span><br><?php echo lang('elevation','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-day helpIcon"></span><br><?php echo lang('day','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-month helpIcon"></span><br><?php echo lang('month','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-calendar-year helpIcon"></span><br><?php echo lang('year','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-hot helpIcon"></span><br><?php echo lang('high temperature','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-cold helpIcon"></span><br><?php echo lang('low temperature','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-sun helpIcon"></span><br><?php echo lang('day','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-night helpIcon"></span><br><?php echo lang('night','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-spring helpIcon"></span><br><?php echo lang('spring','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-summer helpIcon"></span><br><?php echo lang('summer','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-autumn helpIcon"></span><br><?php echo lang('autumn','c')?>
		</div>
		<div class="iconHelpDiv">
			<span class="mticon-snow helpIcon"></span><br><?php echo lang('winter','c')?>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tooltipster.js"></script>
	<script>
		$(function() {
			coord = "<?php echo $stationLat.",".$stationLon;?>";
			$div = $('#main');
			$( "#settingsDialog" ).dialog({
				autoOpen: false,
				show: {
					effect: "puff",
					duration: 500
				},
				hide: {
					effect: "puff",
					duration: 500
				},
				width: '70%',
				 my: "top",
				  at: "left top",
				  of: $div
			});
			$( "#templateHelpDialog" ).dialog({
				autoOpen: false,
				show: {
					effect: "puff",
					duration: 500
				},
				hide: {
					effect: "puff",
					duration: 500
				},
				width: (screen.width*0.7),
				 my: "top",
				  at: "left top",
				  of: $div
			});
			$( "#userSettings" ).click(function() {
				$("#settingsDialog" ).dialog( "open" );
			});
			$( "#templateHelp" ).click(function() {
				$("#templateHelpDialog" ).dialog( "open" );
			});
			$( "#siteSelector" ).click(function() {
				location = "<?php echo $pageURL.$path?>indexDesktop.php";
			});
			$("#siteMapLink").click(function(){
				window.location = "<?php echo $pageURL.$path?>sitemap.php";
			});
			$( "#saveSettings" ).click(function() {
				tempUnits = $("#userTemperature").val();
				windUnits = $("#userWind").val();
				rainUnits = $("#userRain").val();
				pressureUnits = $("#userPressure").val();
				visibilityUnits = $("#userVisibility").val();
				cloudbaseUnits = $("#userCloudbase").val();
				userLang = $("#userLang").val();

				$.ajax({url: "<?php echo $pageURL.$path?>userSettings.php?tempUnits="+tempUnits+"&windUnits="+windUnits+"&rainUnits="+rainUnits+"&pressureUnits="+pressureUnits+"&visibilityUnits="+visibilityUnits+"&cloudbaseUnits="+cloudbaseUnits+"&userLang="+userLang+"&design=<?php echo $design?>&design2=<?php echo $design2?>&designFont=<?php echo $designFont?>&designFont2=<?php echo $designFont2?>", success: function(result){
					$("#settingsDialog" ).dialog( "close" );
					location.reload();
				}});
			});
			$( "#resetDefaults" ).click(function() {
				$.ajax({url: "<?php echo $pageURL.$path?>userSettings.php?reset=1", success: function(result){
					$("#settingsDialog" ).dialog( "close" );
					location.reload();
				}});
			});
			<?php
				if($userCustomUnits==true || $userCustomFont==true || $userCustomColor==true || $userCustomLang==true){
			?>
				$('#settingsDiv').css('visibility','visible');
			<?php
				}
			?>
		});
		 function setLang(selected){
			  $("#userLang").val(selected);
			  $(".langIcon").css("opacity","0.7");
			  $(".langIcon").css("width","30px");
			  $("#lang"+selected).css("opacity","1");
			  $("#lang"+selected).css("width","40px");
		 }
	</script>
	<?php
		if(isset($errorLog)){
			showLog($errorLog);
		}
	?>
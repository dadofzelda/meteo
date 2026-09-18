<style>
		h1{
			font-size: 18px; 
		}
		h2{
			font-size: 20px;
		}
		@media all and (min-width: 960px) {
			body{
				font-size: 16px; 
			}
		}

		@media all and (max-width: 959px) and (min-width: 600px) {
			body{
				font-size: 16px; 
			}
			 .button{
				 font-size: 14px; 
			}
		}

		@media all and (max-width: 599px) and (min-width: 50px) {
			body{
				font-size: 16px; 
			}
			.button{
				font-size: 14px; 
			}
		}
		.block table th, td{
			padding: 0px;
		}
		#main{
				<?php
					if($theme=="dark"){
						echo "background: #".$color_schemes[$design]['600'].";";
						echo "color: white;";
					}
					if($theme=="light"){
						echo "background: #".$color_schemes[$design]['100'].";";
						echo "color: black;";
					}
				?>
			}
			h1, h2, h3{
				<?php
					if($theme=="dark"){
						echo "color: white;";
					}
					if($theme=="light"){
						echo "color: black;";
					}
				?>
			}
			<?php
				if($theme=="light"){
			?>
					.button {
						border: 1px solid #<?php echo $color_schemes[$design]['600']?>;
						color: #<?php echo $color_schemes[$design]['900']?>;
						background: #<?php echo $color_schemes[$design]['200']?>;
						<?php
							if($gradient){
						?>
							background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['300']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['300']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['300']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['100']?>), color-stop(50, #<?php echo $color_schemes[$design]['300']?>), color-stop(100, #<?php echo $color_schemes[$design]['100']?>));
							background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['300']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['300']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
						<?php
							}
						?>
					}
					.button:hover {
						background: #<?php echo $color_schemes[$design]['400']?>;
						color: #<?php echo $color_schemes[$design]['font400']?>;
					}
					#main a { 
						color:#<?php echo $color_schemes[$design]['800']?>;
					}
					#main a:link {
						color:#<?php echo $color_schemes[$design]['800']?>;
						font-variant: small-caps;
					}    
					#main a:visited {
						color:#<?php echo $color_schemes[$design]['800']?>;
					} 
			<?php
				}
			?>
			.block{
				<?php
					if($theme=="dark"){
				?>
						
						margin: 0 auto;
						margin-bottom:10px;
						border: <?php echo $customBlockBorderWidth?> solid #<?php echo $color_schemes[$design2]['700']?>;
						background: #<?php echo $color_schemes[$blockColor]['900']?>;
						<?php
							if($gradient){
						?>
								background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$blockColor]['900']?>), color-stop(50, #<?php echo $color_schemes[$blockColor]['800']?>), color-stop(100, #<?php echo $color_schemes[$blockColor]['900']?>));
								background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
						<?php
							}
						?>
						border-radius: <?php echo $customBlockRadius?>;
						padding: 0px;
						padding-top: 5px;
						padding-bottom: 5px;
						position: relative;
						-moz-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4);
						-webkit-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4);
						box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(255, 255, 255, .4);
				<?php
					}
					if($theme=="light"){
				?>
						
						margin: 0 auto;
						border: <?php echo $customBlockBorderWidth?> solid #<?php echo $color_schemes[$design2]['400']?>;
						background: #<?php echo $color_schemes[$blockColor]['300']?>;
						<?php
							if($gradient){
						?>
								background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$blockColor]['300']?>), color-stop(50, #<?php echo $color_schemes[$blockColor]['50']?>), color-stop(100, #<?php echo $color_schemes[$blockColor]['300']?>));
								background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
						<?php
							}
						?>
						border-radius: <?php echo $customBlockRadius?>;
						padding: 0px;
						padding-top: 5px;
						padding-bottom: 5px;
						margin-bottom: 10px;
						position: relative;
						-moz-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);
						-webkit-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);
						box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);
				<?php
					}
				?>
			}
			.details{
				display:none;
			}
			<?php 
				if(!$moreLinkHighlight){
			?>
					.more, #moreYrNo, #moreWU, .more2{
						font-weight: bold;
						font-variant: small-caps;
						cursor: pointer;
					}
					.more:hover, #moreYrNo:hover, #moreWU:hover, .more2:hover{
						color: #<?php echo $color_schemes[$design2]['500']?>;
					}
			<?php 
				}
				else{
			?>
					.more, #moreYrNo, #moreWU, .more2{
						font-weight: bold;
						font-variant: small-caps;
						cursor: pointer;
						font-size: 1.3em;
						border: 1px solid <?php echo $theme=="dark" ? "white" : "black"?>;
						padding: 1px;
						padding-left: 3px;
						padding-right: 3px;
						background: #<?php echo $color_schemes[$design2]['700']?>;
						color: white;
						position: relative;
						top: 5px;
						border-radius: 7px;
					}
					.more:hover, #moreYrNo:hover, #moreWU:hover, .more2:hover{
						color: #<?php echo $color_schemes[$design2]['200']?>;
						background: #<?php echo $color_schemes[$design2]['900']?>;
					}
			<?php 
				}
			?>
			.divIcon{
				position: absolute;
				top: 5px;
				left: 5px;
				z-index: 2;
				opacity: 0.8;
			}
			<?php
				if($theme=="dark"){
			?>
					.table th{
						background: #000000;
						<?php
							if($gradient){
						?>
							background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
							background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
							background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
							background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['900']?>), color-stop(50, #<?php echo $color_schemes[$design]['500']?>), color-stop(100, #<?php echo $color_schemes[$design]['900']?>));
							background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design2]['500']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
							background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
						<?php
							}
						?>
						color: #ffffff;
					}
					.table tr:nth-child(even) {
						background: #<?php echo $color_schemes[$design]['600']?>;
						color: #<?php echo $color_schemes[$design]['font600']?>;
					}
					.table tr:nth-child(odd) {
						background: #<?php echo $color_schemes[$design]['800']?>;
						color: #<?php echo $color_schemes[$design]['font800']?>;
					}
					.table tbody tr:hover td{
						background: #<?php echo $color_schemes[$design2]['800']?>;
						color: #<?php echo $color_schemes[$design2]['font800']?>;
					}
					.table tfoot tr{
						background: #<?php echo $color_schemes[$design]['900']?>;
						color: #<?php echo $color_schemes[$design]['font900']?>;
					}
			<?php
				}
			?>
			<?php
				if($theme=="light"){
			?>
					.table th{
						background: #<?php echo $color_schemes[$design]['100']?>;
						<?php
							if($gradient){
						?>
							background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['100']?>), color-stop(50, #<?php echo $color_schemes[$design]['500']?>), color-stop(100, #<?php echo $color_schemes[$design]['100']?>));
							background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
							background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['100']?> 0%, #<?php echo $color_schemes[$design]['500']?> 50%, #<?php echo $color_schemes[$design]['100']?> 100%);
						<?php
							}
						?>
						color: #ffffff;
					}
					.table tr:nth-child(even) {
						background: #<?php echo $color_schemes[$design]['300']?>;
						color: #<?php echo $color_schemes[$design]['font300']?>;
					}
					.table tr:nth-child(odd) {
						background: #<?php echo $color_schemes[$design]['400']?>;
						color: #<?php echo $color_schemes[$design]['font400']?>;
					}
					.table tbody tr:hover td{
						background: #<?php echo $color_schemes[$design]['200']?>;
						color: #<?php echo $color_schemes[$design]['font200']?>;
					}
					.table tfoot tr{
						background: #<?php echo $color_schemes[$design]['100']?>;
						color: #<?php echo $color_schemes[$design]['font100']?>;
					}
			<?php
				}
			?>
			.multipleTable{
				<?php
					if($theme=="dark"){
				?>
						
						
						border-radius: <?php echo $customBlockRadius?>;
				<?php
					}
					if($theme=="light"){
				?>
						
						
						border-radius: <?php echo $customBlockRadius?>;
				<?php
					}
				?>
				margin-bottom:10px;
			}
			.multipleTD{
				<?php 
					if($theme=="dark"){
				?>
						border: <?php echo $customBlockBorderWidth?> solid #<?php echo $color_schemes[$design2]['700']?>;
						border-radius: <?php echo $customBlockRadius?>;
						background: #<?php echo $color_schemes[$blockColor]['900']?>;
						<?php
							if($gradient){
						?>
								background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$blockColor]['900']?>), color-stop(50, #<?php echo $color_schemes[$blockColor]['800']?>), color-stop(100, #<?php echo $color_schemes[$blockColor]['900']?>));
								background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
								background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$blockColor]['900']?> 0%, #<?php echo $color_schemes[$blockColor]['800']?> 50%, #<?php echo $color_schemes[$blockColor]['900']?> 100%);
						<?php
							}
						?>
				<?php
					}
					if($theme=="light"){
				?>
						border: <?php echo $customBlockBorderWidth?> solid #<?php echo $color_schemes[$design2]['400']?>;
						border-radius: <?php echo $customBlockRadius?>;
						background: #<?php echo $color_schemes[$blockColor]['300']?>;
						<?php
							if($gradient){
						?>
								background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$blockColor]['300']?>), color-stop(50, #<?php echo $color_schemes[$blockColor]['50']?>), color-stop(100, #<?php echo $color_schemes[$blockColor]['300']?>));
								background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
								background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$blockColor]['300']?> 0%, #<?php echo $color_schemes[$blockColor]['50']?> 50%, #<?php echo $color_schemes[$blockColor]['300']?> 100%);
						<?php
							}
						?>
				<?php
					}
				?>
			}
			.shadow{
				<?php 
					if($theme=="light"){
				?>	
						text-shadow: 2px 1px 2px #000;
						font-weight: bolder;
				<?php
					}
				?>
				<?php 
					if($theme=="dark"){
				?>	
						text-shadow: 1px 1px 1px #000;
						font-weight: bolder;
				<?php
					}
				?>
			}
</style>
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
	#	Main table creation
	#
	# 	A script to create the main table for data from weather station.
	#
	############################################################################

	include("../config.php");

	// check if table already exists
	if(
		mysqli_num_rows(
			mysqli_query($con,
				"
					SHOW TABLES LIKE 'alldata'
				"
			)
		) > 0
	){
		die ("Table already exists.");
	}

	// create table
	mysqli_query($con,
		"	
	 CREATE  TABLE  `$dbName`.`alldata` (  `DateTime` datetime NOT  NULL ,
	 `T` decimal( 4, 1  )  DEFAULT  NULL ,
	 `Tmax` decimal( 4, 1  )  DEFAULT  NULL ,
	 `Tmin` decimal( 4, 1  )  DEFAULT  NULL ,
	 `H` decimal( 4, 1  )  DEFAULT  NULL ,
	 `D` decimal( 4, 1  )  DEFAULT  NULL ,
	 `W` decimal( 4, 1  )  DEFAULT  NULL ,
	 `G` decimal( 4, 1  )  DEFAULT  NULL ,
	 `B` decimal( 4, 1  )  DEFAULT  NULL ,
	 `RR` decimal( 7, 3  )  DEFAULT  NULL ,
	 `R` decimal( 7, 3  )  DEFAULT  NULL ,
	 `P` decimal( 7, 3  )  DEFAULT  NULL ,
	 `S` decimal( 5, 1  )  DEFAULT NULL ,
	 `A` decimal( 4, 1  )  DEFAULT NULL ,
	 PRIMARY  KEY (  `DateTime`  ) ,
	 UNIQUE  KEY  `DateTime` (  `DateTime`  ) 
	  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8 COLLATE  = utf8_unicode_ci;
		"
	);

	// check if table was created successfully
	if(
		mysqli_num_rows(
			mysqli_query($con,
				"
					SHOW TABLES LIKE 'alldata'
				"
			)
		) > 0
		or die ("Table was not created, please check your MySQL setup.")
	){
		echo "Table created!";
	}
?>
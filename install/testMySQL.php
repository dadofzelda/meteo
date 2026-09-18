<?php
	
	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	error_reporting(0);
	$host = $_POST['host'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$dbName = $_POST['name'];
	
	$con=mysqli_connect($host,$user,$pass,$dbName);
	if (!$con) {
		echo "Error: Unable to connect to MySQL.<br>";
		echo "Debugging error number: " . mysqli_connect_errno() . "<br>";
		echo "Debugging error: " . mysqli_connect_error() ."<br>";
		exit;
	}

	echo "Success: A proper connection to MySQL was made!<br><br>";
	
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

	mysqli_close($con);
?>
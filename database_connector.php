<?php
	$host="localhost";
	$user="root";
	$password="";
	$dbName="dungeon_db";
	
	$con=mysqli_connect($host,$user,$password,$dbName);
	
	if(!$con)
		echo "Could not connect to database";
?>
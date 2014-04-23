<?php
	session_start();
	if(!isset($_SESSION['username']))
	{
		session_destroy();
		header("location:index.php");
	}
	if(isset($_POST['logout']))
		header("location:logout.php");
	if(isset($_POST['location']))
		header("location:location.php");
	if(isset($_POST['explore']))
		header("location:explore.php");
	if(isset($_POST['create']))
		header("location:create.php");
	if(isset($_POST['edit']))
		header("location:edit.php");
	if(isset($_POST['favorites']))
		header("location:favorites.php");		

	include 'database_connector.php';
?>
	
<html>
<head>
	<title>Welcome to Dungeon Designer!</title>
	<link rel="stylesheet" type="text/css" href="site.css">
	<style>
	div.menubar
	{
		border:1px solid white;
		display:flex;
		padding-left: 5px;
		width:650px;
		margin:auto;
	}
	</style>
</head>
<body>
	<div class="menubar">
		<form style="float:left;margin:20px;" method="post" action="location.php">
			<input type="submit" name="location" value="Camp">
		</form>
		<form style="float:left;margin:20px;" method="post" action="explore.php">
			<input type="submit" name="explore" value="Explore!">
		</form>
		<form style="float:left;margin:20px;" method="post" action="create.php">
			<input type="submit" name="create" value="Create">
		</form>
		<form style="float:left;margin:20px;" method="post" action="edit.php">
			<input type="submit" name="edit" value="Edit">
		</form>
		<form style="float:left;margin:20px;" method="post" action="favorites.php">
			<input type="submit" name="favorites" value="Favorites">
		</form>
	
		<form style="float:left;margin:20px;" method="post" action="logout.php">
			<input type="submit" name="logout" value="Log Out">
		</form>
	</div>
	
	<?php
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$UID=$UID[0];
		echo "</br></br><h3>Your Favorited Dungeons</h3>";		
		$dungeons=mysqli_query($con,"select * from rooms where 
		RoomID IN(select RoomID from favorites where UserID = '$UID')");
		while ($d=mysqli_fetch_array($dungeons))
		{
			echo"<form style='border:1px solid;width:1000px;padding:2px;margin:20px;margin-left:150px;' method='post' action='explore.php'>";
				echo "<img src='cave.png' width='50px' style='float:left;'>";
				echo "<p align='left'>$d[1]</p>";
				echo "<input type='hidden' name='to' value='$d[0]'>";
				echo "<input style='position:relative;left:400px;' type='submit' name='go' value='Go!'>";
			echo"</form>";
		}
	?>
</body>
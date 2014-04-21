<?php
	session_start();
	if(!isset($_SESSION['username']))
	{
		session_destroy();
		header("location:index.php");
	}
	if(isset($_POST['back']))
		header("location:location.php");
	include 'database_connector.php';
	
	if(isset($_POST['change']))
	{
		$m1=$_POST['m1'];
		$m2=$_POST['m2'];
		$m3=$_POST['m3'];

		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$UID=$UID[0];
		mysqli_query($con,"update movesequipped set Move1ID='$m1', Move2ID='$m2', Move3ID='$m3' where UserID='$UID'");
		//mysqli_query($con,"insert into MovesEquipped values('$UID','$m1','$m2','$m3')");
	}
?>
	
<html>
<head>
	<title>Welcome to Dungeon Designer!</title>
	<link rel="stylesheet" type="text/css" href="site.css">
</head>
<body>

	<div>
	<form style="float:left;margin:20px;" method="post" action="location.php">
		<input type="submit" name="back" value="< Back">
	</form>
	</div>
	
	<?php
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$UID=$UID[0];
		$moves=mysqli_query($con,"select * from moves where MoveID IN (select MoveID from userknowsmove where UserID='$UID')");
		echo"<form style='float:left;margin-top:100px;margin-left:150px;' method='post' action='moves.php'>";
		echo"Move 1:</br>
		<select name='m1'>";
		while ($move=mysqli_fetch_array($moves))
		{
			echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
		}
		echo"</select> </br></br>";
		$moves=mysqli_query($con,"select * from moves where MoveID IN (select MoveID from userknowsmove where UserID='$UID')");
		echo"Move 2:</br>
		<select name='m2'>";
		while ($move=mysqli_fetch_array($moves))
		{
			echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
		}
		echo"</select> </br></br>";
		$moves=mysqli_query($con,"select * from moves where MoveID IN (select MoveID from userknowsmove where UserID='$UID')");
		echo"Move 3:</br>
		<select name='m3'>";
		while ($move=mysqli_fetch_array($moves))
		{
			echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
		}
		echo"</select> </br></br>";
		
		echo"<input type='submit' name='change' value='Change Moves'>";
		echo"</form>";
	
	?>

</body>
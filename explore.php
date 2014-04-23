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
	
	if(isset($_POST['go']))
		enter($_POST['to']);
		
	function enter ($tolocation)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
		$UID=$user[0];
		$curlocation=$user[7];
		$_SESSION['back']=$curlocation;
		$enemies=mysqli_query($con,"select * from roomhasmonster where RoomID='$tolocation'");
		//create enemy instances
		if(mysqli_fetch_array(mysqli_query($con,"select * from currentinstancemonsters where Location='$tolocation' and UserID='$UID'"))===null)
		{while ($enemy=mysqli_fetch_array($enemies))
		{
			$type=$enemy[2];
			$class=$enemy[1];
			$enemytype=mysqli_fetch_array(mysqli_query($con,"select * from monsters where MonsterID='$type'"));
			$enemyclass=mysqli_fetch_array(mysqli_query($con,"select * from monsterclasses where MonsterClassID='$class'"));
			$str=$enemyclass[2];
			$int=$enemyclass[3];
			$cons=$enemyclass[5];
			$wil=$enemyclass[4];
			$health=10*($wil+$cons)+10;
			$m1p=$enemytype[3];
			$m2p=$enemytype[5];
			$m3p=$enemytype[7];

			$m=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$enemytype[2]' "));
			$cr=($m[4]+($m[2]*$str)+($m[3]*$int))*$m1p;
			if($m2p>0)
			{
				$m=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$enemytype[4]'"));
				$cr+=($m[4]+($m[2]*$str)+($m[3]*$int))*$m2p;
			}
			if($m3p>0)
			{
				$m=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$enemytype[6]'"));
				$cr+=($m[4]+($m[2]*$str)+($m[3]*$int))*$m3p;
			}
			
			$cr=$cr/100;
			for ($i=0;$i<$enemy[3];$i++)
			{
				mysqli_query($con,"insert into currentinstancemonsters values(0,$type,$class,$health,$cr,$tolocation,$UID)");
				
			}
		}}
		//set user location
		mysqli_query($con,"update users set Location='$tolocation' where UserID='$UID'");
		//change to location
		header("location:location.php");
	}
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
	
	<form style="margin-bottom:20px;margin-top:20px;"method="post" action="location.php">
		<input type="submit" value="Continue Exploring Current Dungeon">
	</form>
	
	<?php
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
		$UID=$user[0];
		echo"</br>";
		if($user[3]<5)
		{
			echo "<h3>Tutorial</h3>";
			$d=mysqli_fetch_array(mysqli_query($con,"select * from rooms where RoomID=1"));
			echo"<form style='border:1px solid;padding:2px;width:1000px;margin:20px;margin-left:150px;' method='post' action='explore.php'>";
				echo "<img src='cave.png' width='50px' style='float:left;'>";
				echo "<p align='left'>$d[1]</p>";
				echo "<input type='hidden' name='to' value='$d[0]'>";
				echo "<input style='position:relative;left:400px;' type='submit' name='go' value='Go!'>";
			echo"</form>";
		}
		
		echo"</br>";
		echo "<h3>You see three dungeons to explore</h3>";
		$dungeons=mysqli_query($con,"select * from rooms where isEntryPoint=1 and RoomID<>1 and
		RoomID NOT IN(select RoomID from completed where UserID='$UID')
		ORDER BY RAND() LIMIT 3");
		while ($d=mysqli_fetch_array($dungeons))
		{
			echo"<form style='border:1px solid;padding:2px;width:1000px;margin:20px;margin-left:150px;' method='post' action='explore.php'>";
				echo "<img src='cave.png' width='50px' style='float:left;'>";
				echo "<p align='left'>$d[1]</p>";
				echo "<input type='hidden' name='to' value='$d[0]'>";
				echo "<input style='position:relative;left:400px;' type='submit' name='go' value='Go!'>";
			echo"</form>";
		}
		
		echo"</br></br>";
		echo "<h3>Your Dungeons</h3>";		
		$dungeons=mysqli_query($con,"select * from rooms where isEntryPoint=1 and 
		RoomID IN(select RoomID from roommadeby where CreatorID = '$UID')");
		while ($d=mysqli_fetch_array($dungeons))
		{
			echo"<form style='border:1px solid;padding:2px;width:1000px;margin:20px;margin-left:150px;' method='post' action='explore.php'>";
				echo "<img src='cave.png' width='50px' style='float:left;'>";
				echo "<p align='left'>$d[1]</p>";
				echo "<input type='hidden' name='to' value='$d[0]'>";
				echo "<input style='position:relative;left:400px;' type='submit' name='go' value='Go!'>";
			echo"</form>";
		}
		if($user[3]>=5)
		{
			echo "<h3>Tutorial</h3>";
			$d=mysqli_fetch_array(mysqli_query($con,"select * from rooms where RoomID=1"));
			echo"<form style='border:1px solid;padding:2px;width:1000px;margin:20px;margin-left:150px;' method='post' action='explore.php'>";
				echo "<img src='cave.png' width='50px' style='float:left;'>";
				echo "<p align='left'>$d[1]</p>";
				echo "<input type='hidden' name='to' value='$d[0]'>";
				echo "<input style='position:relative;left:400px;' type='submit' name='go' value='Go!'>";
			echo"</form>";
		}
	?>
	
</body>
</html>
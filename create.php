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
	

	if(isset($_POST['weapon']))
	{	if(!isset($_POST['name'])||!isset($_POST['description'])
		||!isset($_POST['stat1'])||!isset($_POST['stat2']))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>";
		else
			createweapon($_POST['name'],$_POST['description'],$_POST['stat1'],$_POST['stat2']);
	}
	if(isset($_POST['armor']))
	{	if(!isset($_POST['name'])||!isset($_POST['description'])
		||!isset($_POST['stat1'])||!isset($_POST['stat2']))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>";
		else
			createarmor($_POST['name'],$_POST['description'],$_POST['stat1'],$_POST['stat2']);
	}
	if(isset($_POST['item']))
	{	if(!isset($_POST['name'])||!isset($_POST['description'])
		||!isset($_POST['value']))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>"; 
		else
			createitem($_POST['name'],$_POST['description'],$_POST['value']);
	}
	if(isset($_POST['move']))
	{	if(!isset($_POST['name'])||!isset($_POST['smult'])
		||!isset($_POST['imult'])||!isset($_POST['dmg']))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>";
		else
			createmove($_POST['name'],$_POST['smult'],$_POST['imult'],$_POST['dmg']);
	}
	if(isset($_POST['enemy']))
	{	
		if(!isset($_POST['m1p']))
			echo "<p style='color:red;'>Error: Move 1 must have a probability</p>";
		else
			$m1p=$_POST['m1p'];
		if($_POST['m2']=='NULL')
			$m2p=0;
		else
			$m2p=$_POST['m2p'];
		if($_POST['m3']=='NULL')
			$m3p=0;
		else
			$m3p=$_POST['m3p'];
		if(!isset($_POST['name'])
		||!isset($m2p)||!isset($m3p))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>";
		else
			createenemy($_POST['name'],$_POST['m1'],$m1p,$_POST['m2'],$m2p,$_POST['m3'],$m3p);
	}	
	if(isset($_POST['enemyclass']))
	{	
		if(!isset($_POST['name'])||!isset($_POST['stat1'])||!isset($_POST['cts'])
		||!isset($_POST['stat2'])||!isset($_POST['stat3'])||!isset($_POST['stat4']))
			echo "<p style='color:red;'>Error: Be sure to include all needed values</p>";
		else
			createenemyclass($_POST['name'],$_POST['stat1'],$_POST['stat2'],$_POST['stat3'],$_POST['stat4'],$_POST['cts']);
	}	
	function createenemy($name,$m1,$m1p,$m2,$m2p,$m3,$m3p)
	{
		include 'database_connector.php';

		if($m1p<0||$m2p<0||$m3p<0)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		if($m1p+$m2p+$m3p!==100)
		{ echo "<p style='color:red;'>Error: Be sure your move probabilities sum to 100</p>"; return;}
		if($m1p<1||($m2p<1&&$m2!=='NULL')||($m3p<1&&$m3!=='NULL'))
		{ echo "<p style='color:red;'>Error: Be sure your move probabilities are not zero</p>"; return;}
		$success=mysqli_query($con,"insert into monsters values (0,\"$name\",\"$m1\",\"$m1p\",$m2,\"$m2p\",$m3,\"$m3p\")");
		if ($success)
			echo "Enemy added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name (don't use \"s), or enemy already exists</p>";
	}
	function createenemyclass($name,$strength,$intelligence,$constitution,$willpower,$chance)
	{
		include 'database_connector.php';

		if($strength<0 || $intelligence<0||$constitution<0 || $willpower<0 || $chance<0 || $chance>100)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where username = '$username'"));
		$UID=$UID[0];
		$success=mysqli_query($con,"insert into monsterclasses values (0,\"$name\",\"$strength\",\"$intelligence\",\"$willpower\",\"$constitution\",$UID,\"$chance\")");
		if ($success)
			echo "Enemy class added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name (don't use \"s), or class already exists</p>";

	}
	function createweapon($name,$description,$strength,$intelligence)//+more
	{
		include 'database_connector.php';

		if($strength<0 || $intelligence<0)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		$success=mysqli_query($con,"insert into weapons values (0,\"$description\",\"$name\",\"$strength\",\"$intelligence\")");
		if ($success)
			echo "Weapon added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name/description (don't use \"s), or item already exists</p>";
	}
	function createarmor($name,$description,$constitution,$willpower)
	{
		include 'database_connector.php';

		if($constitution<0 || $willpower<0)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		$success=mysqli_query($con,"insert into armor values (0,\"$description\",\"$name\",\"$constitution\",\"$willpower\")");
		if ($success)
			echo "Armor added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name/description (don't use \"s), or item already exists</p>";
		
	}
	function createitem($name,$description,$value)
	{
		include 'database_connector.php';

		if($value<0)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		$success=mysqli_query($con,"insert into items values (0,\"$name\",\"$description\",\"false\",\"$value\")");
		if ($success)
			echo "Item added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name/description (don't use \"s), or item already exists</p>";
	}
	function createmove($name,$strengthmod,$intelligencemod,$damage)
	{
		include 'database_connector.php';

		if($strengthmod<0.0||$intelligencemod<0.0||$damage<0)
		{ echo "<p style='color:red;'>Error: Some fields were invalid (values shouldn't be negative)</p>"; return;}
		$success=mysqli_query($con,"insert into moves values (0,\"$name\",\"$strengthmod\",\"$intelligencemod\",\"$damage\")");
		if ($success)
			echo "Move added";
		else	
			echo "<p style='color:red;'>Error: Name too long, invalid name (don't use \"s), or move already exists</p>";
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
	<script type="text/javascript">
	function showform(form)
	{
		var wform,aform,iform,mform,eform,ecform;
		wform=document.getElementById('weaponform');
		aform=document.getElementById('armorform');
		iform=document.getElementById('itemform');
		mform=document.getElementById('moveform');
		eform=document.getElementById('enemyform');
		ecform=document.getElementById('enemyclassform');
		wform.style='display:none;';
		aform.style='display:none;';
		iform.style='display:none;';
		mform.style='display:none;';
		eform.style='display:none;';
		ecform.style='display:none;';
		if(form=='weaponform')
			wform.style='display:visible;';
		else if(form=='armorform')
			aform.style='display:visible;';
		else if(form=='itemform')
			iform.style='display:visible;';
		else if(form=='moveform')
			mform.style='display:visible;';
		else if(form=='enemyform')
			eform.style='display:visible;';
		else if(form=='enemyclassform')
			ecform.style='display:visible;';
	}
</script>
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

	<form>
		<h3>What would you like to make?</h3> 
		<select id='cmode' onchange='showform(this.value)' >
			<option value='enemyform'>Enemy</option>
			<option value='enemyclassform'>Enemy Class</option>
			<option value='weaponform'>Weapon</option>
			<option value='armorform'>Armor</option>
			<option value='itemform'>Item</option>
			<option value='moveform'>Move</option>
		</select>
	</form>
	</br>
	<form style='display:none;' id='weaponform' method='post' action='create.php'>

			Weapon Name:</br>
			<input type='text' name='name'></br></br>
			Weapon Description:</br>
			<textarea rows='3' cols='30' name='description'></textarea></br></br>
			Strength:</br>
			<input type='number' name='stat1'></br></br>
			Intelligence:</br>
			<input type='number' name='stat2'></br></br>
			
		<input type='submit' name='weapon' value='Create Weapon'>
	</form>
	
	<form style='display:none;' id='armorform' method='post' action='create.php'>

			Armor Name:</br>
			<input type='text' name='name'></br></br>
			Armor Description:</br>
			<textarea rows='3' cols='30' name='description'></textarea></br></br>
			Constitution:</br>
			<input type='number' name='stat1'></br></br>
			Willpower:</br>
			<input type='number' name='stat2'></br></br>
			
		<input type='submit' name='armor' value='Create Armor'>
	</form>
		
	<form style='display:none;' id='itemform' method='post' action='create.php'>

			Item Name:</br>
			<input type='text' name='name'></br></br>
			Item Description:</br>
			<textarea rows='3' cols='30' name='description'></textarea></br></br>
			Value:</br>
			<input type='number' name='value'></br></br>
			
		<input type='submit' name='item' value='Create Item'>
	</form>
	
	<form style='display:none;' id='moveform' method='post' action='create.php'>

			Move Name:</br>
			<input type='text' name='name'></br></br>
			Strength Multiplier:</br>
			<input type='text' name='smult'></br></br>
			Intelligence Multiplier:</br>
			<input type='text' name='imult'></br></br>
			Base Damage:</br>
			<input type='number' name='dmg'></br></br>
			
		<input type='submit' name='move' value='Create Move'>
	</form>
		
	<form id='enemyform' method='post' action='create.php'>
	
			Enemy Name:</br>
			<input type='text' name='name'></br></br>
			
			<?php
			$moves=mysqli_query($con,"select * from moves");
			echo"Move 1:</br>
			<select name='m1'>";
			while ($move=mysqli_fetch_array($moves))
			{
				echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
			}
			echo"</select> </br></br>";
			echo"
			Move 1 Probablity (%1-100):</br>
			<input type='number' name='m1p'></br></br>";
			
			$moves=mysqli_query($con,"select * from moves");
			echo"Move 2:</br>
			<select name='m2'>";
			echo "<option value='NULL'>None</option>'";
			while ($move=mysqli_fetch_array($moves))
			{
				echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
			}
			echo"</select> </br></br>";
			
			echo"
			Move 2 Probability (%1-100):</br>
			<input type='number' name='m2p'></br></br>";
			
			$moves=mysqli_query($con,"select * from moves");
			echo"Move 3:</br>
			<select name='m3'>";
			echo "<option value='NULL'>None</option>'";
			while ($move=mysqli_fetch_array($moves))
			{
				echo"<option value='$move[0]'>$move[1] (Base Damage: $move[4],  Strengh Modifier: $move[2],  Intelligence Modifier: $move[3]) </option>";
			}
			echo"</select> </br></br>";
			
			echo"
			Move 3 Probability (%1-100):</br>
			<input type='number' name='m3p'></br></br>";
			?>
			
		<input type='submit' name='enemy' value='Create enemy'>
	</form>
	
	<form style='display:none;' id='enemyclassform' method='post' action='create.php'>

		Class Descriptor:</br>
		<input type='text' name='name'></br></br>
		Strength:</br>
		<input type='number' name='stat1'></br></br>
		Intelligence:</br>
		<input type='number' name='stat2'></br></br>
		Constitution:</br>
		<input type='number' name='stat3'></br></br>
		Willpower:</br>
		<input type='number' name='stat4'></br></br>
		Chance to Strike (%0-100):</br>
		<input type='number' name='cts'></br></br>
			
		<input type='submit' name='enemyclass' value='Create Enemy Class'>
	</form>
	
</body>
</html>
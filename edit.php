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
	if(isset($_POST['finalize']))
	{
		if(isset($_POST['isentry']))
			$entry=true;
		else
			$entry=false;
		submit($_POST['itemvalues'],$_POST['typevalues'],$_POST['classvalues'],$_POST['doorvalues'],
		$_POST['doordvalues'],$_POST['description'],$entry,$_POST['cdescription']);
	}
	
	function submit($itemvalues,$typevalues,$classvalues,$doorvalues,$doordvalues,$description,$isentry,$cdescription)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$UID=$UID[0];
		mysqli_query($con,"insert into rooms values (0,\"$description\",'$isentry',\"$cdescription\")");
		$roomid=mysqli_insert_id($con);
		if($roomid==0)
			return;
		echo "New room id is $roomid";
		mysqli_query($con,"insert into roommadeby values ('$roomid',\"$UID\")");
		
		$items=array();
		$item=strtok($itemvalues,",");
		while ($item!==false)
		{	
			array_push($items,$item);
			$itementry=mysqli_fetch_array(mysqli_query($con,"select * from roomhasitem where RoomID='$roomid' and ItemID = '$item'"));
			if($itementry===null)
				mysqli_query($con,"insert into roomhasitem values ('$roomid',\"$item\",1)");
			else
			{
				$newhas=$itementry[2]+1;
				mysqli_query($con,"update roomhasitem set Amount=$newhas where RoomID='$roomid' and ItemID = '$item'");
			}
			$item=strtok(",");
		}
		
		$types=array();
		$type=strtok($typevalues,",");
		while ($type!==false)
		{	
			array_push($types,$type);
			$type=strtok(",");
		}
		
		$classes=array();
		$class=strtok($classvalues,",");
		while ($class!==false)
		{	
			array_push($classes,$class);
			$class=strtok(",");
		}
		
		for($i=0;$i<count($types);$i++)
		{
			$monsterentry=mysqli_fetch_array(mysqli_query($con,"select * from roomhasmonster 
			where RoomID='$roomid' and ClassID = '$classes[$i]' and MonsterID = '$types[$i]'"));
			if($monsterentry===null)
			 mysqli_query($con,"insert into roomhasmonster values ('$roomid', '$classes[$i]', '$types[$i]',1)");
			else
			{
				$newhas=$monsterentry[3]+1;
				mysqli_query($con,"update roomhasmonster set Amount=$newhas 
				where RoomID='$roomid' and ClassID = '$classes[$i]' and MonsterID = '$types[$i]'");
			}
		
		}
		
		$doords=array();
		$doord=strtok($doordvalues,"`");
		while ($doord!==false)
		{
			array_push($doords,$doord);
			$doord=strtok("`");
		}
		$doors=array();
		$door=strtok($doorvalues,",");
		while ($door!==false)
		{	
			array_push($doors,$door);
			$door=strtok(",");
		}
		for($i=0;$i<count($doors);$i++)
		{
			mysqli_query($con,"insert into roomleadsto values ('$roomid','$doors[$i]',\"$doords[$i]\")");
		}
		//for each value string make an array
		//add each item for each array, accounting for repeats
	
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
	var items='';
	var monsters='';
	var monsterclasses='';
	var doorsto='';
	var doorstod='';
	
	function recordItem()
	{
		var newitem=document.getElementById('itemselect').value;
		var newitemtext=document.getElementById('itemselect').options[document.getElementById('itemselect').selectedIndex].text;
		items+=newitem;
		items+=',';
		var box=document.getElementById('showbox');
		box.innerHTML+='</br> <u>Item added</u>: ';
		box.innerHTML+=newitemtext;
	}
	function recordEnemy()
	{
		var newclass=document.getElementById('classselect1').value;
		var newtype=document.getElementById('monsterselect').value;
		var newclasstext=document.getElementById('classselect1').options[document.getElementById('classselect1').selectedIndex].text;
		var newtypetext=document.getElementById('monsterselect').options[document.getElementById('monsterselect').selectedIndex].text;
		monsters+=newtype;
		monsters+=',';
		monsterclasses+=newclass;
		monsterclasses+=',';
		var box=document.getElementById('showbox');
		box.innerHTML+='</br> <u>Enemy added</u>: </br>Type: ';
		box.innerHTML+=newclasstext+'</br>Class: ';
		box.innerHTML+=newtypetext;
	}
	function recordDoor()
	{
		var newdoor=document.getElementById('roomselect').value;
		var newdoord=document.getElementById('doordescription').value;
		var newdoortext=newdoord;
		doorsto+=newdoor;
		doorsto+=',';
		doorstod+=newdoord;
		doorstod+='`';
		var box=document.getElementById('showbox');
		box.innerHTML+='</br> <u>Door added</u> ' ;
		box.innerHTML+=newdoortext;
		newdoortext=document.getElementById('roomselect').options[document.getElementById('roomselect').selectedIndex].text;
		box.innerHTML+=' to ';
		box.innerHTML+=newdoortext;
	}
	
	function sendValues()
	{
		var form=document.getElementById('main');
		var newinput=document.createElement('input');
		newinput.name="itemvalues";
		newinput.value=items;
		newinput.type="hidden";
		form.appendChild(newinput);
		newinput=document.createElement('input');
		newinput.name="typevalues";
		newinput.value=monsters;
		newinput.type="hidden";
		form.appendChild(newinput);
		newinput=document.createElement('input');
		newinput.name="classvalues";
		newinput.value=monsterclasses;
		newinput.type="hidden";
		form.appendChild(newinput);
		newinput=document.createElement('input');
		newinput.name="doorvalues";
		newinput.value=doorsto;
		newinput.type="hidden";
		form.appendChild(newinput);
		newinput=document.createElement('input');
		newinput.name="doordvalues";
		newinput.value=doorstod;
		newinput.type="hidden";
		form.appendChild(newinput);
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
	
	<form id="main" style="float:left;margin:20px;margin-left:150px;" method="post" action="edit.php">
		<?php
			$username=$_SESSION['username'];
			$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
			$UID=$UID[0];
			$myrooms=mysqli_query($con,"select RoomID from roommadeby where CreatorID=\"$UID\"");
			echo"Door leads to:</br>
			<select style='width:250px;' id='roomselect'>";
			while ($item=mysqli_fetch_array($myrooms))
			{
				$roominfo=mysqli_fetch_array(mysqli_query($con,"select * from rooms where RoomID='$item[0]'"));
				$blurb=substr($roominfo[1],0,65);
				echo"<option value='$item[0]'>($item[0]) $blurb... </option>";
			}
			echo"</select></br>";
			echo "Door description: </br><textarea rows='3' cols='30' id='doordescription'></textarea></br>";
			
		?>
		<input type="button" value="Add a door" onclick='recordDoor()'></br></br>
		<?php
			$monsters=mysqli_query($con,"select * from monsterclasses");
			echo"Enemy Class:</br>
			<select id='classselect1'>";
			while ($monster=mysqli_fetch_array($monsters))
			{
				echo"<option value='$monster[0]'>$monster[1]: ";
				echo "Str:$monster[2], Int:$monster[3], Con:$monster[4], Wil:$monster[5], CtS:$monster[7]</option>";
			}
			echo"</select>";
			
			$username=$_SESSION['username'];
			$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
			$UID=$UID[0];
			$monsters=mysqli_query($con,"select * from monsterclasses where ClassMadeBy = '$UID'");
			echo "<select style='display:none;' id='classselect2'>";
			while ($monster=mysqli_fetch_array($monsters))
			{
				echo"<option value='$monster[0]'>$monster[1]: ";
				echo "Str:$monster[2], Int:$monster[3], Con:$monster[5], Wil:$monster[4], CtS:$monster[7]</option>";
			}
			echo"</select>";
			
			echo"</br>";
		
			$monsters=mysqli_query($con,"select * from monsters");
			echo"Enemy Type:</br>
			<select id='monsterselect'>";
			while ($monster=mysqli_fetch_array($monsters))
			{
				$move=mysqli_fetch_array(mysqli_query($con,"select MoveName from moves where MoveID=\"$monster[2]\""));
				$move=$move[0];
				echo"<option value='$monster[0]'>$monster[1] ";
				echo "(Knows: $move";
				if(isset($monster[4]))
				{
					$move=mysqli_fetch_array(mysqli_query($con,"select MoveName from moves where MoveID=\"$monster[4]\""));
					$move=$move[0];
					echo ", $move";
				}
				if(isset($monster[6]))
				{
					$move=mysqli_fetch_array(mysqli_query($con,"select MoveName from moves where MoveID=\"$monster[6]\""));
					$move=$move[0];
					echo ", $move";
				}
				echo")</option>";
			}
			echo"</select></br>";
			echo "<input type='button' value='Add an enemy' onclick='recordEnemy()'></br></br>";
		?>
		<?php
			$items=mysqli_query($con,"select * from items where ItemValue=0");
			echo"Item:</br>
			<select id='itemselect'>";
			while ($item=mysqli_fetch_array($items))
			{
				echo"<option value='$item[0]'>$item[1] </option>";
			}
			echo"</select>";
		?>
		<input type="button" value="Add an item" onclick="recordItem()"></br></br>
		Dungeon Description:</br>
		<textarea rows='3' cols='30' name='description'></textarea></br>
		Dungeon Description (Completed):</br>
		<textarea rows='3' cols='30' name='cdescription'></textarea></br>
		
		Is this an entry point? <input type='checkbox' name='isentry'></br></br>
		<input type="submit" name="finalize" value="Finalize Dungeon" onclick="sendValues()">
		<input type="submit" value="Reset">
	</form>
	
	<div id='showbox' style="float:right;border:2px solid;width:420px;margin:20px;margin-right:150px;">
		<b>Room Elements</b>
	</div>
	
</body>
</html>
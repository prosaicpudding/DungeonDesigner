<?php
	session_start();
	
	if(!isset($_SESSION['username']))
	{
		session_destroy();
		header("location:index.php");
	}
	if(isset($_POST['logout']))
		header("location:logout.php");
	if(isset($_POST['explore']))
		header("location:explore.php");
	if(isset($_POST['create']))
		header("location:create.php");
	if(isset($_POST['edit']))
		header("location:edit.php");
	if(isset($_POST['favorites']))
		header("location:favorites.php");	
	if(isset($_POST['examine']))
		examine($_POST['exname'],$_POST['exdesc'],$_POST['extype'],$_POST['exstat1'],$_POST['exstat2']);
	if(isset($_POST['buy']))
		buy($_POST['bid'],$_POST['bprice'],$_POST['btype']);
	if(isset($_POST['take']))
		take($_POST['tid'],$_POST['tcount']);
	include 'database_connector.php';

	if(isset($_POST['inspect']))
	{
		$inspectquery=$_POST['inspectquery'];
		mysqli_query($con,$inspectquery);
	}
	
	if(isset($_POST['location']))
	{	
		if($_POST['location']==='Camp')
		{
			//delete all current instance monster entries
			$username=$_SESSION['username'];
			$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
			$UID=$UID[0];
			$lvl=mysqli_fetch_array(mysqli_query($con,"select Level from users where Username = '$username'"));
			$lvl=$lvl[0];
			$h=$lvl*10;
			
			mysqli_query($con,"delete from currentinstancemonsters where UserID='$UID'");
			mysqli_query($con,"delete from currentinstanceloot where UserID='$UID'");
			//delete all completed entries
			mysqli_query($con,"delete from completed where UserID='$UID'");
			//set user location to camp
			mysqli_query($con,"update users set Location='0' where UserID='$UID'");
			mysqli_query($con,"update users set Health='$h' where UserID='$UID'");
		}
		header("location:location.php");
	}
	
	
	if(isset($_POST['favorite']))
	{//add a dungeon to favorites
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$UID=$UID[0];
		$where=$_POST['to'];
		mysqli_query($con,"insert into favorites values ('$UID','$where')");
	}
	
	if(isset($_POST['plusr']))
	{//Add one point of resilience
		$username=$_SESSION['username'];
		$R=mysqli_fetch_array(mysqli_query($con,"select Resilience from users where Username = '$username'"));
		$R=$R[0];
		$r=$R+1;
		mysqli_query($con,"update users set Resilience='$r' where Username='$username'");
	}
	if(isset($_POST['plusp']))
	{//Add one point of perseverance
		$username=$_SESSION['username'];
		$P=mysqli_fetch_array(mysqli_query($con,"select Perseverance from users where Username = '$username'"));
		$P=$P[0];
		$p=$P+1;
		mysqli_query($con,"update users set Perseverance='$p' where Username='$username'");
	}
	
	function examine($name,$description,$type,$stat1,$stat2)
	{//Examine a single weapon, armor or item
		$level=ceil(($stat1+$stat2)/2);
		echo"<script type='text/javascript'>alert(\"$name\\n\\n$description\\n";
		if($type=='armor')
		{
			echo "(equip)\\nLevel $level\\n+$stat1 Constitution \\n+$stat2 Willpower";
		}
		else if($type=='weapon')
		{
			echo "(equip)\\nLevel $level\\n+$stat1 Strength \\n+$stat2 Intelligence";
		}
		echo "\");</script>";
	}
	
	function buy($bid,$price,$type)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
		$UID=$user[0];
		if ($price<=$user[4])
		{
			if($type=='armor')
			{
				$item=mysqli_fetch_array(mysqli_query($con,"select * from armoratcamp where ArmorID='$bid'"));
				if($item==null)
					mysqli_query($con,"insert into armoratcamp values ('$UID','$bid',1)");
				else
				{
					$newamount=$item[2]+1;
					mysqli_query($con,"update armoratcamp set Amount='$newamount' where UserID='$UID' and ArmorID='$bid'");
				}
			}
			else if($type=='weapon')
			{
				$item=mysqli_fetch_array(mysqli_query($con,"select * from weaponsatcamp where WeaponID='$bid'"));
				if($item==null)
					mysqli_query($con,"insert into weaponsatcamp values ('$UID','$bid',1)");
				else
				{
					$newamount=$item[2]+1;
					mysqli_query($con,"update weaponsatcamp set Amount='$newamount' where UserID='$UID' and WeaponID='$bid'");
				}
			}
			else
			{
				$item=mysqli_fetch_array(mysqli_query($con,"select * from userinventory where ItemID='$bid'"));
				if($item==null)
					mysqli_query($con,"insert into userinventory values ('$UID','$bid',1)");
				else
				{
					$newamount=$item[2]+1;
					mysqli_query($con,"update userinventory set Amount='$newamount' where UserID='$UID' and ItemID='$bid'");
				}
			}
		}
		$newgold=$user[4]-$price;
		mysqli_query($con,"update users set Gold='$newgold' where UserID='$UID'");
	}
	function take($tid,$tcount)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
		$location=$user[7];
		$UID=$user[0];
		mysqli_query($con,
		"delete from currentinstanceloot where Location='$location' and UserID='$UID' and ItemID='$tid'");
		for($i=0;$i<$tcount;$i++)
			buy($tid,0,'item');
	
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
		margin-bottom:30px;
	}
	</style>
	<script>

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
	
	
	<div style="float:left;margin-left:60px;margin-bottom:20px;width:350px;">
		<?php
			$username=$_SESSION['username'];
			$location=mysqli_fetch_array(mysqli_query($con,"select Location from users where Username = '$username'"));
			$location=$location[0];
			$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
			$UID=$UID[0];
			$com=mysqli_fetch_array(mysqli_query($con,"select * from completed where UserID = '$UID' and RoomID = '$location'"));
			if($com===null)
				$description=mysqli_fetch_array(mysqli_query($con,"select RoomDescription from rooms where RoomID = '$location'"));
			else
				$description=mysqli_fetch_array(mysqli_query($con,"select RoomCompleteDescription from rooms where RoomID = '$location'"));
			$description=$description[0];
			echo"<em>";
			echo $description;
			echo"</em>";
			
			//check which move is being used
			if(isset($_POST['use1']))
			{
				$moves=mysqli_fetch_array(mysqli_query($con,"select * from movesequipped where UserID='$UID'"));
				turn($moves[1],$_POST['on']);
			}
			if(isset($_POST['use2']))
			{
				$moves=mysqli_fetch_array(mysqli_query($con,"select * from movesequipped where UserID='$UID'"));
				turn($moves[2],$_POST['on']);
			}
			if(isset($_POST['use3']))
			{
				$moves=mysqli_fetch_array(mysqli_query($con,"select * from movesequipped where UserID='$UID'"));
				turn($moves[3],$_POST['on']);
			}
			function turn($use,$target)
			{//this function runs one whole single turn
				include 'database_connector.php';
				$username=$_SESSION['username'];
				$location=mysqli_fetch_array(mysqli_query($con,"select Location from users where Username = '$username'"));
				$location=$location[0];
				$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
				$UID=$UID[0];
				$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username='$username'"));
								
				//if health<0 send the user back to camp
				if ($user[5]<=0)
				{
					echo"<script type='text/javascript'>alert('You are too weak to go on. Return to camp to rest and regain health.');</script>";
					return;
				}
				
				//do damage
				$enemy=mysqli_fetch_array(mysqli_query($con,"select * from currentinstancemonsters where InstanceID='$target'"));
				$type=mysqli_fetch_array(mysqli_query($con,"select * from monsters where MonsterID='$enemy[1]'"));
				$class=mysqli_fetch_array(mysqli_query($con,"select * from monsterclasses where MonsterClassID='$enemy[2]'"));
				$move=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$use'"));
				$weapon=mysqli_fetch_array(mysqli_query($con,"select * from weapons where WeaponID='$user[9]'"));
				$cons=$class[5];
				$will=$class[4];
				$pers=$user[11];
				
				$strdmg=$move[2]*$weapon[3]-$cons;
				if($strdmg<0)
					$strdmg=0;
				$intdmg=$move[3]*$weapon[4]-$will;
				if($intdmg<0)
					$intdmg=0;
				$basedmg=$move[4]+($pers/2);
				$dmg=round($intdmg+$strdmg+$basedmg);
				$newhealth=$enemy[3]-$dmg;
				mysqli_query($con,"update currentinstancemonsters set Health = '$newhealth' where InstanceID='$target'");
				echo"<p style='color:red;'> You used $move[1] on $class[1] $type[1]</br> for $dmg damage</p>";

				$enemies=mysqli_query($con,"select * from currentinstancemonsters where UserID='$UID' and Location='$location'");
				while($enemy=mysqli_fetch_array($enemies))	
				{//determine who attacks, and show damage for each monster
					$type=mysqli_fetch_array(mysqli_query($con,"select * from monsters where MonsterID='$enemy[1]'"));
					$class=mysqli_fetch_array(mysqli_query($con,"select * from monsterclasses where MonsterClassID='$enemy[2]'"));
					if($enemy[0]!==$target)
					{	
						$att=rand(1,100);
						if($att>$class[7])
							continue;
					}
					$m1p=$type[3];
					$m2p=$type[5];
					$m3p=$type[7];
					$m1v=$m1p;
					$m2v=$m1p+$m2p;
					$mv=rand(1,100);
					if($m1v>=$mv)
						$mid=$type[2];
					else if($m2v>=$mv)
						$mid=$type[4];
					else
						$mid=$type[6];
					$move=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$mid'"));
					
					$armor=mysqli_fetch_array(mysqli_query($con,"select * from armor where ArmorID='$user[8]'"));
					$cons=$armor[3];
					$will=$armor[4];
					$res=$user[10];
					
					$strdmg=$move[2]*$class[4]-$cons;
					if($strdmg<0)
						$strdmg=0;
					$intdmg=$move[3]*$class[5]-$will;
					if($intdmg<0)
						$intdmg=0;
					$basedmg=$move[4]-($res/2);
					if($basedmg<0)
						$basedmg=0;
					$dmg=round($intdmg+$strdmg+$basedmg);
					$newhealth=$user[5]-$dmg;
					mysqli_query($con,"update users set Health='$newhealth' where UserID='$UID'");
					$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username='$username'"));
					echo"<p style='color:red;'> $class[1] $type[1] uses $move[1]</br> for $dmg damage</p>";
					if ($enemy[3]<=0)
					{
						echo"<p style='color:red;'> You have defeated $class[1] $type[1]</p>";
						$newexp=$user[6]+$enemy[4]*10;
						mysqli_query($con,"update users set Experience='$newexp' where UserID='$UID'");
						mysqli_query($con,"delete from currentinstancemonsters where InstanceID='$enemy[0]'");
						$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username='$username'"));
						if(!(($move[3]>0 &&$class[5]=0)||($move[2]>0 &&$class[4]=0)))
						{
							//check if move learned
							$pl=rand(0,250);
							if($pl<=$class[7])
							{
								if(mysqli_fetch_array(mysqli_query($con,"select * from userknowsmove where MoveID='$mid'"))===null)
								{
									echo"<p style='color:red;'> You learned $move[1] from $class[1] $type[1]</p>";
									mysqli_query($con,"insert into userknowsmove values ('$UID','$mid')");
								}
							}
						}
					}
				}
				//check levels
				while($user[6]>=$user[3]*100)
				{
					$newexp=$user[6]-$user[3]*100;
					$newlevel=$user[3]+1;
					$newhealth=$newlevel*10;
					mysqli_query($con,"update users set Experience='$newexp' where UserID='$UID'");
					mysqli_query($con,"update users set Level='$newlevel' where UserID='$UID'");
					mysqli_query($con,"update users set Health='$newhealth' where UserID='$UID'");
					$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username='$username'"));
					echo"<p style='color:red;'> Leveled up!</p>";
				}
				
				//if all monsters defeated, show loot and refresh
				if(mysqli_fetch_array(mysqli_query($con,"select * from currentinstancemonsters where Location='$location' and UserID='$UID'"))===null)
				{
					//get other loot
					$enemies=mysqli_query($con,"select * from roomhasmonster where RoomID='$location'");
					$cr=0;
					while ($enemy=mysqli_fetch_array($enemies))
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
						$cr+=($m[4]+($m[2]*$str)+($m[3]*$int))*$m1p;
					}
					$rewardtype=rand(1,3);
					$rewardgold=rand(ceil($cr/2),($cr));
					$newgold=$rewardgold+$user[4];
					mysqli_query($con,"update users set Gold='$newgold' where UserID='$UID'");
					if($rewardtype==1)
					{
						$reward=mysqli_fetch_array(mysqli_query($con,"select * from items where ItemValue<='$cr' and ItemValue>0 ORDER BY RAND() LIMIT 1"));
						$rewardname=$reward[1];
						$reward=$reward[0];
						$item=mysqli_fetch_array(mysqli_query($con,"select Amount from userinventory where UserID='$UID' and ItemID='$reward'"));
						if($item!=null)
						{	
							$newamount=$item[0]+1;
							mysqli_query($con,"update userinventory set Amount='$newamount' where ItemID='$reward' and UserID='$UID'");
						}
						else
							mysqli_query($con,"insert into userinventory values('$UID','$reward',1)");
					}
					else if($rewardtype==2)
					{
						$reward=mysqli_fetch_array(mysqli_query($con,"select * from armor where ((ArmorConstitution+ArmorWillpower)*10)<='$cr' and ArmorID>0 ORDER BY RAND() LIMIT 1"));
						$rewardname=$reward[2];
						$reward=$reward[0];
						$item=mysqli_fetch_array(mysqli_query($con,"select Amount from armoratcamp where UserID='$UID' and ArmorID='$reward'"));
						if($item!=null)
						{	
							$newamount=$item[0]+1;
							mysqli_query($con,"update armoratcamp set Amount='$newamount' where ArmorID='$reward' and UserID='$UID'");
						}
						else
							mysqli_query($con,"insert into armoratcamp values('$UID','$reward',1)");
					}
					else
					{
						$reward=mysqli_fetch_array(mysqli_query($con,"select * from weapons where (10*(WeaponIntelligence+WeaponStrength))<='$cr' and WeaponID>0 ORDER BY RAND() LIMIT 1"));
						$rewardname=$reward[2];
						$reward=$reward[0];
						$item=mysqli_fetch_array(mysqli_query($con,"select Amount from weaponsatcamp where UserID='$UID' and WeaponID='$reward'"));
						if($item!=null)
						{	
							$newamount=$item[0]+1;
							mysqli_query($con,"update weaponsatcamp set Amount='$newamount' where WeaponID='$reward' and UserID='$UID'");
						}
						else
							mysqli_query($con,"insert into weaponsatcamp values('$UID','$reward',1)");
					
					}
					echo"<p style='color:red;'>You pick up $rewardgold gold pieces along with a $rewardname</p>";
				}
				
			}
		?>
	</div>
	
	<div style="float:left;width:400px;margin-left:50px;">
	<?php
		$username=$_SESSION['username'];
		$location=mysqli_fetch_array(mysqli_query($con,"select Location from users where Username = '$username'"));
		$location=$location[0];
		$com=mysqli_fetch_array(mysqli_query($con,"select * from completed where UserID = '$UID' and RoomID = '$location'"));
		
		if($location!=0&&$com===null)
		{
			$to=$_SESSION['back'];
			if($to!=0)
				echo
				"<form style='float:left;' method='post' action='explore.php'>
					<input type='hidden' name='to' value='$to'>
					<input type='submit' name='go' value='Turn Back'>
				</form>";
		}
		//add back to camp button for entry points?
	?>
	<div style="background-color:white;border:3px solid;float:left;width:400px;">
	<?php
		$username=$_SESSION['username'];
		$userid=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		$userid=$userid[0];
		$UID=$userid;
		$location=mysqli_fetch_array(mysqli_query($con,"select Location from users where Username = '$username'"));
		$location=$location[0];
		$num_items=mysqli_num_rows(mysqli_query($con,"select * from items"));
		$num_armor=mysqli_num_rows(mysqli_query($con,"select * from armor"));
		$num_weapons=mysqli_num_rows(mysqli_query($con,"select * from weapons"));
		if($location==0)//print merchant items
		{
			$iq=mysqli_query($con,"select * from items where ItemUnique='false' and ItemValue>0 ORDER BY RAND(dayofyear(CURRENT_DATE)) LIMIT 2");
			$aq=mysqli_query($con,"select * from armor where ArmorID > 0 ORDER BY RAND(dayofyear(CURRENT_DATE)) LIMIT 2");
			$wq=mysqli_query($con,"select * from weapons where WeaponID > 0 ORDER BY RAND(dayofyear(CURRENT_DATE)) LIMIT 2");
			echo "Merchant's Stock";
			for($i=0; $i<6; $i++)
			{
				//select from DB
				if($i==0||$i==1)
				{
					$itemtype='item';
					//get item result
					$item=mysqli_fetch_array($iq);
					$item_name=$item[1];
					$item_description=$item[2];
					$item_price=$item[4];
					$item_stat1=0;
					$item_stat2=0;
				}
				else if($i==4||$i==5)
				{
					$itemtype='armor';
					$item=mysqli_fetch_array($aq);
					$item_name=$item[2];
					$item_description=$item[1];
					$item_stat1=$item[3];
					$item_stat2=$item[4];
					$item_price=100*$item[3]+100*$item[4];
				}
				else
				{
					$itemtype='weapon';
					$item=mysqli_fetch_array($wq);
					$item_name=$item[2];
					$item_description=$item[1];
					$item_stat1=$item[3];
					$item_stat2=$item[4];
					$item_price=100*$item[3]+100*$item[4];
				}
					$itemID=$item[0];
					if ($item_price==0)
						$item_price=10;
					echo 
					"<div style='border-top:1px solid;'> 
						<b>$item_name</b> for $item_price gold
						<form style='position:relative;left:100px;' method='post' action='location.php'>
							<input type='text' name='btype' value='$itemtype' style='display:none'>
							<input type='text' name='bprice' value='$item_price' style='display:none'>
							<input type='text' name='bid' value='$itemID' style='display:none'>
							<input type='submit' name='buy' value='Buy'>

							<input type='text' name='exname' value=\"$item_name\" style='display:none'>
							<input type='text' name='exdesc' value=\"$item_description\" style='display:none'>
							<input type='text' name='extype' value='$itemtype' style='display:none'>
							<input type='text' name='exstat1' value='$item_stat1' style='display:none'>
							<input type='text' name='exstat2' value='$item_stat2' style='display:none'>
							<input type='submit' name='examine' value='Examine'>
						</form>
					</div>";
			}
		}
		else //print room's monsters or doors
		{
			$com=mysqli_fetch_array(mysqli_query($con,"select * from completed where UserID = '$userid' and RoomID = '$location'"));
			if($com!==null)
			{
				$doors=mysqli_query($con,"select * from roomleadsto where Room1ID='$location'");
				echo"Doors you see";
				while($door=mysqli_fetch_array($doors))
				{//for each door show description, relative challenge and go
					$compl=mysqli_fetch_array(mysqli_query($con,"select * from completed where UserID = '$userid' and RoomID = '$door[1]'"));
					if($compl!=null)
						$compl='<b>(Completed)</b>';
					$description=$door[2];
					echo 
					"<div style='border-top:1px solid;'> 
						$description $compl</br></br>
						<form style='position:relative;left:100px;' method='post' action='explore.php'>
							
							<input type='hidden' name='to' value='$door[1]'>
							<input type='submit' name='go' value='Go!'>
						</form>
					</div>";
				}
				$doors=mysqli_query($con,"select * from roomleadsto where Room2ID='$location'");
				while($door=mysqli_fetch_array($doors))
				{//for each door show description, relative challenge and go
					$compl=mysqli_fetch_array(mysqli_query($con,"select * from completed where UserID = '$userid' and RoomID = '$door[0]'"));
					if($compl!=null)
						$compl='<b>(Completed)</b>';
					$description=$door[2];
					echo 
					"<div style='border-top:1px solid;'> 
						$description $compl</br></br>
						<form style='position:relative;left:100px;' method='post' action='explore.php'>
							
							<input type='hidden' name='to' value='$door[0]'>
							<input type='submit' name='go' value='Go!'>
						</form>
					</div>";
				}
				$items=mysqli_query($con,"select * from currentinstanceloot where Location='$location' and UserID='$UID'");
				if(mysqli_fetch_array(mysqli_query($con,"select * from currentinstanceloot where Location='$location' and UserID='$UID'"))!==null)
				echo"<div style='border-top:2px solid;'> 
					You also notice
					</div>";
				while($item=mysqli_fetch_array($items))
				{
					$itementry=mysqli_fetch_array(mysqli_query($con,"select * from items where ItemID='$item[0]'"));
					echo 
					"<div style='border-top:1px solid;'> 
						$item[3] x $itementry[1]</br></br>
						<form style='position:relative;left:100px;' method='post' action='location.php'>
							<input type='text' name='tcount' value='$item[3]' style='display:none'>
							<input type='text' name='tid' value='$item[0]' style='display:none'>
							<input type='submit' name='take' value='Take'>

							<input type='text' name='exname' value=\"$itementry[1]\" style='display:none'>
							<input type='text' name='exdesc' value=\"$itementry[2]\" style='display:none'>
							<input type='text' name='extype' value='item' style='display:none'>
							<input type='text' name='exstat1' value='0' style='display:none'>
							<input type='text' name='exstat2' value='0' style='display:none'>
							<input type='submit' name='examine' value='Examine'>
						</form>
					</div>";
				
				}
			}
			else
			{
				$mobs=mysqli_query($con,"select * from currentinstancemonsters where Location='$location' and UserID='$UID'");
				$num_mobs=mysqli_num_rows($mobs);
				if($num_mobs==0)
				{
					//mysqli_query($con,"insert into completed values('$UID','$location')");
					$items=mysqli_query($con,"select * from roomhasitem where RoomID='$location'");
					while ($item=mysqli_fetch_array($items))
					{
						mysqli_query($con,"insert into currentinstanceloot values('$item[1]','$location','$UID','$item[2]')");
					}
					echo
					"What would you like to do?
					<form style=''method='post' action='location.php'>
						<input type='hidden' name='inspectquery' value=\"insert into completed values('$UID','$location')\">
						<input type='submit' name='inspect' value='Inspect Location'>
					</form>";
					//header("location:location.php");
				}
				else
				{
					$moves=mysqli_fetch_array(mysqli_query($con,"select * from movesequipped where UserID='$UID'"));
					$move1=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$moves[1]'"));
					$move2=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$moves[2]'"));
					$move3=mysqli_fetch_array(mysqli_query($con,"select * from moves where MoveID='$moves[3]'"));
					echo "Enemies you see";
					while($mob=mysqli_fetch_array($mobs))
					{//for each monster type show all monsters and store health
						$type=mysqli_fetch_array(mysqli_query($con,"select * from monsters where MonsterID='$mob[1]'"));
						$class=mysqli_fetch_array(mysqli_query($con,"select * from monsterclasses where MonsterClassID='$mob[2]'"));
						echo 
						"<div style='border-top:1px solid;'> 
							$class[1] $type[1] with $mob[3] health </br></br>
							<form style='position:relative;' method='post' action='location.php'>
								<input type='hidden' name='on' value='$mob[0]'>
								<input type='submit' name='use1' value=\"Use $move1[1]\">
								<input type='submit' name='use2' value=\"Use $move2[1]\">
								<input type='submit' name='use3' value=\"Use $move3[1]\">
							</form>
						</div>";
					}
				}
			}
		}
	?>
	</div>
	</div>
	
	<div style="float:right;margin-right:50px;width:350px;">
	<div style="width:350px;border:1px solid;">
		Hero Information
		<?php
			$un=$_SESSION['username'];
			$userinfo=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$un'"),MYSQLI_NUM);
			$maxhealth=10*$userinfo[3];
			$maxexp=100*$userinfo[3];
			$armor=mysqli_fetch_array(mysqli_query($con,"select * from armor where ArmorID= '$userinfo[8]'"));
			$cons=$armor[3];
			$will=$armor[4];
			$weapon=mysqli_fetch_array(mysqli_query($con,"select * from weapons where WeaponID= '$userinfo[9]'"));
			$str=$weapon[3];
			$int=$weapon[4];
			//if no weapon 1 or 2, set false
			
			if($userinfo[3]>($userinfo[10]+$userinfo[11]))
			{ 
				$points=$userinfo[3]-($userinfo[10]+$userinfo[11]);
				echo 
				"</br></br><form align='left' method='post' action='location.php'>
					Points to spend:<b>$points</b> </br>
					<input type='submit' name='plusr' value='+1 Resilience'>
					<input type='submit' name='plusp' value='+1 Perseverance'>
				</form>
				";
			}
			echo
			"<p align='left' >
				<b>Level:</b> $userinfo[3]<br><br>
				<b>Experience:</b> $userinfo[6]/$maxexp<br><br>
				<b>Health:</b> $userinfo[5]/$maxhealth<br><br>
				<b>Gold:</b> $userinfo[4]<br><br>
				<b>Perseverance:</b> $userinfo[11]<br><br>
				<b>Resilience:</b> $userinfo[10]<br><br>
				<b>Strength:</b> $str<br><br>
				<b>Intelligence:</b> $int<br><br>
				<b>Constitution:</b> $cons<br><br>
				<b>Willpower:</b> $will<br><br>
				<b>Wearing:</b> $armor[2]<br><br>
				<b>Wielding:</b> $weapon[2] <br>

			</p>";

		
		?>
	</div>
	<?php
		$username=$_SESSION['username'];
		$location=mysqli_fetch_array(mysqli_query($con,"select Location from users where Username = '$username'"));
		$location=$location[0];
		
		if($location==0)
		echo
		'<form style="float:left;" method="post" action="moves.php">
			<input type="submit" name="moves" value="Select Moves">
		</form>';
		else 
		{
			$locinfo=mysqli_fetch_array(mysqli_query($con,"select isEntryPoint from rooms where RoomID='$location'"));
			$isEntry=$locinfo[0];
			if($isEntry==true)
				echo
				"<form style='float:left;' method='post' action='location.php'>
					<input type='hidden' name='to' value='$location'>
					<input type='submit' name='favorite' value='Add Dungeon to Favorites'>
				</form>";
		}
		?>
		<form style="float:right;"method="post" action="inventory.php">
			<input type='submit' name='inventory' value='Inventory'>
		</form>
	</div>	


	
</body>
</html>
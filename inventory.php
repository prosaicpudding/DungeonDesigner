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

	if(isset($_POST['examine']))
	{
		examine($_POST['exname'],$_POST['exdesc'],$_POST['extype'],$_POST['exstat1'],$_POST['exstat2']);
	}
	function examine($name,$description,$type,$stat1,$stat2)
	{
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
	
	if(isset($_POST['sell']))
	{
		sell($_POST['sid'],$_POST['stype']);
	}
	function sell ($sellid,$selltype)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where username = '$username'"));
		$UID=$UID[0];
		$gold=mysqli_fetch_array(mysqli_query($con,"select Gold from users where UserID='$UID'"));
		$gold=$gold[0];

		if($selltype=='item')
		{//mysqli_fetch_array(mysqli_query($con,""));
			$itementry=mysqli_fetch_array(mysqli_query($con,"select * from userinventory where UserID='$UID' and ItemID = '$sellid'"));
			$price=mysqli_fetch_array(mysqli_query($con,"select ItemValue from items where ItemID = '$sellid'"));
			$newgold=$gold+(ceil($price[0]/10));
			$owned=$itementry[2];
			if ($owned>1)
			{
				$newamount=$owned-1;
				mysqli_query($con,"update userinventory set Amount = '$newamount' where UserID='$UID' and ItemID = '$sellid'");
			}
			else
			{
				mysqli_query($con,"delete from userinventory where UserID='$UID' and ItemID = '$sellid'");
			}
			mysqli_query($con,"update users set Gold = '$newgold' where UserID='$UID'");
		}
		else if($selltype=='weapon')
		{
			$itementry=mysqli_fetch_array(mysqli_query($con,"select * from weaponsatcamp where UserID='$UID' and WeaponID = '$sellid'"));
			$owned=$itementry[2];
			$weapon=mysqli_fetch_array(mysqli_query($con,"select * from weapons where WeaponID = '$sellid'"));
			$newgold=$gold+(10*$weapon[3]+10*$weapon[4]);
			if ($owned>1)
			{
				$newamount=$owned-1;
				mysqli_query($con,"update weaponsatcamp set Amount = '$newamount' where UserID='$UID' and WeaponID = '$sellid'");
			}
			else
			{
				mysqli_query($con,"delete from weaponsatcamp where UserID='$UID' and WeaponID = '$sellid'");
			}
			mysqli_query($con,"update users set Gold = '$newgold' where UserID='$UID'");
		}
		else
		{
			$itementry=mysqli_fetch_array(mysqli_query($con,"select * from armoratcamp where UserID='$UID' and ArmorID = '$sellid'"));
			$owned=$itementry[2];
			$armor=mysqli_fetch_array(mysqli_query($con,"select * from armor where ArmorID = '$sellid'"));
			$newgold=$gold+(10*$armor[3]+10*$armor[4]);
			if ($owned>1)
			{
				$newamount=$owned-1;
				mysqli_query($con,"update armoratcamp set Amount = '$newamount' where UserID='$UID' and ArmorID = '$sellid'");
			}
			else
			{
				mysqli_query($con,"delete from armoratcamp where UserID='$UID' and ArmorID = '$sellid'");
			}
			mysqli_query($con,"update users set Gold = '$newgold' where UserID='$UID'");
		}
	}
	
	if(isset($_POST['sellall']))
		take($_POST['sid'],$_POST['scount'],$_POST['sprice']);
	function take($sid,$scount,$sprice)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
		$location=$user[7];
		$UID=$user[0];
		$gold=$user[4];
		$newgold=$gold+$scount*$sprice;
		mysqli_query($con,"update users set Gold='$newgold' where UserID='$UID'");
		mysqli_query($con,
		"delete from userinventory where UserID='$UID' and ItemID='$sid'");
	}
	
	if(isset($_POST['equip']))
	{
		equip($_POST['eqid'],$_POST['eqtype']);
	}
	function equip ($equipid,$equiptype)
	{
		include 'database_connector.php';
		$username=$_SESSION['username'];
		$user=mysqli_fetch_array(mysqli_query($con,"select * from users where username = '$username'"));
		$UID=$user[0];

		if($equiptype=='armor')
		{	//if not wearing starter rags/bare fists, add worn gear to inventory
			$lr=mysqli_fetch_array(mysqli_query($con,"select * from armor where ArmorID='$equipid'"));
			if((2*$user[3])<($lr[3]+$lr[4]))
			{
				echo "<p style='color:red;'>You are not high enough level to wear this. 
				You can only wear armor with constitution + willpower up to 2 x Level<p>";
				return;
			}
			$armorid=$user[8];
			$currarmorentry=mysqli_fetch_array(mysqli_query($con,"select * from armoratcamp where UserID='$UID' and ArmorID = '$armorid'"));
			if($armorid!=0)
			{
				if(!$currarmorentry)
					mysqli_query($con,"insert into armoratcamp values('$UID','$armorid','1')");
				else
				{
					$owned=$currarmorentry[2];
					$newowned=$owned+1;
					mysqli_query($con,"update armoratcamp set Amount='$newowned' where UserID='$UID' and ArmorID = '$armorid'");
				}
			}
			//equip selected item
			mysqli_query($con,"update users set Armor='$equipid' where UserID='$UID'");
			$eqarmorentry=mysqli_fetch_array(mysqli_query($con,"select * from armoratcamp where UserID='$UID' and ArmorID = '$equipid'"));
			$owned=$eqarmorentry[2];
			if($owned==1)
			{
				mysqli_query($con,"delete from armoratcamp where UserID='$UID' and ArmorID = '$equipid'");
			}
			else
			{
				$newamount=$owned-1;
				mysqli_query($con,"update armoratcamp set Amount = '$newamount' where UserID='$UID' and ArmorID = '$equipid'");
			}

		}
		else if ($equiptype=='weapon')
		{		
			$lr=mysqli_fetch_array(mysqli_query($con,"select * from weapons where WeaponID='$equipid'"));
			if((2*$user[3])<($lr[3]+$lr[4]))
			{
				echo "<p style='color:red;'>You are not high enough level to use this. 
				You can only use weapons with strength + intelligence up to 2 x Level<p>";
				return;
			}
			$weaponid=$user[9];
			$currweaponentry=mysqli_fetch_array(mysqli_query($con,"select * from weaponsatcamp where UserID='$UID' and WeaponID = '$weaponid'"));
			if($weaponid!=0)
			{
				if(!$currweaponentry)
					mysqli_query($con,"insert into weaponsatcamp values('$UID','$weaponid','1')");
				else
				{
					$owned=$currweaponentry[2];
					$newowned=$owned+1;
					mysqli_query($con,"update weaponsatcamp set Amount='$newowned' where UserID='$UID' and WeaponID = '$weaponid'");
				}
			}
			mysqli_query($con,"update users set Weapon='$equipid' where UserID='$UID'");
			$eqweaponentry=mysqli_fetch_array(mysqli_query($con,"select * from weaponsatcamp where UserID='$UID' and WeaponID = '$equipid'"));
			$owned=$eqweaponentry[2];
			if($owned==1)
			{
				mysqli_query($con,"delete from weaponsatcamp where UserID='$UID' and WeaponID = '$equipid'");
			}
			else
			{
				$newamount=$owned-1;
				mysqli_query($con,"update weaponsatcamp set Amount = '$newamount' where UserID='$UID' and WeaponID = '$equipid'");
			}
		}
		//if not wearing starter rags/bare fists, add worn gear to inventory
		//equip selected item
		//subtract selected item from inventory
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
	
	<div style='background-color:white;border:3px solid;width:800px;float:left; margin-left:100px;margin-top:100px;'>
	<?php
		
		$username=$_SESSION['username'];
		$userid=mysqli_fetch_array(mysqli_query($con,"select UserID from users where Username = '$username'"));
		//$location=mysqli_query($con,"select Location from users where Username = '$username'");
		$num_items=mysqli_num_rows(mysqli_query($con,"select * from userinventory where UserID = '$userid[0]'"));
		$num_armor=mysqli_num_rows(mysqli_query($con,"select * from armoratcamp where UserID = '$userid[0]'"));
		$num_weapons=mysqli_num_rows(mysqli_query($con,"select * from weaponsatcamp where UserID = '$userid[0]'"));
		$gold=mysqli_fetch_array(mysqli_query($con,"select Gold from users where UserID='$userid[0]'"));
		$gold=$gold[0];
		echo "<div style='border-bottom:2px solid;padding-bottom:15px;padding-top:15px;'>You have $gold gold pieces. </div> ";
		
		echo "<div style='border-bottom:2px solid;'>Weapons</div>";
		$weapons=mysqli_query($con,"select * from weaponsatcamp where UserID = '$userid[0]'");
		while ($w=mysqli_fetch_array($weapons))
		{//i*2
			$itemtype='weapon';
			$weaponid=$w[1];
			$item=mysqli_fetch_array(mysqli_query($con,"select * from weapons where WeaponID = '$weaponid'"));
			$item_name=$item[2];
			$item_description=$item[1];
			$item_stat1=$item[3];
			$item_stat2=$item[4];
			$item_price=10*$item[3]+10*$item[4];
			$itemID=$weaponid;
			$itemnum=$w[2];
			printout($itemtype,$item_name,$item_description,$item_price,$item_stat1,$item_stat2,$itemID,$itemnum);
		}
		
		echo "<div style='border-bottom:2px solid;border-top:1px solid;'>Armor</div>";
		$armor=mysqli_query($con,"select * from armoratcamp where UserID = '$userid[0]'");
		while ($a=mysqli_fetch_array($armor))
		{
			$itemtype='armor';
			$armorid=$a[1];
			$item=mysqli_fetch_array(mysqli_query($con,"select * from armor where ArmorID = '$armorid'"));
			$item_name=$item[2];
			$item_description=$item[1];
			$item_stat1=$item[3];
			$item_stat2=$item[4];
			$item_price=10*$item[3]+10*$item[4];
			$itemID=$armorid;
			$itemnum=$a[2];
			printout($itemtype,$item_name,$item_description,$item_price,$item_stat1,$item_stat2,$itemID,$itemnum);

		}

		echo "<div style='border-bottom:2px solid;border-top:1px solid;'>Miscellaneous Items</div>";
		$items=mysqli_query($con,"select * from userinventory where UserID = '$userid[0]'");
		while ($i=mysqli_fetch_array($items))
		{
			$itemtype='item';
			//get item result;
			$itemid=$i[1];
			$item=mysqli_fetch_array(mysqli_query($con,"select * from items where ItemID = '$itemid'"));
			$item_name=$item[1];
			$item_description=$item[2];
			$item_price=ceil($item[4]/10);
			$item_stat1=0;
			$item_stat2=0;
			$itemID=$item[0];
			$itemnum=$i[2];
			printout($itemtype,$item_name,$item_description,$item_price,$item_stat1,$item_stat2,$itemID,$itemnum);
		}
		
		function printout($itemtype,$item_name,$item_description,$item_price,$item_stat1,$item_stat2,$itemID,$itemnum)
		{
			include 'database_connector.php';
			$username=$_SESSION['username'];
			$user=mysqli_fetch_array(mysqli_query($con,"select * from users where Username = '$username'"));
			$location=$user[7];
			echo 
			"<div style='border-bottom:1px solid;'> 
				
				<form style='position:relative;' method='post' action='inventory.php'>
					<div style='float:left;width:575px;' align='left'>$itemnum x <b>$item_name</b> worth $item_price gold each</div>
				
					<div >";
					
					if($itemtype!='item')
						echo	
						"<input type='text' name='eqid' value='$itemID' style='display:none'>
						<input type='text' name='eqtype' value='$itemtype' style='display:none'>
						<input type='submit' name='equip' value='Equip'>";
					else 
						if($location==0)
							echo	
							"<input type='text' name='sid' value='$itemID' style='display:none'>
							<input type='text' name='scount' value='$itemnum' style='display:none'>
							<input type='text' name='sprice' value='$item_price' style='display:none'>
							<input type='submit' name='sellall' value='Sell All'>";
					
					if($location==0)
						echo	
						"<input type='text' name='sid' value='$itemID' style='display:none'>
						<input type='text' name='stype' value='$itemtype' style='display:none'>
						<input type='submit' name='sell' value='Sell'>";
					
					echo
					"<input type='text' name='exid' value='$itemID' style='display:none'>
					<input type='text' name='exname' value='$item_name' style='display:none'>
					<input type='text' name='exdesc' value=\"$item_description\" style='display:none'>
					<input type='text' name='exprice' value='$item_price' style='display:none'>
					<input type='text' name='extype' value='$itemtype' style='display:none'>
					<input type='text' name='exstat1' value='$item_stat1' style='display:none'>
					<input type='text' name='exstat2' value='$item_stat2' style='display:none'>
					<input type='submit' name='examine' value='Examine'>
					</div>
				</form>
			</div>";
		}
	?>
	</div>
</body>
</html>
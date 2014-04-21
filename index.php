<?php
session_start();
include 'database_connector.php';

if(isset($_POST['submit']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];
	//echo $username.$password;
	$result=mysqli_query($con,"select *from users where Username='$username' and Password='$password'");
	if($result)
	{
			$count=mysqli_num_rows($result);	
			//echo $count;
	}
	if($count==1)
	{
		$_SESSION['username']=$username;
		$_SESSION['back']=0;
		$_SESSION['password']=$password;
		header("location:location.php");
		//mysqli_query($con,"UPDATE users set Location = 0 where Username = '$username' ");
	}
	else
	{	

		echo "<p style=\"color:red;\"></br>Invalid login credentials</br></p>";
	}
}

if(isset($_POST['submit2']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];
	//echo $username.$password;
	$result=mysqli_query($con,"select * from users where Username='$username'");
	if($result)
	{
			$count=mysqli_num_rows($result);	
			//echo $count;
 	}
	if($count==0)
	{
		mysqli_query($con,"INSERT INTO users (Username,Password,Level,Gold,Health,Experience,Location) 
		VALUES ('$username','$password',1,0,10,0,0)");
		$UID=mysqli_fetch_array(mysqli_query($con,"select UserID from users where username='$username'"));
		$UID=$UID[0];
		mysqli_query($con,"insert into userknowsmove values($UID, '1')");
		mysqli_query($con,"insert into userknowsmove values($UID, '2')");
		mysqli_query($con,"insert into userknowsmove values($UID, '3')");
		mysqli_query($con,"insert into movesequipped values($UID, '1','2','3')");
		echo "<p style=\"color:red;\"></br> Account Created. Please make note of your account credentials.</br></p>";
	}
	else
	{	
		//header("location:index.php");
		echo "<p style=\"color:red;\"></br>This username is already in use. Please try another</br></p>";
	}
}
?>



<!doctype html>
<html>
<head>
<meta charset="utf-8">
	<title>Welcome to Dungeon Designer!</title>
	<link rel="stylesheet" type="text/css" href="site.css">

</head>

<body>

<h1 style="margin:0;">
	Dungeon Designer
</h1>
<h5 style="margin-bottom:50px;">
	Create, Explore, Conquer
</h5>

<form name="form1" method="post" action="index.php">
<table width="300" border="1px solid" align="center" cellpadding="0" cellspacing="1" bgcolor="#330066">
<tr>
  		
  	<td>
		<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#999999">
  			<tr>
  				<td colspan="3"><div align="center"><strong>Player Login </strong></div></td>
  			</tr>
  			<tr>
                  <td width="78">Username</td>
                  <td width="6">:</td>
                  <td width="294"><input name="username" type="text" required="required" id="username"></td>
            </tr>
            <tr>
                  <td>Password</td>
                  <td>:</td>
                  <td><input name="password" type="password" required="required" id="password"></td>
            </tr>
            <tr>
                  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				  <td>
					<input type="submit" name="submit" value="Login">
					<input type="submit" name="submit2" value="Register">
				  </td>
            </tr>
		</table>
	</td>
  		
 </tr>
</table>
</form>
</body>
</html>
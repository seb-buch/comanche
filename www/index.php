<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">

<head>
  <link rel="icon" href="images/favicon.ico" type="image/ico"> 
  <title>Comanche | Home</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="description" content="Automated Membrane Builder and Repository">
  <meta name="keywords" content="Comanche, Molecular Dynamics">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="style.php">
  <!-- This page is an introduction to Comanche and the gateway to the 
       web forms for computation -->
</head>

<body>

<?php
session_start();
session_set_cookie_params(0);
?>

<div id="container">
<div id="top">
  <img src="images/vmdscenegimp.jpg" alt="Membrane" /> 
  <h1>Comanche</h1>
  <h2>Membrane Builder and Repository</h2>
</div>

<div id="login">
<?php
if (isset($_POST['Logout'])){
  session_destroy();
  session_start();
  session_set_cookie_params(0);
  header('Location: index.php'); 
  }
if (!($_SESSION['username'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  if ($username&&$password) {
    $connect = mysql_connect ("localhost","root","comanche") or die ("Couldn't connect");
    mysql_select_db("comanche_db") or die ("Couldn't find db");
    $query = mysql_query("SELECT * FROM register WHERE username='$username'");
    $numrows = mysql_num_rows ($query);
    if ($numrows!=0) {
      while ($row = mysql_fetch_assoc($query)) {
        $dbusername = $row['username'];
        $dbpassword = $row['password'];
        $activate = $row['activate'];
        $_SESSION['email']  = $row['email']; 
        if ($activate=='0') {
          echo "<br />Your account is not activated yet.<br />Check your email."; 
          }
        }
      if ($username==$dbusername&&md5($password)==$dbpassword&&$activate=='1') {
        $_SESSION['username']=$username;
        }
      elseif ($username==$dbusername&&md5($password)!=$dbpassword&&$activate=='1') { 
        echo "<br />Incorect password<br />"; 
        }
      }
    else echo "<br />That user doesn't exist!<br />";  
    }
  else echo "<br />Please enter a user name and password!<br />"; 
  }
if (!($_SESSION['username'])) {
  echo "<br />
<form action='index.php' method='POST'>
  <table>
    <tr>
      <td>Username :</td>
      <td><input type='text' name='username'><br></td>
    </tr>
    <tr>
      <td>Password :</td>
      <td><input type='password' name='password'><br></td>
    </tr>
    <tr>
      <td><input type='submit' value='Log in'></td>
      <td>Create your <a href='index.php?page=register'>account</a></td>
    </tr>
  </table>
</form>";
  }
else {
  $username=$_SESSION['username'];
  echo "<br /><br />
<form method='POST'>
  Welcome, ".$username."!<br /><br />
  <input type='submit' name='Logout' value='Log out'> &nbsp;&nbsp;<a href='index.php?page=userinfo'>Change your users info</a><br />
</form>";
  }
?>
</div>

<div id="menu">
<nav>
<ul>
<li><a href="index.php">Home</a></li>
<li><a href="#">Membrane builder</a>
  <ul>
    <li><a href="index.php?page=equilibrarion">Equilibration</a> 
    <li><a href="index.php?page=insertion">Insertion</a> 
    <li><a href="index.php?page=umbrella">Umbrella</a> 
  </ul>
</li>
<li><a href="#">Libraries</a>
  <ul>
    <li><a href="index.php?page=lipidsdb">Lipids</a> 
    <li><a href="index.php?page=membranedb">Membranes</a> 
    <li><a href="index.php?page=forcefieldsdb">Forcefields</a> 
  </ul>
</li>
<li><a href="index.php?page=jobs">Jobs</a></li>
</ul>
</nav>
</div>

<div id="page">
<?php 
if ((isset($_GET['page'])) && (file_exists("{$_GET['page']}.php"))) {
  include("{$_GET['page']}.php");
  } 
else {
  echo "
<p> Text </p>
<iframe src='res.zmat'></frame>";
  }
?>

</div>
</div> 
</body>
</html>


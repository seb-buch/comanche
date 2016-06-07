<?php
//when activation email is sent, a link to this page will acctivate the user account
$connect = mysql_connect ("localhost","root","comanche") or die ("Couldn't connect!");
mysql_select_db ("comanche_db") or die ("couldn't find db");

$id = $_GET['id'];
$code = $_GET['code'];

// check that activation and ID codes in db matches email activation and ID codes  
if ($id&&$code)
  {
  $check = mysql_query("SELECT * FROM register WHERE id='$id'AND random='$code'");
  $checknum = mysql_num_rows($check);
  if ($checknum==1)
    {
    //if match is OK then : run a query to activate the account
    $acti = mysql_query("UPDATE register SET activate='1' WHERE id='$id'");  
    echo "Your account is activated. You may now login.";  
    }
  else
    echo "Invalid ID or Activation code.";
  }
else
  echo "Data missing!"; 
?>


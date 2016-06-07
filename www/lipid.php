<?php
if (!(isset($_SESSION['username']))) { 
  die("You must be logged in!"); 
  }
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $connect = mysql_connect("localhost","root","comanche");
  if (!$connect) {
    die('Could not connect: ' . mysql_error());
    }
  mysql_select_db("comanche_db", $connect);
  $sql = "SELECT * FROM lipids WHERE `lipids`.`lmid` = '$id'";
  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  if ($numrows == 1) {
    while ($rows = mysql_fetch_assoc($result)) {
      $commonname = $rows['commonname']; 
      $systematicname = $rows['systematicname'];
      echo "<br /><strong>Common name :</strong> $commonname<br />";
      echo "<br /><strong>Systematic name :</strong> $systematicname<br />";
      $lmid = $rows['lmid'];
      $lmc = substr($lmid,0,4); 
      $imgsrc = "MD_Lipids/LipidMaps/LMSD/$lmc/$lmid/$lmid.jpg";
      echo "<br /><img src=$imgsrc alt='Membrane' width='600' /><br />";
      }
    $sql = "SELECT * FROM forcefield LEFT JOIN lipids ON `forcefield`.`lmid` = `lipids`.`lmid` WHERE `forcefield`.`lmid` = '$id'";
    $result = mysql_query($sql);
    while ($rows = mysql_fetch_assoc($result)) {
      $forcefield = $rows['forcefield']; 
      $name = $rows['name']; 
      echo "<br /><strong>Forcefield :</strong> $forcefield - <strong>Name :</strong> $name<br />";
      }
    }
  else {
    die('Invalid query');
    }
  }
else {
  die("You have no selected a lipid!"); 
  }
echo "<br />";
?> 


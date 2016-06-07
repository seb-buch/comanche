<?php
if (!(isset($_SESSION['username']))) { 
  die("You must be logged in!"); 
  }
$connect = mysql_connect("localhost","root","comanche");
if (!$connect) {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("comanche_db", $connect);
$sql = "SELECT * FROM `lipids` ORDER BY `lipids`.`id` DESC LIMIT 0, 100";
$result = mysql_query($sql);
if (!$result) {
  die('Invalid query: ' . mysql_error());
  }
$forcefield = $_POST['forcefield']; 
if(isset($_POST['submit'])) {
  $sql = "SELECT * FROM lipids LEFT JOIN forcefield ON lipids.lmid = forcefield.lmid WHERE forcefield.forcefield='$forcefield' ORDER BY `lipids`.`id` DESC LIMIT 0, 100";
  $result = mysql_query($sql);
  }
?> 

<!-- <form action='index.php?page=lipidsdb' method='POST'> -->
<form method='POST'>
  <table>
    <tr>
      <td>Forcefield :</td>
      <td>
        <select name="forcefield">
          <option value="none"selected>--</option>
          <option value="Martini">Martini</option>        
          <option value="Charmm36">Charmm36</option>        
        </select>
      </td>
    </tr>
    <tr>
      <td><input type='submit' name='submit' value='Select'></td>
    </tr>
  </table>
</form>

<table class=center border="1" cellspacing="2" cellpadding="0">
<tr>
  <td align="center" bgcolor="#65a9d7"><strong>Id</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Common name</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Systematic name</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>formula</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Lipid Maps ID</strong></td>
</tr>
<?php while ($rows = mysql_fetch_assoc($result)): ?>
<tr>
  <td><strong><a href='index.php?page=lipid&id=<?php echo $rows['lmid']; ?>'><?php echo $rows['id']; ?></a></td>
  <td><strong><?php echo $rows['commonname']; ?></td>
  <td><strong><?php echo $rows['systematicname']; ?></td>
  <td><strong><?php echo $rows['formula']; ?></td>
  <td><strong><a href='<?php echo $rows['lipidmapsurl']; ?>'><?php echo $rows['lmid']; ?></a></td>
</tr>
<?php endwhile; ?>
            

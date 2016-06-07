<?php

if (!(isset($_SESSION['username']))) { 
  die("You must be logged in!"); 
  }

$connect = mysql_connect("localhost","root","comanche");
if (!$connect) {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("comanche_db", $connect);
$sql = "SELECT * FROM `jobs` ORDER BY `jobs`.`id` DESC LIMIT 0, 100";
$result = mysql_query($sql);
if (!$result) {
  die('Invalid query: ' . mysql_error());
  }
?> 

<table class=center border="1" cellspacing="2" cellpadding="0">
<tr>
  <td align="center" bgcolor="#65a9d7"><strong>Id</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Username</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Job Name</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Date</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Time</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Status</strong></td>
  <td align="center" bgcolor="#65a9d7"><strong>Node</strong></td>
</tr>
<?php while ($rows = mysql_fetch_assoc($result)): ?>
<tr>
  <td><strong><?php echo $rows['id']; ?></td>
  <td><strong><?php echo htmlspecialchars($rows['jobname']); ?></td>
  <td><strong><?php echo htmlspecialchars($rows['username']); ?></td>
  <td><strong><?php echo htmlspecialchars($rows['date']); ?></td>
  <td><strong><?php echo htmlspecialchars($rows['time']); ?></td>
  <td><strong><?php echo htmlspecialchars($rows['status']); ?></td>
  <td><strong><?php echo htmlspecialchars($rows['node']); ?></td>
</tr>
<?php endwhile; ?>
            

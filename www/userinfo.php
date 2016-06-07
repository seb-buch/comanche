<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
$securimage = new Securimage();

echo"<h1>User information</h1>";
$connect = mysql_connect("localhost","root","comanche");
mysql_select_db("comanche_db"); 
$rows = mysql_fetch_assoc(mysql_query("SELECT * FROM register WHERE username='$username'")); 
$id = $rows['ID'];
$fullname = $rows['fullname'];
$email = $rows['email']; 
$random = $rows['random']; 
$date = $rows['date']; 
?>

<form action='index.php?page=userinfo' method='POST'>
  <table>
    <tr><td>Your full name:</td><td><input type='text' name='fullname' value='<?php echo $fullname ?>'></td></tr>
    <tr><td>Choose a username:</td><td><input type='text' name='username' value='<?php echo $username ?>'></td></tr>
    <tr><td>Choose a password:</td><td><input type='password' name='password'></td></tr>
    <tr><td>Repeat your password:</td><td><input type='password' name='repeatpassword'></td></tr> 
    <tr><td>Enter your email:</td><td><input type='email' name='email' value='<?php echo $email ?>'></td></tr>
  </table>
  <br /><input type='submit' name='submit' value='Change'>
</form>

<?php
$submit   = $_POST['submit'];
$fullname = strip_tags($_POST['fullname']);
$username = strtolower(strip_tags($_POST['username']));
$password = strip_tags($_POST['password']);
$repeatpassword = strip_tags($_POST['repeatpassword']);
$email    = strip_tags($_POST['email']);

// "$fqdn" must Change this to 10.1.1.138 for INTRANET vs Internet
$fqdn     = "peplook.gembloux.ulg.ac.be"; #$fqdn="10.1.1.138";
if ($submit) {
  $connect = mysql_connect("localhost","root","comanche");
  mysql_select_db("comanche_db"); 
  if ($fullname&&$username&&$password&&$repeatpassword&&$email) {
    if ($password==$repeatpassword) {
      if (strlen($username)>25||strlen($fullname)>25) {
        echo "<br /><br />Length of username or fullname is too long!";    
        }
      else {
        if (strlen($password)>25||strlen($password)<6) {
          echo "<br /><br />Password must be between 6 and 25 characters";    
          }
        else {
          $password = md5($password);
          $repeatpassword = md5($repeatpassword);
          $acti = mysql_query("UPDATE register SET fullname='$fullname' WHERE ID='$id'");
          $acti = mysql_query("UPDATE register SET username='$username' WHERE ID='$id'");
          $acti = mysql_query("UPDATE register SET password='$password' WHERE ID='$id'");
          $acti = mysql_query("UPDATE register SET email='$email' WHERE ID='$id'");
          $_SESSION['fullname']  = $fullname;
          $_SESSION['username']  = $username;
          $_SESSION['email']  = $email;
          header('Location: index.php?page=userinfo');
          }
        }
      }
    else {
      echo "<br /><br />Your passwords do not match!";
      }
    }
  else {
    echo "<br /><br />Please fill in <b>all</b> fields!"; 
    }
  }
?>


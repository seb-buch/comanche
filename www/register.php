<?php
session_set_cookie_params(0);
include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
$securimage = new Securimage();
session_destroy();
?>

<h1>Registration</h1>
<form action='index.php?page=register' method='POST'>
  <table>
    <tr><td>Your full name:</td><td><input type='text' name='fullname' value='<?php echo $fullname ?>'></td></tr>
    <tr><td>Choose a username:</td><td><input type='text' name='user' autocomplete="off" value='<?php echo $user ?>'></td></tr>
    <tr><td>Choose a password:</td><td><input type='password' name='pass' autocomplete="off"></td></tr>
    <tr><td>Repeat your password:</td><td><input type='password' name='repeatpassword'></td></tr> 
    <tr><td>Enter your email:</td><td><input type='email' name='email' value='<?php echo $email ?>'></td></tr>
    <tr>
      <td><br><a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">ReCaptcha</a></td>
      <td><img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" /></td>
    </tr>
    <tr><td>Enter Captcha:</td><td><input type="text" name="captcha_code" size="20" maxlength="6" /></td></tr>
  </table>
  <br /><input type='submit' name='submit' value='Register'>
</form>

<?php
$submit   = $_POST['submit'];
$fullname = strip_tags($_POST['fullname']);
$user = strtolower(strip_tags($_POST['user']));
$password = strip_tags($_POST['pass']);
$repeatpassword = strip_tags($_POST['repeatpassword']);
$email    = strip_tags($_POST['email']);

// "$fqdn" must Change this to 10.1.1.138 for INTRANET vs Internet
$fqdn     = "peplook.gembloux.ulg.ac.be"; #$fqdn="10.1.1.138";
if ($submit) {
  $connect = mysql_connect("localhost","root","comanche");
  mysql_select_db("comanche_db"); 
  if ($fullname&&$user&&$password&&$repeatpassword&&$email) {
    $namecheck = mysql_query("SELECT username FROM register WHERE username='$user'");
    $count = mysql_num_rows($namecheck);
    if ($count!=0) {
      echo "<br /><br />This username is already taken.";
      }
    else {
      if ($password==$repeatpassword) {
        if (strlen($user)>25||strlen($fullname)>25) {
          echo "<br /><br />Length of username or fullname is too long!";    
          }
        else {
          if (strlen($password)>25||strlen($password)<6) {
            echo "<br /><br />Password must be between 6 and 25 characters";    
            }
          else {
            $password = md5($password);
            $repeatpassword = md5($repeatpassword);
            if ($securimage->check($_POST['captcha_code']) == false) {
              echo "<br /><br />Incorrect captcha! Try again."; // recharger un chaptka 
              }
            else {
              $date     = date("Y-m-d");
              $random = rand(23456789,98765432); //generate random for acctivation email / see activate.php 
              $queryreg = mysql_query("INSERT INTO register VALUES ('' , '$fullname', '$user', '$password', '$email', '$date', '$random', '0')");
              $lastid = mysql_insert_id();
              $to = $email;
              $subject = "Activate your account!";
              $headers = array('From: peplook <peplook@gembloux.ulg.ac.be>', 'Content-Type: text/html');
              $server = "smtp.fsagx.ac.be";
              ini_set("SMTP", $server);
              $body = "Hello $fullname,<br><br>You need to activate your account with the link below:<br><br>
                <a href ='http://$fqdn/activate.php?id=$lastid&code=$random'>Click here to activate your account</a><br><br>
                Thanks! <br><br>If the link doesn't work copy and paste this link in your browser's address bar:<br><br>
                http://$fqdn/activate.php?id=$lastid&code=$random <br><br>support: jmcrowet@ulg.ac.be";
              // mail($to, $subject, $body, $headers);
              mail($to, $subject, $body , implode("\r\n", $headers));
              echo "<br /><br />Check your email $email and activate your account.";
              echo "<br /><br />localhost/index.php?page=activate&id=$lastid&code=$random";
              }
            }
          }
        }
      else {
        echo "<br /><br />Your passwords do not match!";
        }
      }
    }
  else {
    echo "<br /><br />Please fill in <b>all</b> fields!"; 
    }
  } 
?>


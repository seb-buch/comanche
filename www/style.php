<?php
//styles in all pages containing : <link rel="stylesheet" type="text/css"
header("Content-type: text/css");
$white    = '#fff'   ; 
$ltgrey   = '#C0C0C0'; 
$awhite   = '#8B8378'; //Antique white 4
$dkgrey   = '#333'   ; 
$dkgreen  = '#008400'; 
$crimson  = '#DC143C'; 
$dgold    = '#8B6508'; //Dark golden rod 4
$black    = '#000000'; 
$seafoam2 = '#a0d6b4';
$C1       = '#997777'; 
$C2       = '#779977'; 
$C3       = '#777799'; 
$C4       = '#995555'; 
$C5       = '#559955'; 
?>

html {
  width: 1200px;
  margin: 0 auto;
}

body {
  background:<?=$white?>;
  color:<?=$dkgrey?>;
}

h1, h2 {
  color:<?=$dkgreen?>;
}

h3 {
  color:<?=$dgold?>;
}

h4 {
  color:<?=$crimson?>;
  font-size: 90%;
}

h5 {
  color:<?=$crimson?>;
}

blockquote {
  color:<?=$dkgreen?>;
}

#container {
  overflow: auto;
  width: 1200px;
  min-height: 600px;
  position: static;
  background: <?=$seafoam2?>;
}

#top {
  background: <?=$white?>;
  color:<?=$dkgreen?>;
  width: 930px;
  height: 155px;
  float: left;
  display: block;
  position: static;
/*  background-image: url(images/vmdscene2.png);
  background-size: 100%;
  background-repeat: no-repeat;*/
}

#top img {
  float: right;
  height: 100%;
}

#login {
  background: <?=$white?>;
  width: 270px;
  height: 155px;
  float: right;
  display: block;
  position: static;
}

#menu {
  background: <?=$seafoam2?>;
  width : 1150px;
  float: left;
  display: block;
  position: relative;
  left: 30px;
}

nav ul {
  background: #efefef; 
  background: linear-gradient(top, #efefef 0%, #bbbbbb 100%);  
  background: -moz-linear-gradient(top, #efefef 0%, #bbbbbb 100%); 
  background: -webkit-linear-gradient(top, #efefef 0%,#bbbbbb 100%); 
  box-shadow: 0px 0px 9px rgba(0,0,0,0.15);
  padding: 0 20px;
  border-radius: 10px;  
  list-style: none;
  position: relative;
  display: inline-table;
  width: 1100px;
}

nav ul:after {
  content: ""; 
  clear: both; 
  display: block;
}

nav ul li {
  float: left;
}

nav ul li a {
  display: block; 
  padding: 25px 40px;
  color: #757575; 
  text-decoration: none;
}

nav ul li:hover {
  background: #4b545f;
  background: linear-gradient(top, #4f5964 0%, #5f6975 40%);
  background: -moz-linear-gradient(top, #4f5964 0%, #5f6975 40%);
  background: -webkit-linear-gradient(top, #4f5964 0%,#5f6975 40%);
}

nav ul li:hover a {
  color: #fff;
}

nav ul li:hover > ul {
  display: block;
}

nav ul ul {
  display: none;
  background: #5f6975; 
  border-radius: 0px; 
  padding: 0;
  position: absolute; 
  top: 100%;
  width: auto;
  z-index: 1;
}

nav ul ul li {
  float: none; 
  border-top: 1px solid #6b727c;
  border-bottom: 1px solid #575f6a;
  position: relative;
}

nav ul ul li a {
  padding: 15px 40px;
  color: #fff;
}	

nav ul ul li a:hover {
  background: #4b545f;
}

nav ul ul ul {
  position: absolute; 
  left: 100%; 
  top:0;
}

#page {
  background: <?=$seafoam2?>;
  width: 1130px;
  display: block;
  position: relative;
  float: left;
  left: 30px;
}

.center {
  margin-left: auto;
  margin-right: auto;
}

.fileUpload {
  position: relative;
  overflow: hidden;
  margin: 0px;
  padding : 0px 10px;
  background:#eeeeee;
  border: 1px groove;
}

.fileUpload input.upload {
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
  padding: 0;
  cursor: pointer;
  opacity: 0;
  filter: alpha(opacity=0);
}

.textarea {
  resize: none; /* vertical;*/
}

/*

height: 100vh;

.box {
  border: 5px solid red;
}

*/

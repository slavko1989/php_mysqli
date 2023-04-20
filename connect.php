<?php
define("DB_SERVER","localhost");
define("DB_ROOT","root");
define("DB_NAME","admin");
define("DB_PASS","");

$conn = mysqli_connect(DB_SERVER,DB_ROOT,DB_PASS,DB_NAME);
if(!$conn){
    die("failed: " .mysqli_connect_error());
}else{
   // echo "connection is ok";
}
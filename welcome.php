<?php
//session_start();
include_once("html/head.php");
include_once("function.php");
//logout();
if(isset($_SESSION["id"])){
    if(isset($_SESSION["username"])) {
        echo "hey " . $_SESSION["username"] . ' your account is:<br>';
        echo "<a href='logout.php'>Logout</a><br>";
   }
}else{
    echo "please login";
}

select();

include_once("html/footer.php");


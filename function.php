<?php
//session_start();
include_once("connect.php");
function security($security){
    global $conn;
    $security = trim($security);
    $security = stripslashes($security);
    $security = htmlspecialchars($security);
    $security = mysqli_real_escape_string($conn,$security);
    return $security;
}

function register(){
    global $conn;
    $errUsername="";
    $errEmail="";
    $errPass="";
    $errImg="";
    $errName="";
    $valid = true;

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $uname= security($_POST["username"]);
        $pass= security($_POST["password"]);
        $name=security($_POST["name"]);
        $email=security($_POST["email"]);
        $img = security($_FILES["image"]["name"]);
        //$hash = sha1($pass);

        if(empty($uname)){
            $errUsername="Your username is empty";
            $valid = false;
        }
        if(empty($pass)){
            $errPass="Your password is empty";
            $valid = false;
        }
        if(empty($name)){
            $errName="Your name is empty";
            $valid = false;
        }
        if(empty($email)){
            $errEmail="Your email is empty";
            $valid = false;
        }
        if(empty($img)){
            $errImg="Your images is empty";
            $valid = false;
        }
        if(username()===true){
            $errUsername="username exists";
        }
        elseif($valid){
            $query = "INSERT into examp (username,password,name,email,image)
VALUES ('$uname', '$pass', '$name', '$email','$img')";
            $insert = mysqli_query($conn,$query);
            if($insert) {
                $target = "img/";
                $target_file = $target . basename($img);
                $tmp = $_FILES["image"]["tmp_name"];
                $size = $_FILES["image"]["size"];
                $type = pathinfo($target_file,PATHINFO_EXTENSION);


                if (!$target_file) {
                    $errImg = "img ist not images";
                    false;
                }
                if ($size > 500000) {
                    $errImg = "img ist to big";
                    false;
                }
                if ($type!="jpg" && $type!=="png" && $type!=="gif" && $type!=="jpeg") {
                    $errImg = "img ist to big";
                    false;
                }else{
                    move_uploaded_file($tmp,$target_file);
                    echo "you are register";
                }

            }
        }
    }

?>
<div class="form">
    <h1>Registration</h1>
    <h1>if you are register click to <a href="login.php">LOGIN</a></h1>

    <form name="registration" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username"><br><?php echo $errUsername; ?>
        <input type="password" name="password" placeholder="Password"><br><?php echo $errPass; ?>
        <input type="text" name="name" placeholder="Name"><br><?php echo $errName; ?>
        <input type="text" name="email" placeholder="Email"><br><?php echo $errEmail; ?>
        <input type="file" name="image"<br><?php echo $errImg; ?>
        <input type="submit" name="submit" value="Register" />
</div>

<?php } ?>
<?php
function login(){
    global $conn;
    $errU="";
    $errP="";

    if($_SERVER["REQUEST_METHOD"]=="POST") {
        $username = security($_POST["username"]);
        $password = security($_POST["password"]);
        //$id = security($_POST["id"]);
        //$hash = md5($password);

        if (empty($username)) {
            $errU = "Your username is empty";
            false;
        }
        if (empty($password)) {
            $errP = "Your password is empty";
            false;
        }

        if (true) {
            $login = "select * from examp where username='$username' and password='$password'";
            $log = mysqli_query($conn, $login);
            $rows = mysqli_num_rows($log);
            $row = mysqli_fetch_assoc($log);
            if($rows==1){
                $username = $row["username"];
                $id = $row["id"];
                $_SESSION["username"]= $username;
                $_SESSION["id"]= $id;
                redirect("welcome.php");
                true;
            }else{
                echo "password or username is wrong";
                false;
            }
        }
    }

    ?>
    <div class="form">
        <h1>Login</h1>
        <h1><a href="index.php">register</a></h1>
        <h1><a href="forgot.php">forgot password</a></h1>
        <form name="registration" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Username"><br><?php echo $errU; ?>
            <input type="password" name="password" placeholder="Password"><br><?php echo $errP; ?>
            <input type="submit" name="login" value="Login" />
        </form>
    </div>
<?php } ?>
<?php
function logout(){
    session_start();
    session_destroy();
    echo '<a href="login.php">logout </a><br>';

}
function redirect($url){
    header("location:$url");
}
function select(){
    global $conn;
    if(isset($_SESSION["id"])) {
        $id = $_SESSION["id"];
        $query = "select examp.id, examp.username, examp.email, examp.name, examp.password,examp.image from examp where id='$id'";
        $select = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($select)) {
            echo $row["username"] . "<br>";
            echo $row["email"] . "<br>";
            echo $row["name"] . "<br>";
            echo $row["password"] . "<br>";
            ?><img src="img/<?php echo $row["image"]; ?>"><br>
            <a href="delete.php?delId=<?php echo $row['id']; ?>">delete account</a>
            <a href="edit.php?edId=<?php echo $row['id']; ?>">edit account</a>
            <a href="changePass.php">change password</a>
            <?php
        }
    }
}
function delete(){
    global $conn;
        if (isset($_GET["delId"])) {
            $id = $_GET["delId"];
            mysqli_query($conn,"delete from examp where id='$id'");
                echo "your account deleted ";
                echo "<a href='index.php'>register again</a>";
            }

}


function changePass()
{
    global $conn;
    if(isset($_SESSION["id"])){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pass = security($_POST["password"]);
        $new_pass = security($_POST["newPassword"]);
        $confirm_pass = security($_POST["confirmPassword"]);

        $result = mysqli_query($conn, "SELECT *from examp WHERE id='" . $_SESSION["id"] . "'");
        $row = mysqli_fetch_array($result);
        if ($pass == $row["password"]) {
            mysqli_query($conn,"UPDATE examp set password= '$new_pass' WHERE id='" . $_SESSION["id"] . "'");
            session_destroy();
            redirect("index.php");
        } else {
            echo "Current Password is not correct";
        }
    }
    }
}
function username(){
    global $conn;
    $uname= security(@$_POST["username"]);
    $result = mysqli_query($conn, "SELECT * from examp WHERE username='$uname'");
    $row = mysqli_num_rows($result);
    if($row==1) {
        return true;
    } else{
        return false;
    }
}

function edit()
{
    global $conn;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = security($_POST["id"]);
        $uname = security($_POST["username"]);
        $pass = security($_POST["password"]);
        $name = security($_POST["name"]);
        $email = security($_POST["email"]);
        $img = security($_FILES["image"]["name"]);

            $query = "update  examp set username='$uname',password='$pass',name='$name',email='$email',image='$img'
where id='$id'";
            $edit = mysqli_query($conn, $query);
            if ($edit) {
                $target = "img/";
                $target_file = $target . basename($img);
                $tmp = $_FILES["image"]["tmp_name"];
                //$size = $_FILES["image"]["size"];
                //$type = pathinfo($target_file, PATHINFO_EXTENSION);
                    move_uploaded_file($tmp, $target_file);
                    redirect("welcome.php");
                    true;
            }
        }
    }
    if(isset($_GET["edId"])){
        $id = $_GET["edId"];
        $ed = "select * from examp where id='$id'";
        $editId = mysqli_query($conn,$ed);
        while($edit = mysqli_fetch_assoc($editId)){
        ?>
        <div class="form">
            <h1>EDIT ACCOUNT</h1>
            <form name="registration" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit["id"];?>"><br>
                <input type="text" name="username" value="<?php echo $edit["username"];?>" placeholder="Username"><br>
                <input type="password" name="password" value="<?php echo $edit["password"];?>" placeholder="Password"><br>
                <input type="text" name="name" value="<?php echo $edit["name"];?>" placeholder="Name"><br>
                <input type="text" name="email" value="<?php echo $edit["email"];?>" placeholder="Email"><br>
                <input type="file" name="image" value="<?php echo $edit["image"];?>"><br>
                <input type="submit" name="submit" value="Edit" />
        </div>

    <?php } } ?>
<?php
function forgot_password()
{
    global $conn;
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $email = security($_POST['email']);
        $sql = "SELECT * FROM examp WHERE email = '$email'";
        $res = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($res);
        if ($count == 1) {
            echo "Send email to user with password";
        } else {
            echo "User email does not exist in database";
        }

        $r = mysqli_fetch_assoc($res);
        $password = $r['password'];
        $to = $r['email'];
        $subject = "Your Recovered Password";

        $message = "Please use this password to login " . $password;
        $headers = "From : zdravkovic.slavisa89@gmail.com";
        if (mail($to, $subject, $message, $headers)) {
            echo "Your Password has been sent to your email id";
        } else {
            echo "Failed to Recover your password, try again";
        }
    }

?>
<div class="form">
    <h1>FORGOT PASSWORD</h1>
    <form name="registration" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <input type="text" name="email"  placeholder="Email"><br>
        <input type="submit" name="submit" value="forgot" />
</div>
<?php } ?>


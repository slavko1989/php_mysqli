<?php
include_once("html/head.php");

include_once("function.php");
changePass();
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="password" name="password" placeholder="old password"><br>
    <input type="password" name="newPassword" placeholder="new password"><br>
    <input type="submit" name="submit">
</form>
<?php include_once("html/footer.php"); ?>

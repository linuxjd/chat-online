<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

if (login_check($mysqli) == true) {
    $logged = 'in';
    echo "<script>";
    echo "window.location.href='chat/index.php'";
    echo "</script>";
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>

<head>

  <meta charset="UTF-8">

  <title>Log-in - CodePen</title>

    <link rel='stylesheet' href='css/jquery-ui.css'>
    <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    <script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/JavaScript" src="js/forms.js"></script> 
</head>

<body>
<?php
if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}
?> 
  <div class="login-card">
    <img class="profile-img" src="img/photo.jpg" alt="？">
    <form action="includes/process_login.php" method="post" name="login_form">                      
        <input type="text" name="email" placeholder="请输入您的邮箱">
        <input type="password" id="password" name="password" placeholder="请输入密码">
        <input type="button" name="login" class="login login-submit" value="登录" onclick="formhash(this.form, this.form.password);">
  </form>

  <div class="login-help">
    <a href="register.php">注册</a> • <a href="#">找回密码</a>
  </div>
</div>
</body>
</html>

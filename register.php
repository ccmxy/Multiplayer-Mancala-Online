<?php
ob_start();
session_start();
if( isset($_SESSION['user'])!="" ){
 header("Location: home.php");
}
include_once 'dbconnect.php';

if(isset($_POST['btn-signup'])) {

 $uname = trim($_POST['uname']);
 $email = trim($_POST['email']);
 $upass = trim($_POST['pass']);

 $uname = strip_tags($uname);
 $email = strip_tags($email);
 $upass = strip_tags($upass);

 // password encrypt using SHA256();
 $password = hash('sha256', $upass);

 // check email exist or not
 $query = "SELECT userEmail FROM users WHERE userEmail='$email'";
 $result = mysql_query($query);

 $count = mysql_num_rows($result); // if email not found then proceed

 if ($count==0) {

  $query = "INSERT INTO users(userName,userEmail,userPass) VALUES('$uname','$email','$password')";
  $res = mysql_query($query);

  if ($res) {
   $errTyp = "success";
   $errMSG = "successfully registered, you may login now";
  } else {
   $errTyp = "danger";
   $errMSG = "Something went wrong, try again later...";
  }

 } else {
  $errTyp = "warning";
  $errMSG = "Sorry Email already in use ...";
 }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coding Cage - Login & Registration System</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

<div class="container">

 <div id="login-form">
    <form method="post" autocomplete="off">

     <div class="col-md-12">

         <div class="form-group">
             <h2 class="">Sign Up.</h2>
            </div>

         <div class="form-group">
             <hr />
            </div>

            <?php
   if ( isset($errMSG) ) {

    ?>
    <div class="form-group">
             <div class="alert alert-<?php echo ($errTyp=="success") ? "success" : $errTyp; ?>">
    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                </div>
             </div>
                <?php
   }
   ?>

            <div class="form-group">
             <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
             <input type="text" name="uname" class="form-control" placeholder="Enter Username" required />
                </div>
            </div>

            <div class="form-group">
             <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
             <input type="email" name="email" class="form-control" placeholder="Enter Your Email" required />
                </div>
            </div>

            <div class="form-group">
             <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
             <input type="password" name="pass" class="form-control" placeholder="Enter Password" required />
                </div>
            </div>

            <div class="form-group">
             <hr />
            </div>

            <div class="form-group">
             <button type="submit" class="btn btn-block btn-primary" name="btn-signup">Sign Up</button>
            </div>

            <div class="form-group">
             <hr />
            </div>

            <div class="form-group">
             <a href="index.php">Sign in Here...</a>
            </div>

        </div>

    </form>
    </div>

</div>

</body>
</html>

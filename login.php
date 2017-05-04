<?php
require_once('src/php/require.php');
require_once('mcl_Oci.php');
//require_once("mcl_Ldap.php");
$authorized = ($user["status"] == "authorized" ? 1 : 0);
if($authorized) {
  header("Location: http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/");
  die();
}

mcl_Html::s(mcl_Html::INC_CSS, 'src/css/login.css');

mcl_Html::s(mcl_Html::SRC_JS, <<<JS
  dojo.require("dojo.window");

  var resize_logo = function() {
    var container = dojo.byId("logo");
    if (!container) {
      return;
    }
    
    var wb = dojo.window.getBox();
    container.style.left = (wb.w / 2) - (container.offsetWidth / 2) + "px";
  };

  dojo.connect(window, "onresize", function() {
    resize_logo();
  });

  dojo.ready(function() {
    var username = dojo.byId('username');
  
    try {
      var x = new ActiveXObject("WScript.Network");
      username.value = x.UserName;
    } catch (_e) { }
  
    resize_logo();
    
    if (username.value != "") {
      dojo.byId('password').focus();
    } else {
      username.focus();
    }
  });

JS
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>Validations Dashboard</title>

  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <!--Self made style sheet-->
  <link rel="stylesheet" href="style.css"> 

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="design.css">
  <link href="css/signin.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>
  <div class="login">
  <div class="container">
      <form class="form-signin" role="form" action="index.php" method='post'>
        <h2 class="form-signin-heading">Validations Dashboard</h2>

        <!--Inputs-->
        <div align="left">
        <label>Username:</label>
        </div>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>

         <div align="left">
        <label>Password:</label>
        </div>
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <!--end-->

        <br>

        <!--Login button-->
        <div id = "Login_Button">
        <input type="submit" value="Login" class="btn btn-lg btn-primary btn-block">
        </div>
        <!--end-->
      </form>
  </div>
</div>

<br>

<div id="login_footer" style="background-image: url(css/Images/dte_logo.png)">
  <b>Any questions about this dashboard contact:</b>
  <br>
  Alaa A Al-Naber [:alaa.al-naber@dteenergy.com]
  <br>
  Brian Atiyeh [:brian.atiyeh@dteenergy.com]
  <br>
  Daoud Sleiman [:daoud.sleiman@dteenergy.com]
</div>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

</body>
</html>
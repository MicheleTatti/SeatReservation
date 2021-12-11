<?php

session_start();

setcookie('testcookie', "logIn");

if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {} else {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 30 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

if(isset($_SESSION['user'])) {
  header('Location: index.php');
  exit();
}

if(!isset($_COOKIE['testcookie'])) {
  if(isset($_GET['cookiecheck'])) {

    die("<title>Log-In</title><h1>Cookies are not enabled</h1><br><p>Plese enable cookies if you want to use this website</p>");
  }
  else {

    die(header('Location: https://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']. '?cookiecheck=1'));
  }
}


include "myFunctions.php";

$errore = "";

if(isset($_POST['user']) && isset($_POST['pwd'])) {

  $conn = mysqli_connect("localhost", "s265607", "citarryn", "s265607");
  $user = sanitizeStringsql($_POST['user'], $conn);
  $pwd = md5($_POST['pwd']);
  $query = "SELECT count(*) FROM users WHERE username='$user' and passwd='$pwd'";
  $re = mysqli_query($conn, $query);

  if (! $re) {
    die("Error:". mysqli_error($conn)."\n" );
  }

  $num = mysqli_fetch_array($re);

  if ($num['count(*)'] != 1) {
    $errore= "Wrong Username or Password";
  } else {
    if(!isset($_SESSION['user']))
    $_SESSION['user'] = $user;
    $_SESSION['time'] = time();
    header('Location: index.php');
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>

  <script src="jquery.js"></script>
  <link rel='stylesheet' type="text/css" href="esonero2.css">
  <title>Log-In</title>
  <script><!--
    $(document).ready( function() {
      $("#sbm").show().fadeOut(2500, function(){ $(this).remove()
      });
    });

    $(document).ready(function()
    {
    var new_url = location.href
    var url=new_url.split('?');
    window.history.replaceState({}, "Registration", url[0]);

    })
    //--></script>
</head>
<body>

  <noscript>
    <h1>Sorry: Your browser does not support or has disabled javascript</h1>
    <style display="none">
  </noscript>

  <header> Please Log In to access this website! </header>

  <?php if(isset($_GET['msg'])){
   echo" <p> You were logged out by the system! Log In again </p>";
  }  ?>


  <nav>
    <ul>
    <li class="buttons"><a href='index.php'>Home</a></li>
    <li class="buttons-middle"><a href='signUp.php'>Sign up</a></li>
    <li class="buttons"><a href='logIn.php'>Log In</a></li>
    </ul>
  </nav>

  <section class="logIn">
    <?php
    echo "
    <form class=\"credentials\" action=\"logIn.php\" method=\"POST\">
    <label for=\"usr\">Username</label><br>
    <input id=\"usr\" name=\"user\" type=\"email\" ></input><br>
    <label for=\"pwd\">Password</label><br>
    <input id=\"pwd\" name=\"pwd\" type=\"password\"></input><br>
    <span id=\"sbm\" style=\"color:red; text-align:center\">$errore</span><br>
    <input type=\"submit\" value=\"Log In\"></input><br>
    </form>";
    ?>

  </section>

  <noscript>
    </style>
  </noscript>

</body>

</html>

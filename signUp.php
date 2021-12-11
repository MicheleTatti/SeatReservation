<?php
session_start();

setcookie('testcookie', "signUp");

if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {} else {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 30 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if (! isset($_COOKIE['testcookie'])) {
    if (isset($_GET['cookiecheck'])) {

        die("<title>Registration</title><h1>Cookies are not enabled</h1><br><p>Plese enable cookies if you want to use this website</p>");
    } else {

        die(header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?cookiecheck=1'));
    }
}

include "myFunctions.php";

$error = "";

if (isset($_POST['user']) && isset($_POST['password'])) {

    if ($_POST['password'] == "" || preg_match("/^((?=.*[a-z])(?=.*[0-9A-Z]))/", $_POST['password']) == 0 || $_POST['user'] == "" || preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/", $_POST['user']) == 0) {
        $error = "Invalid username or password!";
    }

    if ($error == "") {
        $conn = mysqli_connect("localhost", "s265607", "citarryn", "s265607");
        $user = sanitizeStringsql($_POST['user'], $conn);
        $pwd = md5($_POST['password']);
        $query = "SELECT count(*) FROM users WHERE username='$user'";
        $re = mysqli_query($conn, $query);

        if (! $re) {
            $error = mysqli_error($conn);
        }

        $row_cnt = mysqli_fetch_array($re);

        if ($row_cnt[0] == 0) {
            $query = "INSERT INTO `users`(`username`, `passwd`) VALUES ('$user', '$pwd')";
            $re = mysqli_query($conn, $query);
        }
        if (! $re) {
            $error = mysqli_error($conn);
        } else {
            $_SESSION['user'] = $user;
            $_SESSION['time'] = time();
            header('Location: index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<script src="jquery.js"></script>
<title>Registration</title>
<link rel='stylesheet' type="text/css" href="esonero2.css">
<script><!--

  $(document).ready(function()
  {
    var new_url = location.href
    var url=new_url.split('?');
    window.history.replaceState({}, "Registration", url[0]);

  })

  $(document).ready(function(){

    $("#usr").blur(function(){
      checkUser($("#usr"));
    })

    $("#pwd").blur(function(){
      validatePassword($("#pwd").val());
    })})


    function validatePassword($field)
    {
      $str = "";
      if ($field == "")
      $str = "No Password was entered."
      else if ($field.length < 2)
      $str = "Passwords must be at least 2 characters."
      else if (!/[a-z]/.test($field) || ! (/[A-Z]/.test($field) || /[0-9]/.test($field)))
      $str="Passwords require at least one of a-z, and at least one A-Z or 0-9."

      $("<span style=\"color:red\">"+$str+"</span>").appendTo("#pas").show().fadeOut(3000, function(){ $(this).remove()})

    }


    function checkUser($user)
    {
      $("#sbm").html("");
      if ($user.val() == '')
      {
        $('info').html('')
      }

      var $params  = $user.val()
      var $request;
      $request = new ajaxRequest()

      $request.open("POST", "checkUser.php", true) // preparing a request
      $request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      $request.send("user="+$params) //sending parameters

      $request.onreadystatechange = showUsername;
      $request.clear;
    }


    function showUsername()
    {
      if (this.readyState == 4)
      {
        if (this.status == 200)
        {
          if (this.responseText != null)
          {

            $(this.responseText).appendTo("#info").show().fadeOut(3000, function(){ $(this).remove()})

          }
          else alert("Ajax error: No data received")
        }
        else alert( "Ajax error: " + this.statusText)
      }
    }

    function ajaxRequest() {
      try {
        var $request = new XMLHttpRequest()
      } catch(e1){
        try {
          $request = new ActiveXObject("Msxml2.XMLHTTP")
        } catch(e2){
          try {
            $request = new ActiveXObject("Microsoft.XMLHTTP")
          } catch(e3){
            $request = false
          }
        }
      }
      return $request
    }

//--> </script>
</head>

<body>
	<header> Registration! </header>

  <noscript>
    <h1>Sorry: Your browser does not support or has disabled javascript<h1>
    <style display="none">
  </noscript>

	<nav>
		<ul>
			<li class="buttons"><a href='index.php'>Home</a></li>
			<li class="buttons-middle"><a href='signUp.php'>Sign up</a></li>
			<li class="buttons"><a href='logIn.php'>Log In</a></li>
		</ul>
	</nav>


	<section class="signup">
        <?php
        echo "
            <form class=\"credentials\" action=\"signUp.php\" method=\"POST\">
            <label for=\"usr\">Username</label><br>
            <input id=\"usr\" name=\"user\" type=\"email\" ></input><br>
            <div id=\"info\" ></div><br>
            <label for=\"pwd\">Password</label><br>
            <input id=\"pwd\" name=\"password\" type=\"password\"></input><br>
            <div id=\"pas\"></div><br>
            <span id=\"sbm\" style=\"color:red text-align:center\">$error</span><br>
            <input type=\"submit\" value=\"Register\"></input><br>
            </form>";
        ?>
     </section>


</body>
</html>

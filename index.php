<?php
session_start();

setcookie('testcookie', "index");

if (! isset($_COOKIE['testcookie'])) {
    if (isset($_GET['cookiecheck'])) {

        die("<title>Home</title><h1>Cookies are not enabled</h1><br><p>Plese enable cookies if you want to use this website</p>");
    } else {
        if (isset($_SESSION['user'])) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?cookiecheck=1');
            exit();
        } else {
            header('Location: index.php?cookiecheck=1');
            exit();
        }
    }
}
include "myFunctions.php";

$errore = "";
$message = "";

if (isset($_SESSION['user'])) {

    if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $user = sanitizeString($_SESSION['user']);
        $loggedin = TRUE;
    } else {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 30 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }

    $t = time();
    $diff = 0;
    $new = false;

    if (isset($_SESSION['time'])) {
        $t0 = $_SESSION['time'];
        $diff = ($t - $t0);
    } else {
        $new = true;
    }
    if ($new || ($diff > 120)) {
        destroySession();
    } else {
        $_SESSION['time'] = time();
    }
} else
    $loggedin = false;

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">

  <title>Home</title>
  <link rel='stylesheet' type="text/css" href="esonero2.css">
  <script src="jquery.js"></script>
  <script><!--

  vector_id=new Array();

  $timeout = setTimeout(reloc, 120000);
  $lockcheckSeat = false;

  function reloc(){
    $lockcheckSeat = true;
    $("td").click(function(){
      location.replace("logOut.php?msg=timeout");
    })


  }

  $(document).ready(function()
  {
    var new_url = location.href
    var url=new_url.split('?');
    window.history.replaceState({}, "Registration", url[0]);

  })

  $(document).ready( function(){

    $("#hiddenSeats").html("")
    for(var i=0; i<vector_id.length; i++) {
      var x = vector_id[i].split(" ");
      $("#cart-info").append("<li style=\"color:green\"><h3>"+x[1]+x[0]+"<h3></li>");
      $("#hiddenSeats").append('<input type="hidden" id="my'+x[0]+" "+x[1]+'" name="seats['+x[0]+x[1]+']" value="'+x[0]+"_"+x[1]+'"/>');
    }

  })

  function swapArray(arr, obj) {
    for(var i=0; i<arr.length; i++) {
      if (arr[i] == obj){
        tmp = arr[arr.length-1]
        arr[i] = tmp;
        arr[arr.length-1] = obj;
        return;
      }
    }
  }




  function checkSeat($seat)
  {

    clearTimeout($timeout);
    $timeout = setTimeout(reloc, 120000);


    $str = $seat.attr('id').split(" ");
    $status = $seat.attr('class');

    var $r  = $str[0];
    var $c  = $str[1];
    var $request;
    $request = new ajaxRequest()

    $request.open("POST", "checkSeats.php", true) // preparing a request
    $request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    $request.send("s_row="+$r+"&s_col="+$c+"&s_status="+$status) //sending parameters

    $request.onreadystatechange =   function()
    {
      if (this.readyState == 4)
      {
        if (this.status == 200)
        {
          if (this.responseText != null)
          {
            $seat.attr('class' ,   this.responseText) ;

            if(this.responseText == 'myreserved')
            {
              vector_id.push($seat.attr('id'));

            } else if(this.responseText == 'free')
            {
              swapArray(vector_id, $seat.attr('id'));
              vector_id.pop();
              $("#hiddenSeats").remove("#my"+$seat.attr('id')+"");

            }

            $('<span class="'+this.responseText+'">'+$c+$r+' </span>').prependTo("#alert").show().fadeToggle(3000, function(){ $(this).remove()})

            $("#cart-info").html("")
            $("#hiddenSeats").html("")
            for(var i=0; i<vector_id.length; i++) {
              var x = vector_id[i].split(" ");

              $("#cart-info").append("<li style=\"color:green\"><h3>"+x[1]+x[0]+"<h3></li>");
              $("#hiddenSeats").append('<input type="hidden" id="my'+x[0]+" "+x[1]+'" name="seats['+x[0]+x[1]+']" value="'+x[0]+"_"+x[1]+'"/>');
            }
          }
          else alert("Ajax error: No data received")
        }
        else alert( "Ajax error: " + this.statusText)
      };

      $request.clear;
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

  vector_id=new Array();;

  //--> </script>

</head>
<body>

  <noscript>
    <h1>Sorry: Your browser does not support or has disabled javascript</h1>
    <style display="none"></style>
  </noscript>


<?php
  if ($loggedin){
    echo "<header> Welcome $user!</header><p>You can now choose your seats for the flight</p>
    <div id=\"alert\" class=\"alert\"></div>";

    echo "<nav>
    <ul>
    <li class=\"buttons\"><a href='index.php'>Home</a></li>
    <li class=\"buttons-middle\"  id='update'><a href=\"index.php\">Update</a></li>
    <li class=\"buttons\"><a href='logOut.php'>Log Out</a></li>
    </ul></nav>";
  }else {
    echo "<header> Welcome to our homepage!</header>";
    echo "<nav><ul>
    <li class=\"buttons\"><a href='index.php'>Home</a></li>
    <li class=\"buttons-middle\"><a href='signUp.php'>Sign up</a></li>
    <li class=\"buttons\"><a href='logIn.php'>Log In</a></li>
    </ul>
    </nav>";
  }
?>

<div class="center">
  <div class="colsx">
  <table>
    <?php
    if (isset($_POST['seats']) && $loggedin == TRUE) {

        $conn = mysqli_connect("localhost", "s265607", "citarryn", "s265607");

        $client_res_seats = $_POST['seats'];
        $user = sanitizeStringsql($user, $conn);

        try {
            mysqli_autocommit($conn, false);

            $query = "SELECT p_row, p_column from seats where user = '$user' and p_status = 'reserved' FOR UPDATE";
            if (! ($response = mysqli_query($conn, $query)))
                throw new Exception("An error has occurred!");
            $server_res_seats = array();
            while ($row = mysqli_fetch_array($response)) {
                $server_res_seats[] = "$row[0]_$row[1]";
            }
            $diff = array_diff($client_res_seats, $server_res_seats);

            if (empty($diff) == true) {
                $query = "UPDATE seats SET p_status = 'purchased' where user = '$user' and p_status = 'reserved'";

                if (! ($response = mysqli_query($conn, $query)))
                    throw new Exception("Sorry: An error has occurred while updating your information");
            } else {
                forEach ($diff as $key => $val) {

                    $val = sanitizeString($val, $conn);
                    $place = explode("_", $val);
                    $query = "SELECT * from seats where  p_row ='$place[0]' and p_column='$place[1]' ";
                    $response = mysqli_query($conn, $query);

                    if (mysqli_num_rows($response) != 0)
                        throw new Exception("Seats belong to someone else ");
                    $query = "INSERT INTO `seats`VALUES ('$place[0]', '$place[1]', 'purchased', '$user')";

                    if (! mysqli_query($conn, $query))
                        throw new Exception("An error has occurred while inserting data");
                }
            }

            mysqli_commit($conn);
            mysqli_autocommit($conn, true);
            $message = "Purchase completed";
        } catch (Exception $e) {
            $message = "Something went wrong while purchasing your seats";
            mysqli_rollback($conn);
            mysqli_autocommit($conn, true);
            $command1 = "DELETE from seats where user = '$user' and p_status='reserved'";
            mysqli_query($conn, $command1);

            $message = $e->getMessage();

        }
          echo "<script type='text/javascript'>$(document).ready(function(){alert('$message')});</script>";
    }

    createTable();
    ?>
  </table>
</div>

  <div class="coldx">

<?php
if($loggedin){

  echo "

      <ul class='summary'>
      <h2>&#x270E Reserved seats:</h2>
      <div id=\"cart-info\"></div>
      <form action=\"index.php\" method=\"POST\">
      <div id=\"hiddenSeats\">
      </div>
      <input class=\"buttons\" type=\"Submit\" value=\"PURCHASE\">
      </form>";

  echo "<script>
            $(\"td\").click(function(){

                if(\$lockcheckSeat)
                   return;

                if($(this).attr('class') == 'purchased' ){
                 $('<span class=\"purchased\">&nbsp;&#x2718;</span> ').prependTo(\"#alert\").show().fadeToggle(3000, function(){ $(this).remove()})
                 return
                }

            checkSeat($(this));
          });
      </script>";


}else{

  echo "<div class=\"summary\"><h2 class=\"summary\"> $totSeats total seats</h2><ul class='summary'>
   <li class=\"summary\"><h3 class=\"summary\" style=\"color:green\"> &#x2714 $avlSeats available</h3></li><br>
   <li class=\"summary\"><h3 class=\"summary\" style=\"color:red\"> &#x2718 $purSeats purchased</h3></li><br>
   <li class=\"summary\"><h3 class=\"summary\" style=\"color:orange\"> &#x270E $resSeats reserved</h3></li></ul></div>";
}
?>
</div>
</div>

</body>
</html>

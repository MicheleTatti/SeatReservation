<?php

include "myFunctions.php";

if (isset($_POST['user']))
{

  $conn = mysqli_connect("localhost");
  $user = sanitizeStringsql($_POST['user'], $conn);

  $query="SELECT count(*) FROM users WHERE username='$user'";
  $re = mysqli_query($conn, $query);

  if (!$re) {
    printf("Error: %s\n", mysqli_error($conn));
  }

  $row_cnt = mysqli_fetch_array($re);

  if ($row_cnt[0] > 0){

    echo  "<span id=\"info\" style=\"color:red\">&nbsp;&#x2718;This username is taken</span>";

  }
  else{
    if($user == "" || preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/",$user) ==0){
      echo  "<span id=\"info\" style=\"color:red\">&nbsp;&#x2718;Please insert a valid email</span>";
    }else{
      echo "<span id=\"info\" style=\"color:green\">&nbsp;&#x2714; This username is available</span>";
    }
  }

}
?>

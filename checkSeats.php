<?php
session_start();

if (isset($_POST['s_row']) && isset($_POST['s_col']) && isset($_POST['s_status']) && isset($_SESSION['user']))
{

  $row = $_POST['s_row'];
  $col = strtoupper($_POST['s_col']);
  $stato = $_POST['s_status'];
  $user = $_SESSION['user'];

  if(isset($_SESSION['time']))
    $_SESSION['time']=time();

  $conn = mysqli_connect("localhost", "s265607", "citarryn", "s265607");
  $query="SELECT * FROM seats WHERE p_row='$row' and p_column='$col'";
  $re = mysqli_query($conn, $query);

  if (!$re) {
    printf("Error: %s\n", mysqli_error($conn));
  }

  if(mysqli_num_rows($re) == 0){

    $query1 = "INSERT INTO `seats`(`p_row`, `p_column`, `p_status`, `user`) values($row, '$col','reserved', '$user');";
    $re = mysqli_query($conn, $query1);
    if (!$re) {
      $error = mysqli_error($conn);
      echo "Error: %s\n," .$error ;
      mysqli_close($conn);
      return;

    }
    echo  "myreserved";
    mysqli_close($conn);


  }
  else{
    $rows = mysqli_fetch_array($re);
    if($rows['p_status'] == 'purchased'){
      echo  "purchased";
      mysqli_close($conn);
    }else if($rows['p_status'] == 'reserved' && $rows['user'] != $user ){

      if($_POST['s_status'] == 'myreserved'){
        echo  "free";
        mysqli_close($conn);

     }else{

      $query1 = "UPDATE seats SET user='$user' WHERE p_row=$row and p_column='$col';";
      $rex = mysqli_query($conn, $query1);
      if (!$rex) {
        $error = mysqli_error($conn);
        echo "Error: %s\n," .$error ;
        mysqli_close($conn);
        return;
      }
      mysqli_close($conn);
      echo  "myreserved";
    }
    }else {
      $query1 = "DELETE FROM seats WHERE p_row=$row and p_column='$col';";
      $re = mysqli_query($conn, $query1);
      mysqli_close($conn);
      echo  "free";
    }
  }
}
?>

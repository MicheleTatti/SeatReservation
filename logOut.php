<?php
session_start();
$_SESSION=array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600*24,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
if(isset($_GET['msg'])){
 header('Location: logIn.php?msg=TimeOut');
exit;
}else{
 header('Location: index.php');
 exit;
}

?>

<?php
$totSeats = 0;
$avlSeats = 0;
$purSeats = 0;
$resSeats = 0;


function createTable()
{

    global $totSeats;
    global $avlSeats;
    global $purSeats;
    global $resSeats;

    $rows = 10;
    $cols = 6;

    $totSeats = $rows * $cols;
    $avlSeats = $totSeats;

    $let = range('A','Z');

    echo "<tr>";
    for ($j = 0; $j < $cols; $j ++) {
        if ($j == 3) {
            echo "<th background=\"#000000\"></th>";
        }
        echo "<th>" . $let[$j] . "</th>";
    }
    echo "</tr>\n";

    $seats = checkSeats();

    for ($i = 1; $i <= $rows; $i ++) {
        echo "<tr>";
        for ($j = 0; $j < $cols; $j ++) {

            if ($j == 3) {
                echo "<td class=\"corridoio\">" . $i . "</td>";
            }
            if (isset($seats[$i][$let[$j]])) {

                if ($seats[$i][$let[$j]] == "reserved") {
                    $resSeats += 1;
                    echo "<td id=\"" . $i . " " . $let[$j] . "\"  class=\"reserved\"></td>";
                } else if ($seats[$i][$let[$j]] == "purchased") {
                    $purSeats += 1;
                    echo "<td id=\"" . $i . " " . $let[$j] . "\" class=\"purchased\" ></td>";
                } else if ($seats[$i][$let[$j]] == "myreserved") {
                    echo "<td id=\"" . $i . " " . $let[$j] . "\"  class=\"myreserved\"></td> <script> vector_id.push(\"" . $i . " " . $let[$j] . "\");</script>";
                }
            } else {
                $seats[$i][$let[$j]] = 'free';
                echo "<td id=\"" . $i . " " . $let[$j] . "\" class=\"free\" ></td>";
            }
        }
        echo "</tr>\n";
    }

    $avlSeats -= ($purSeats + $resSeats);
}

function checkSeats()
{
    $conn = mysqli_connect("localhost", "s265607", "citarryn", "s265607");
    $query = "Select * from seats";
    $re = mysqli_query($conn, $query);

    if (! $re) {
        printf("Error: %s\n", mysqli_error($conn));
    }

    if (mysqli_num_rows($re) == 0) {
        mysqli_close($conn);
        return;
    }
    while (($row_cnt = mysqli_fetch_array($re)) > 0) {

        if (isset($_SESSION['user']) && $row_cnt[2] == 'reserved' && $_SESSION['user'] == $row_cnt[3]) {
            $seats[$row_cnt[0]][strtoupper($row_cnt[1])] = 'myreserved';
        } else
            $seats[$row_cnt[0]][strtoupper($row_cnt[1])] = $row_cnt[2];
    }
    mysqli_close($conn);
    return $seats;
}

function sanitizeStringsql($var, $conn)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return mysqli_real_escape_string($conn, $var);
}


function sanitizeString($var)
{

    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $var;
}


function destroySession()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600 * 24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header('HTTP/1.1 307 temporary redirect');
    header('Location: logIn.php?msg=SessionTimeOut');
    exit();
}

?>

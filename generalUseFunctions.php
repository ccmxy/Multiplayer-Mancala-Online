<?php
ob_start();
session_start();
require_once 'dbconnect.php';
//I thought I could use this but it doesn't seem to be working,
// so I'm using some depricated functions that were in the code
// I found for using sessions instead of this.
function getUserRow(){
    $user_id =  $_SESSION['user'];
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    $user = array();
    if (!($stmt = $mysqli->prepare("SELECT users.userName, users.userId FROM `users` WHERE users.userId=$user_id"))) {
        echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }
    if (!$stmt->bind_result($user['userName'], $user['userId'])) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    while ($stmt->fetch()) {
      return $user;
    }
    //I'm not sure how to close the connection after a return statement
}


function isGameActive($game_id)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT games.active FROM `games` WHERE games.id=$game_id"))) {
        echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->bind_result($game_active)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        if ($game_active === 0) {
            return False;
        } else {
            return True;
        }
    }
}
?>

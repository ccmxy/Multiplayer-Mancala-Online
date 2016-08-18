<?php

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");

//Set endzone seedCount to 0
if (!($stmt = $mysqli->prepare("UPDATE endzones SET seedCount = 0"))) {
    echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//Set other pits seedCount to 4
if (!($stmt = $mysqli->prepare("UPDATE pits SET seedCount = 4"))) {
    echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}


header("Location: board.php");
exit;


 ?>

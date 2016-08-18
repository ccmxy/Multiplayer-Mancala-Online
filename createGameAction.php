<?php
ob_start();
session_start();
require_once 'dbconnect.php';

if( !isset($_SESSION['user']) ) {
 header("Location: index.php");
 exit;
}
// select loggedin users detail

$res=mysql_query("SELECT * FROM users WHERE userId=".$_SESSION['user']);
$userRow=mysql_fetch_array($res);
$user_id = $userRow['userId'];

 //Turn on error reporting
 ini_set('display_errors', 'On');
 //Connects to the database

 $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
 if($mysqli->connect_errno){
 	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
 	}


 if ($_POST){
 	$name     = $_POST['name'];
 }

 // select loggedin users detail
 $res=mysql_query("SELECT * FROM users WHERE userId=".$_SESSION['user']);
 $userRow=mysql_fetch_array($res);

//Create game
if (!($stmt = $mysqli->prepare("INSERT INTO `games`(name, turn, active) VALUES ('$name', 'Player1', '0')"))) {
    echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//
//Get the id of the newly created game
$game_id = getGameId($name);


//Create a player to act as the agent for the user to play during the game
$user_id = $userRow['userId'];
if (!($stmt = $mysqli->prepare("INSERT INTO `players`(name, turn, game_id, user_id) VALUES ('Player1', 1, $game_id, 0)"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}
//
//Get the id of the player to add to endzone and players_games
$player_name = 'Player1';
$player_id = getPlayerId($game_id, $player_name);
//
//
//Create and endzone (this will belong to the player about to be created)
if (!($stmt = $mysqli->prepare("INSERT INTO `endzones`(seedCount, game_id, player_id) VALUES (0, $game_id, $player_id)"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}
//Create pits for player

if (!($stmt = $mysqli->prepare("INSERT INTO `pits` VALUES (1,'pit1',4,$player_id),(2,'pit2',4,$player_id),(3,'pit3',4,$player_id),(4,'pit4',4,$player_id),(5,'pit5',4,$player_id),(6,'pit6',4,$player_id);
"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//Put the player and game into players_games
if (!($stmt = $mysqli->prepare("INSERT INTO `players_games`(player_id, game_id) VALUES ($player_id, $game_id)"))) {
    echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}


//Create a player to act as the agent for the user to play during the game
//Player 2 will have their user_id set to 0 for now
if (!($stmt = $mysqli->prepare("INSERT INTO `players`(name, turn, game_id, user_id) VALUES ('Player2', 0, $game_id, $user_id)"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//Get the id of the player to add to endzone and players_games
$player_name = 'Player2';
$player_id = getPlayerId($game_id, $player_name);

//Create an endzone for player 2
if (!($stmt = $mysqli->prepare("INSERT INTO `endzones`(seedCount, game_id, player_id) VALUES (0, $game_id, $player_id)"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}
//Create pits for player 2
if (!($stmt = $mysqli->prepare("INSERT INTO `pits` VALUES (7,'pit1',4,$player_id),(8,'pit2',4,$player_id),(9,'pit3',4,$player_id),(10,'pit4',4,$player_id),(11,'pit5',4,$player_id),(12,'pit6',4,$player_id);
"))) {
  echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//Put the player and game into players_games
if (!($stmt = $mysqli->prepare("INSERT INTO `players_games`(player_id, game_id) VALUES ($player_id, $game_id)"))) {
    echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
}
if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}

//Send the user to their new game board:
$user_name = $userRow['userName'];
$msg = "Welcome to your new game, $user_name! Just have someone else join in to begin playing :)";
loadTheBoard($msg, $game_id);

//Funtions:
function getGameId($game_name){
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
  if (!($stmt = $mysqli->prepare("SELECT games.id FROM `games` WHERE games.name = '$game_name'"))) {
      echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
  }
  if (!$stmt->execute()) {
      echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
  }
  if(!$stmt->bind_result( $game_id)){
    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  }
  // $stmt->fetch();
  while($stmt->fetch()){
  	return $game_id;
  }
}

function getPlayerId($game_id, $player_name){
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
  if (!($stmt = $mysqli->prepare("SELECT players.id FROM `players` WHERE players.game_id=$game_id AND players.name='$player_name'"))) {
      echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
  }
  if (!$stmt->execute()) {
      echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
  }
  if(!$stmt->bind_result( $player_id)){
    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  }
  while($stmt->fetch()){
  	return $player_id;
  }
}

  function loadTheBoard($msg, $game_id){
    echo "
    <html>
    <head></head>

    <body>

    <form action='board.php' method='post' name='bad_choice_frm'>
      <input type='hidden' name='returnMsg' value='$msg'>
      <input type='hidden' name='gameId' value='$game_id'>
    </form>

    <script>
      document.bad_choice_frm.submit();
    </script>
    </body>
    </html>
    ";
  }


?>

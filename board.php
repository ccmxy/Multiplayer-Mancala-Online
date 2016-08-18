<?php
//Turn on error reporting
include './setEndZone2.php';
include './setEndZone1.php';
include './printPits.php';
include './boardRetreivalFunctions.php';
include './generalUseFunctions';

ob_start();
session_start();
require_once 'dbconnect.php';

if( !isset($_SESSION['user']) ) {
 header("Location: index.php");
 exit;
}


$res=mysql_query("SELECT * FROM users WHERE userId=".$_SESSION['user']);
$userRow=mysql_fetch_array($res);


$mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
if($mysqli->connect_errno){
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}

  if ($_POST){
  	$returnMsg     = $_POST['returnMsg'];
  	$game_id 			 = $_POST['gameId'];
    $game_name     = $_POST['game_name'];
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <LINK href="menu-bar-css.css" rel="stylesheet" type="text/css">
	<LINK href="style.css" rel="stylesheet" type="text/css">

</head>
<body>

  <ul>
    <li><a href="home.php">Home</a></li>
    <li><a href="joinGamePage.php">Join Game</a></li>
    <li><a href="createGamePage.php">Create Game</a></li>
    <li><a href="gamesUserIsPlayingPage.php">Your Active Games</a></li>
    <li class="float-right"><a href="logout.php?logout"><?php echo $userRow['userEmail']; ?></a></li>
  </ul>
  <div id="text-container">
	<?php

  $game_name = getGameName($game_id);
  echo "<h1 id='game-name'> $game_name</h1>";

  if($returnMsg){
    echo "<h1>$returnMsg</h1>";
  }
 $username = $userRow['userName'];
 //Greet the user:
  echo "<p> Hello, $username. Today's players are... </p>";

   //Print list of players:
	if(!($stmt = $mysqli->prepare("SELECT players.name, users.userName FROM players
    JOIN players_games
    ON players.id=players_games.player_id
    AND players_games.game_id = $game_id
    JOIN users
    ON players.user_id = users.userId"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

	if(!$stmt->execute()){
		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	if(!$stmt->bind_result($player_name, $user_name)){
		echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	while($stmt->fetch()){
		echo "<p> " . $user_name . " playing as $player_name! </p> ";
	}
	$stmt->close();
  //Get the current turn:
  if(!($stmt = $mysqli->prepare("SELECT DISTINCT players.name FROM players JOIN games WHERE games.id = $game_id AND games.turn=players.name"))){
  	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
  }

  if(!$stmt->execute()){
  	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  }
  if(!$stmt->bind_result($name)){
  	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  }
  while($stmt->fetch()){
    echo "Waiting on $name...";
  }
  $stmt->close();

	?>
</div>
	<!-- 					Print the gameboard:					 -->
  <div id="board-container">
<div id="board">
	<?php //Might not need this again here
  if ($_POST){
   $game_id     = $_POST['gameId'];
  }

  $player_1_id = getPlayerId($game_id, 'Player1');

  $player_2_id = getPlayerId($game_id, 'Player2');

	setEndZone2($player_2_id);
  // echo $player_1_id;
	setEndZone1($player_1_id);

	player2Pits($game_id, $player_2_id);
	player1Pits($game_id, $player_1_id);

  function getPlayerId($game_id, $player_name){
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT players.id FROM `players` WHERE players.game_id=$game_id AND players.name='$player_name'"))) {
        echo "Prepare failed on getPlayerId: " . $stmt->errno . " " . $stmt->error;
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
	?>
</div>
</div>
</div>
</body>
</html>

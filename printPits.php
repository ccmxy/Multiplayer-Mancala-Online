<?php

  /******************** PLAYER 2 PITS ****************/
function player2Pits($game_id, $player_2_id){
  ini_set('display_errors', 'On');
  //Connects to the database
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
  if($mysqli->connect_errno){
  	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}

if(!($stmt = $mysqli->prepare("SELECT pits.seedCount, pits.id FROM pits WHERE pits.owner=$player_2_id"))){
	echo "Prepare failed on player2Pits: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($seedCount, $id)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
while($stmt->fetch()){
	echo "<div style='float:right'>
  <form method='post' action='makeMove.php'>
  <input type='hidden' name='game_id' value='" . $game_id . "'>
  <input type='hidden' name='id' value='" . $id . "'>
  <input type='submit' name='numSeeds' value='" . $seedCount . "'>
   </form>
	</div>";
}
$stmt->close();
}


/******************** PLAYER 1 PITS ****************/
function player1Pits($game_id, $player_1_id){
  ini_set('display_errors', 'On');
  //Connects to the database
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
  if($mysqli->connect_errno){
  	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}
    //PROBABLE SOLUTION: Just pass palyer 1 and 2's real ids around, instead of trying to join the player_game etc.
//if(!($stmt = $mysqli->prepare("SELECT pits.id, pits.seedCount FROM pits WHERE pits.owner IN (SELECT players.id FROM players WHERE players.name='Player1' JOIN players_games WHERE players.id=players_games.player_id AND players_games.game_id = $game_id)"))){
if(!($stmt = $mysqli->prepare("SELECT pits.id, pits.seedCount FROM pits WHERE pits.owner=$player_1_id"))){
	echo "Prepare failed on player1Pits: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($id, $seedCount)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
while($stmt->fetch()){
 echo "<div>
 <form method='post' action='makeMove.php'>
 <input type='hidden' name='game_id' value='" . $game_id . "'>
  <input type='hidden' name='id' value='" . $id . "'>
   <input type='submit' name='numSeeds' value='" . $seedCount . "'>
   </form>
   </div> ";
}
$stmt->close();
}
?>

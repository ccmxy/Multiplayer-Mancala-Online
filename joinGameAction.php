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

 ini_set('display_errors', 'On');

 if ($_POST){
 	$game_id 			 = $_POST['gameId'];

 }

  if ($_GET){
    $gameId 			 = $_GET['gameId'];
    $returnMsg            = $_GET['$returnMsg'];
    $game_name            = $_GET['$game_name'];
  }


 $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
 if (!$mysqli || $mysqli->connect_errno) {
     echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
 }

///Find the player named who's id is 0,
// who is in games_players where games_id = $game_id
 if (!($stmt = $mysqli->prepare("UPDATE players JOIN players_games ON players_games.player_id = players.id AND players_games.game_id = $gameId SET user_id = $user_id WHERE user_id = 0 "))) {
     echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
 }
 if (!$stmt->execute()) {
     echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
 }


 if (!($stmt = $mysqli->prepare("UPDATE games SET active = 1 WHERE id = $gameId "))) {
     echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
 }
 if (!$stmt->execute()) {
     echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
 }
 echo "
 <html>
 <head></head>
 <body>
 <h1>" . $game_name . $game_id . "</h1>
 <form method='post' name='formName' action='board.php'>
  <input type='hidden' name='gameId' value='$gameId'>
  <input type='hidden' name='returnMsg' value='$returnMsg'>
  <input type='hidden' name='game_name' value='$game_name'>
  </form>
  <script>
    document.formName.submit();
  </script>
 </body>
 </html>";

 //Join teh game.....

?>

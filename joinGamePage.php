<?php
include './generalUseFunctions.php';

ob_start();
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
$res=mysql_query("SELECT * FROM users WHERE userId=".$_SESSION['user']);
$userRow=mysql_fetch_array($res);
$user_id = $userRow['userId'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome - <?php
echo $userRow['userEmail'];
?></title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"  />
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

 <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="home.php">Mancala</a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="joinGamePage.php">Join Game</a></li>
            <li><a href="createGamePage.php">Create Game</a></li>
            <li><a href="gamesUserIsPlayingPage.php">Your Active Games</a></li>


          </ul>

          <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
     <span class="glyphicon glyphicon-user"></span>&nbsp;Hi' <?php
echo $userRow['userEmail'];
?>&nbsp;<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
              </ul>
            </li>


          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

 <div id="wrapper">

 <div class="container">

     <div class="page-header">
     <h3>Here are some games which need a second player! Click to join!</h3>
     </div>

        <div class="list-group">

    <?php
$user_id             = $userRow['userId'];
$inactive_games_list = getInactiveGames();

printGamesListStepOne($inactive_games_list);

//This is step one of a two part function
//It calls printGamesList to ultimately print
//the list of currently inactive games
function printGamesListStepOne($game_id_array)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");

    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    $game_id_count = count($game_id_array);
    for ($idx = 0; $idx < $game_id_count; $idx++) {
        $game_id = $game_id_array[$idx];
        printGamesListStepTwo($game_id);
    } //end of for loop

} //end of printGamesList

function printGamesListStepTwo($game_id)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");

    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    if (!($stmt = $mysqli->prepare("SELECT games.name, games.id FROM `games` WHERE games.id=$game_id"))) {
        echo "<h3> Actually, there are no games waiting to be joined right now! Why don't you go ahead and <a href='createGamePage.php'>make one?</a></h3>";
    }
    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }
    if (!$stmt->bind_result($game_name, $game_id)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    while ($stmt->fetch()) {
        $msg = "Welcome";

        echo "<div class='row'>
         <div class='col-lg-12'>
         <a href='joinGame.php?gameId=" . urlencode($game_id) . "&returnMsg=" . urlencode($msg) . "&name=" . urlencode($game_name) . "'class='list-group-item list-group-item-action'> " . $game_name . "<a>
         </div>
         </div>";
    }
}

function loadTheBoard($msg, $game_id)
{
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

//Function to return a list of all games which are not currently active, and which
//neither of the players have a user_id of 0.
// This is because when a game is created player 2's user id is set to 0,
// but then it is set to their real user id when they join. So this is how we
// excluse games that have finished.
function getInactiveGames()
{
    $mysqli        = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    $game_id_array = array();
    $i             = 0;
    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    if (!($stmt = $mysqli->prepare("SELECT games.id FROM `games`
      JOIN players_games
      ON players_games.game_id = games.id
      JOIN players
      ON players_games.player_id = players.id
      AND players.user_id = 0
      WHERE games.active=0
      "))) {
        echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
    }
    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }
    if (!$stmt->bind_result($game_ids)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
    while ($stmt->fetch()) {
        $game_id_array[$i] = $game_ids;
        $i++;
    }
    if (count($game_id_array) > 0) {
        return $game_id_array;
    } else {
        return false;
    }
}

?>

  </div>

    </div>

    </div>

    <script src="assets/jquery-3.1.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

</body>
</html>

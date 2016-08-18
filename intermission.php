<?php
//This is just a file that I use after a use clicks a game on their joined games list.
//The purspose is to so that I can use a GET request in the form of href=
// and then send that info to board.php as a POST.
//If I were to have the clicked links send a POST request then I think I would
// have to use either a javascript onclick listener or a form.

include './generalUseFunctions.php';

ob_start();
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
// select loggedin users detail
$userRow = getUserRow();
//Turn on error reporting
 ini_set('display_errors', 'On');

//Get the variables sent in through the url
 if ($_GET){
   $gameId 			 = $_GET['gameId'];
   $returnMsg            = $_GET['$returnMsg'];
   $game_name            = $_GET['$game_name'];
 }

//Go directly to the board with a POST
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
?>

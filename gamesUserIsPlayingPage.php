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
<title>Welcome - <?php echo $userRow['userEmail']; ?></title>
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
            <li><a href="joinGamePage.php">Join Game</a></li>
            <li><a href="createGamePage.php">Create Game</a></li>
            <li class="active"><a href="gamesUserIsPlayingPage.php">Your Active Games</a></li>


          </ul>

          <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
     <span class="glyphicon glyphicon-user"></span>&nbsp;Hi' <?php echo $userRow['userEmail']; ?>&nbsp;<span class="caret"></span></a>
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
     <h3>Here are the games you are currently signed up for</h3>
     </div>


        <div class="list-group">

    <?php
     $res=mysql_query("SELECT * FROM users WHERE userId=".$_SESSION['user']);
     $userRow=mysql_fetch_array($res);
     $user_id = $userRow['userId'];


    //Get the games the user has joined
       $player_ids = getPlayerIds($user_id);


        $user_games_list = getPlayerGames($player_ids);
       printGamesList($user_games_list);

       function printGamesList($game_id_array){
        //  print_r(array_values($game_id_array));

         $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");

         if($mysqli->connect_errno){
           echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
          }
         $game_id_count = count($game_id_array);
         for($idx = 0; $idx < $game_id_count; $idx++){
           $game_id = $game_id_array[$idx];
           printGamesListStepTwo($game_id);
       } //end of for loop

     } //end of printGamesList

     function printGamesListStepTwo($game_id){
       $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");

       if($mysqli->connect_errno){
         echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }
       if (!($stmt = $mysqli->prepare("SELECT games.name, games.id FROM `games` WHERE games.id=$game_id"))) {
           echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
       }
       if (!$stmt->execute()) {
           echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
       }
       if(!$stmt->bind_result( $game_name, $game_id)){
         echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
       }
       while($stmt->fetch()){
         $msg = "Welcome back";

         echo "<div class='row'>
         <div class='col-lg-12'>

        <a href='intermission.php?gameId=" .urlencode($game_id) . "&returnMsg="  .urlencode($msg).  "&name=" .urlencode($game_name)."'class='list-group-item list-group-item-action'> " . $game_name . "<a>

         </div>
         </div>";
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

          function getPlayerGames($player_id_array){
            $player_games_array = array();
            $player_id_count = count($player_id_array);
            for($idx = 0; $idx < $player_id_count; $idx++){
              $player_id = $player_id_array[$idx];
              $player_games_array[$idx] = getGameIdFromPlayerId($player_id);
            }
            if (count($player_games_array) == count($player_id_array)) {
              return $player_games_array;
            } else {
              return false;
            }
          }

         function getGameIdFromPlayerId($player_id){
           $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");

           if($mysqli->connect_errno){
             echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }

            if (!($stmt = $mysqli->prepare("SELECT players_games.game_id FROM `players_games` WHERE players_games.player_id=$player_id"))) {
                echo "<h3> You haven't joined any games! Why not <a href='joinGamePage.php'>join</a> or <a href='createGame'>make</a> one?</h3>";
            }
            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
            }
            if(!$stmt->bind_result( $game_id)){
              echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
            }
            while($stmt->fetch()){
             return $game_id;
            }
          }


       function getPlayerIds($user_id){
         $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
         $player_id_array = array();
         $i = 0;
         if($mysqli->connect_errno){
           echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
          }

          if (!($stmt = $mysqli->prepare("SELECT players.id FROM `players` WHERE players.user_id=$user_id"))) {
              echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
          }
          if (!$stmt->execute()) {
              echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
          }
          if(!$stmt->bind_result( $player_ids)){
            echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
          }
          while($stmt->fetch()){
            $player_id_array[$i] = $player_ids;
            $i++;
          	// return $player_ids;
          }

          if (count($player_id_array) > 0) {
            return $player_id_array;
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

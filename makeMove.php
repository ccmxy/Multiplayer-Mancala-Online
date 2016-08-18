

<?php
//The code here is executed by a user clicking on a pit.

include './generalUseFunctions.php';
//The only function from generalUseFunctions
// that I use in this file is isGameActive()

ob_start();
session_start();
require_once 'dbconnect.php';
//Go to log in if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Turn on error reporting

ini_set('display_errors', 'On');

// Connects to the database

$numSeeds = $_POST['numSeeds']; //The number of seeds in the pit that was chosen.
$pit_id   = $_POST['id']; //The id of the pit that was chosen.
$game_id  = $_POST['game_id']; //Id of the game

// Make sure the game is active:

if (!isGameActive($game_id)) {
    $msg = 'This game is not active.';
    loadTheBoard($msg, $game_id);
    exit;
}

// Check to make sure that it's the user's turn:

if (!isItUsersTurn($game_id)) {
    $msg = 'Please wait your turn.';
    loadTheBoard($msg, $game_id);
    exit;
}

// Check if user clicked on empty pit:

if ($numSeeds === 0) {
    $msg = 'Please choose a pit that has seeds in it.';
    loadTheBoard($msg, $game_id);
    exit;
}

// Make sure user is choosing from their pits:

$whos_turn = getTurn($game_id);

if (strcmp($whos_turn, 'Player1') === 0 AND $pit_id > 6) {
    $msg = 'Player 1, you need to choose from the pits on the BOTTOM row. TURN: Player 1.';
    loadTheBoard($msg, $game_id);
    exit;
}

if (strcmp($whos_turn, 'Player2') === 0 AND $pit_id < 7) {
    $msg = 'Player 2, you need to choose from the pits on the TOP row. TURN: Player 2.';
    loadTheBoard($msg, $game_id);
    exit;
}

// Get the player ids to use in switch statement:

$player_1_id = getPlayerId($game_id, 'Player1');
$player_2_id = getPlayerId($game_id, 'Player2');

// Get 'Player1' or 'Player2' in whos_turn:

$whos_turn = getTurn($game_id);

// Get current player id to modify their pits
// and endzones:

$current_player = getPlayerId($game_id, $whos_turn);

setPitToZero($pit_id, $current_player);
$mysqli         = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");

$state = $pit_id;

//This for loop executes for the duration of the turn,
// dropping a seed in each pit and endzone.
for ($x = 0; $x < $numSeeds; $x++) {
    switch ($state) {
        case 1:
            regularIncrement($game_id, $x, $numSeeds, 2);
            $state = 2;
            break;

        case 2:
            regularIncrement($game_id, $x, $numSeeds, 3);
            $state = 3;
            break;

        case 3;
            regularIncrement($game_id, $x, $numSeeds, 4);
            $state = 4;
            break;

        case 4:
            regularIncrement($game_id, $x, $numSeeds, 5);
            $state = 5;
            break;

        case 5:
            regularIncrement($game_id, $x, $numSeeds, 6);
            $state = 6;
            break;

        case 6:
            if (!($stmt = $mysqli->prepare("UPDATE endzones SET seedCount = seedCount + 1 WHERE player_id = $player_1_id"))) {
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
            }

            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
            }

            $state = "EndZone1";

            // if landing in last pit...

            if ($x === ($numSeeds - 1)) {
                $curr_turn = getTurn($game_id);

                // If it is player 1 landing last in their own pit...

                if (strcmp($curr_turn, 'Player1') === 0) {
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        $msg = "Player 1, since your last seed landed in your own endzone, you get to go again! TURN: Player 1";
                        loadTheBoard($msg, $game_id);
                    } else if ($finished === 1) {
                        gameOver($game_id);
                    }
                } else {
                    changeTurn($curr_turn, $game_id);
                    $curr_turn = getTurn($game_id);
                    $msg       = "Nice move! TURN: $curr_turn";
                    loadTheBoard($msg, $game_id);
                }
            }

            break;

        case "EndZone1":
            regularIncrement($game_id, $x, $numSeeds, 7);
            $state = 7;
            break;

        case 7:
            regularIncrement($game_id, $x, $numSeeds, 8);
            $state = 8;
            break;

        case 8:
            regularIncrement($game_id, $x, $numSeeds, 9);
            $state = 9;
            break;

        case 9:
            regularIncrement($game_id, $x, $numSeeds, 10);
            $state = 10;
            break;

        case 10:
            regularIncrement($game_id, $x, $numSeeds, 11);
            $state = 11;
            break;

        case 11:
            regularIncrement($game_id, $x, $numSeeds, 12);
            $state = 12;
            break;

        case 12:
            if (!($stmt = $mysqli->prepare("UPDATE endzones SET seedCount = seedCount + 1 WHERE player_id = $player_2_id"))) {
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
            }

            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
            }

            $state = "EndZone2";

            // And if this is the last one...

            if ($x === ($numSeeds - 1)) {
                $curr_turn = getTurn($game_id);

                // If it is player 1 landing last in their own pit...

                if (strcmp($curr_turn, 'Player2') === 0) {
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        $msg = "Player 2, since your last seed landed in your own endzone, you get to go again! TURN: Player 2";
                        loadTheBoard($msg, $game_id);
                    }

                    if ($finished === 1) {
                        gameOver($game_id);
                    }
                } else {
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        changeTurn($curr_turn, $game_id);
                        $curr_turn = getTurn($game_id);
                        $msg       = "Nice move! TURN: $curr_turn";
                        loadTheBoard($msg, $game_id);
                    }

                    if ($finished === 1) {
                        gameOver($game_id);
                    }
                }
            }

            break;

        case "EndZone2":
            regularIncrement($game_id, $x, $numSeeds, 1);
            $state = 1;
            break;

        default:
            echo "Default for some reason";
    }
}


function loadTheBoard($msg, $game_id)
{
    $curr_turn = getTurn($game_id);

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

// x holds how far along the increment we are (we started at 0), and numSeeds holds the total number of seeds
// we are dropping.
// nextPit holds the # of seeds in the next pit.

function regularIncrement($game_id, $x, $numSeeds, $pitToIncrement)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if ($x === ($numSeeds - 1)) { //If LAST SEED is landing here...

        $player_1_id = getPlayerId($game_id, 'Player1');
        $player_2_id = getPlayerId($game_id, 'Player2');
        if (!$mysqli || $mysqli->connect_errno) {
            echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }

        if (!($stmt = $mysqli->prepare("UPDATE pits SET seedCount = seedCount + 1 WHERE pits.id = $pitToIncrement AND pits.owner IN ($player_1_id, $player_2_id) "))) {
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!($stmt = $mysqli->prepare("SELECT pits.seedCount FROM pits WHERE pits.id=$pitToIncrement AND pits.owner IN ($player_1_id, $player_2_id)"))) {
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }

        if (!$stmt->bind_result($seedCount)) {
            echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }

        // Then check if it was empty before we just incremented it:

        while ($stmt->fetch()) {
            if ($seedCount == 1) {
                $curr_turn  = getTurn($game_id);
                $gonnaSteal = False;

                // Player1's pits are numbered 1-6
                // If it's Player1's turn and the pit belongs to them, they steal the seeds across from them:

                if (($pitToIncrement) < 7 and strcmp($curr_turn, 'Player1') === 0) {
                    $gonnaSteal = True;
                    stealSeeds($pitToIncrement, $curr_turn, $game_id); //Steel seeds from pit across from it
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        $opposite_turn = getOppositeTurn($curr_turn);
                        $msg           = "Awesome steal, $curr_turn! TURN: $opposite_turn";
                        changeTurn($curr_turn, $game_id);
                        loadTheBoard($msg, $game_id);
                    }

                    if ($finished === 1) {
                        gameOver($game_id);
                    }
                }

                // If it's Player2's turn and the pit belongs to them, steal from the pit across from them:

                if (($pitToIncrement) > 6 and strcmp($curr_turn, 'Player2') === 0) {
                    $gonnaSteal = True;
                    stealSeeds($pitToIncrement, $curr_turn, $game_id);
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        $opposite_turn = getOppositeTurn($curr_turn);
                        $msg           = "Awesome steal, $curr_turn! TURN: $opposite_turn";
                        changeTurn($curr_turn, $game_id);
                        loadTheBoard($msg, $game_id);
                    }

                    if ($finished === 1) {
                        gameOver($game_id);
                    }
                }

                if ($gonnaSteal === False) {
                    $finished = checkIfFinished($player_1_id, $player_2_id);
                    if ($finished === 0) {
                        changeTurn($curr_turn, $game_id);
                        $opposite_turn = getOppositeTurn($curr_turn);
                        $msg           = "Nice move there! TURN: $opposite_turn";
                        loadTheBoard($msg, $game_id);
                    }

                    if ($finished === 1) {
                        gameOver($game_id);
                    }
                }
            }

            // But if it was NOT empty (meaning no steal), but this IS the last one...

            else if ($seedCount > 1) {
                $finished = checkIfFinished($player_1_id, $player_2_id);
                if ($finished === 0) {
                    $curr_turn     = getTurn($game_id);
                    $opposite_turn = getOppositeTurn($curr_turn);
                    $msg           = "Nice move there! TURN: $opposite_turn";
                    changeTurn($curr_turn, $game_id);
                    loadTheBoard($msg, $game_id);
                }

                if ($finished === 1) {
                    gameOver($game_id);
                }
            }
        }
    } else { //if NOT the last move...
        if (!$mysqli || $mysqli->connect_errno) {
            echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }

        if (!($stmt = $mysqli->prepare("UPDATE pits SET seedCount = seedCount + 1 WHERE id = $pitToIncrement"))) {
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
        }
    }
    $stmt->close();
}

function getTurn($game_id)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT games.turn FROM games WHERE id=$game_id"))) {
        echo "Prepare failed on getTurn: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->bind_result($turn)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        $this_turn = (string) $turn;
        return $this_turn;
    }
}

function changeTurn($whos_turn, $game_id)
{
    $Player1 = "Player1";
    $Player2 = "Player2";
    $mysqli  = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (strcmp($whos_turn, $Player1) === 0) {
        if (!($stmt = $mysqli->prepare("UPDATE games SET turn = 'Player2' WHERE id=$game_id"))) {
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
        }
    }

    if (strcmp($whos_turn, $Player2) === 0) {
        if (!($stmt = $mysqli->prepare("UPDATE games SET turn = 'Player1' WHERE id=$game_id"))) {
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
        }
    }
}

function stealSeeds($pitNumber, $whos_turn, $game_id)
{

    // Get the id of the current player

    $player_1_id  = getPlayerId($game_id, 'Player1');
    $player_2_id  = getPlayerId($game_id, 'Player2');
    $whos_turn_id = getPlayerId($game_id, $whos_turn);

    // Get the pod to steal from:

    $podToStealFrom = (13 - $pitNumber);
    $mysqli         = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!$mysqli || $mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    // Get the seed count

    if (!($stmt = $mysqli->prepare("SELECT pits.seedCount FROM pits WHERE id=$podToStealFrom AND owner IN ($player_1_id, $player_2_id)"))) {
        echo "Prepare failed on stealSeeds: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!$stmt->bind_result($seedCount)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        $numberOfSeedsToSteal = $seedCount;
    }

    // Add seeds to end zone
    // if (!($stmt = $mysqli->prepare("UPDATE endzones SET seedCount = seedCount + $numberOfSeedsToSteal WHERE id IN (SELECT endzone FROM players WHERE name='$whos_turn')"))) {

    if (!($stmt = $mysqli->prepare("UPDATE endzones SET seedCount = seedCount + $numberOfSeedsToSteal WHERE player_id = $whos_turn_id"))) {
        echo "Prepare failed on stealSeeds: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }

    // Turn the seed count of robbed pod to 0

    if (!($stmt = $mysqli->prepare("UPDATE pits SET seedCount = 0 WHERE id = $podToStealFrom AND owner IN ($player_1_id, $player_2_id)"))) {
        echo "Prepare failed in stealSeeds: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }
}

function gameOver($game_id)
{
    $player1Sum = getPlayer1Sum($game_id);
    $player2Sum = getPlayer2Sum($game_id);
    $winner     = getWinner($player1Sum, $player2Sum);
    makeGameInactive($game_id);
    $msg = "GAME OVER! Final score: Player 1: $player1Sum --- Player 2: $player2Sum --- Winner: $winner";
    loadTheBoard($msg, $game_id);
}

function makeGameInactive($game_id)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!($stmt = $mysqli->prepare("UPDATE games SET games.active=0 WHERE games.id=$game_id"))) {
        echo "Prepare failed in makeGameInactive: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    $stmt->close();
}

function checkIfFinished($player_1_id, $player_2_id)
{
    $finished = 0;
    $mysqli   = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!($stmt = $mysqli->prepare("SELECT SUM(pits.seedCount)FROM pits WHERE pits.owner=$player_1_id"))) {
        echo "Prepare failed in checkIfFinished: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!$stmt->bind_result($seedCount)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        if ($seedCount == 0) {
            $finished = 1;
            return $finished;
        }
    }

    $stmt->close();
    if (!($stmt = $mysqli->prepare("SELECT SUM(pits.seedCount)FROM pits WHERE pits.owner=$player_2_id"))) {
        echo "Prepare failed in checkIfFinished: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!$stmt->bind_result($seedCount)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        if ($seedCount == 0) {
            $finished = 1;
            return $finished;
        }
    }

    $stmt->close();
    return $finished;
}

function getPlayer1Sum($game_id)
{
    $player_1_id = getPlayerId($game_id, 'Player1');
    $mysqli      = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT SUM(endzones.seedCount) FROM endzones WHERE endzones.player_id = $player_1_id"))) {
        echo "Prepare failed in getPlayer1Sum: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!$stmt->bind_result($seedCount)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        return $seedCount;
    }

    $stmt->close();
}

function getPlayer2Sum($game_id)
{
    $player_2_id = getPlayerId($game_id, 'Player2');
    $mysqli      = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT SUM(endzones.seedCount) FROM endzones WHERE endzones.player_id = $player_2_id"))) {
        echo "Prepare failed in getPlayer2Sum: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if (!$stmt->bind_result($seedCount)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        return $seedCount;
    }

    $stmt->close();
}

function getWinner($player1Sum, $player2Sum)
{
    if ($player1Sum > $player2Sum) {
        return "Player1";
    }

    if ($player2Sum > $player1Sum) {
        return "Player2";
    }

    if ($player1Sum == $player2Sum) {
        return "Tie!";
    }
}

function getOppositeTurn($curr_turn)
{
    if (strcmp($curr_turn, 'Player1') === 0) {
        return 'Player2';
    } else {
        return 'Player1';
    }
}

function getPlayerId($game_id, $player_name)
{
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT players.id FROM `players` WHERE players.game_id=$game_id AND players.name='$player_name'"))) {
        echo "Prepare failed on getPlayerId: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->bind_result($player_id)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        return $player_id;
    }
}

function isItUsersTurn($game_id)
{
    // Get the current user:
    $whos_turn = getTurn($game_id);

    $userRow = getUserRow();

    // Get the current user's user_id:
    $current_user_id = $userRow['userId'];

    // Run a query for the user_id of the current player:
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");
    if (!($stmt = $mysqli->prepare("SELECT players.user_id FROM `players` WHERE players.game_id=$game_id AND players.name='$whos_turn'"))) {
        echo "Prepare failed in isItUsersTurn: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
    }

    if (!$stmt->bind_result($whos_turn_user_id)) {
        echo "Bind failed: " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    while ($stmt->fetch()) {
        if ($whos_turn_user_id == $current_user_id) {
            return True;
        } else {
            return False;
        }
    }
}

//Sets the seedCount of the player's pit that was sent in to 0
function setPitToZero($pit_id, $player){
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "minorc-db", "kLdt7swbvLhRmjzu", "minorc-db");

if (!$mysqli || $mysqli->connect_errno) {
    echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

if (!($stmt = $mysqli->prepare("UPDATE pits SET seedCount = 0 WHERE id = $pit_id AND owner=$player"))) {
    echo "Prepare failed on update pits: " . $stmt->errno . " " . $stmt->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: " . $stmt->errno . " " . $stmt->error;
}
}

?>

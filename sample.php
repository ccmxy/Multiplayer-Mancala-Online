<?php

/*************************  SELECT QUERIES ***********************/
/*Getting the player names (Player1 and Player2) and the username
of the user playing them to print in board.php*/
"SELECT players.name, users.userName FROM players
  JOIN players_games
  ON players.id=players_games.player_id
  AND players_games.game_id = [game id]
  JOIN users
  ON players.user_id = users.userId"

/*Getting the seedCount of a particular pit in a game in MakeMove.php.*/
"SELECT pits.seedCount
  FROM pits
  WHERE pits.id=[id of pit]
  AND pits.owner
  IN ([id of player1 in the game], [id of player 2 in the game])"

/*Getting the total of player 1's seedCount to compare to player 2's
and determine the winner, in makeMove.php*/
"SELECT SUM(endzones.seedCount) FROM endzones WHERE endzones.player_id = [player 1 id]"

/*Getting the player_ids attached to a particular user in gamesUserIsPlayingPage.php*/
"SELECT players.id FROM `players` WHERE players.user_id=$user_id"

/* Getting the game id's in player_games based on a player id,
in order to print all of the games that a user is attached to in gamesUserIsPlayingPage.php*/
"SELECT players_games.game_id FROM `players_games` WHERE players_games.player_id=$player_id"

/*Selecting id of newly created game in createGameAction*/
"SELECT games.id FROM `games` WHERE games.name = '[name that user chose]'"

/************************* UPDATE QUERIES ************************/
/*Here are the 2 queries that are ran when the a player is added to a pre-existing game. When a game is created, the player 2’s user_id is set to 0, so to add a user you just have to find the player in that game who’s user_id is 0 and update it to the user’s id. */
"UPDATE players
  JOIN players_games
  ON players_games.player_id = players.id
  AND players_games.game_id = $gameId
  SET user_id = $user_id
  WHERE user_id = 0 "

/*And then you run this one to set the game to active.*/
"UPDATE games SET active = 1 WHERE id = $gameId "

/*Updating player 1's endzone when a seed lands in it*/
"UPDATE endzones SET seedCount = seedCount + 1 WHERE player_id = $player_1_id"

/*Updating player 1's endzone when a seed lands in it*/
"UPDATE pits
SET seedCount = seedCount + 1
WHERE pits.id = [id of pit that is being updated]
AND pits.owner
IN ([id of player 1 in this game], [id of player 2 in this game])"

/******************************** INSERT QUERIES ******************/

/*Creating a new game: All of these happen in createGamePageAction.php, in this order*/
"INSERT INTO `games`(name, turn, active) VALUES ('[name chosen by user]', 'Player1', '0')"

"INSERT INTO `players`(name, turn, game_id, user_id) VALUES ('Player1', 1, $game_id, 0)"

"INSERT INTO `endzones`(seedCount, game_id, player_id) VALUES (0, $game_id, $player_id)"

/* $player_id is the id of the newly created player that was found through a SELECT query:*/
"INSERT INTO `pits` VALUES
(1,'pit1',4,$player_id),(2,'pit2',4,$player_id),(3,'pit3',4,$player_id),
(4,'pit4',4,$player_id),(5,'pit5',4,$player_id),(6,'pit6',4,$player_id);"

/* $game_id was retreived through a SELECT query after the game was created*/
"INSERT INTO `players_games`(player_id, game_id) VALUES ($player_id, $game_id)"

/* $user_id is the id of the user currently logged in, who is creating the game*/
"INSERT INTO `players`(name, turn, game_id, user_id) VALUES ('Player2', 0, $game_id, $user_id)"

?>

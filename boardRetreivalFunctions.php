<?php

//Function to retreive the name of the game from its game_id
function getGameName($game_id){
  $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
  if($mysqli->connect_errno){
  	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}
    if(!($stmt = $mysqli->prepare("SELECT games.name FROM games
      WHERE games.id = $game_id"))){
  		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
  	}
  	if(!$stmt->execute()){
  		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}
  	if(!$stmt->bind_result($game_name)){
  		echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}
  	while($stmt->fetch()){
    	return $game_name;
  	}
  	$stmt->close();

}//end of getGameName.php

 ?>

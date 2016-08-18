<?php
function setEndZone2($player_2_id){

  $mysqli = new mysqli("oniddb.cws.oregonstate.edu","minorc-db","kLdt7swbvLhRmjzu","minorc-db");
  if($mysqli->connect_errno){
  	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
  	}

    // EndZone2:
if(!($stmt = $mysqli->prepare("SELECT endzones.seedCount FROM endzones WHERE endzones.id=$player_2_id"))){
  echo "Prepare failed on player 2 endzone creation: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
  echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result( $seedCount)){
  echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
while($stmt->fetch()){

  echo "<div><div class='circle'>";
  if( $seedCount <= 24){

    echo "<span class='seeds color-four'><br>";
    if ($seedCount <= 20){
      echo "<br><span class='under-20'>";
    }
    if ($seedCount <= 12){
      echo "<br>";
    }
    if ($seedCount >= 20){
    echo "<span>";
  }
    $divCounter = 0;
    for ($i = 0; $i < $seedCount; $i++) {

    echo "<span> . </span>";
    $divCounter++;
    if ($divCounter === 4){
     $divCounter = 0;

     echo "<br>";
    }

    }

  }


if($seedCount >= 25 AND $seedCount <= 30){
 echo "<br><br>";
 echo "<span class='seeds color-ten'><span>&nbsp;";
 $divCounter = 0;
 for ($i = 0; $i < $seedCount; $i++) {

 echo "<span> . </span>";
 $divCounter++;
 if ($divCounter === 5){
  $divCounter = 0;
  echo "<br>&nbsp;";
 }
 }
}
  if ($seedCount > 30){
    echo "<br>";
    echo "<span class='seeds color-ten'>";
    echo "<span class='over-thirty'>";
  $divCounter = 0;
  for ($i = 0; $i < $seedCount; $i++) {

    echo "<span>.</span>";

    $divCounter++;
    if ($divCounter === 7){
      $divCounter = 0;
      echo "<br> ";
    }
  }
}

  echo "</span></span></div></div>";
}

$stmt->close();
}
?>

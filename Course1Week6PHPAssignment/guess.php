<!DOCTYPE html>
<html lang="en">
<head>
    <title>Anthony Barretti</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Welcome to my guessing game</h1>

<p>
<?php

    $correctAnswer = 31;

    if( !isset($_GET['guess']) ) {
        echo "Missing guess parameter";
    }
    elseif( strlen($_GET['guess']) < 1 ) {
        echo "Your guess is too short";
    }
    elseif( preg_match("/^[^0-9]+$/",$_GET['guess']) ) {
        echo "Your guess is not a number";
    }
    elseif( $_GET['guess'] < $correctAnswer ) {
        echo "Your guess is too low";
    }
    elseif( $_GET['guess'] > $correctAnswer ) {
        echo "Your guess is too high";
    }
    elseif( $_GET['guess'] == $correctAnswer ) {
        echo "Congratulations - You are right";
    }
    
?>
</p>
</body>
</html>
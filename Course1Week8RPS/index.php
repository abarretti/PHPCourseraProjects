<?php
session_start();

if ( isset($_SESSION["name"]) ) {
  // Redirects the browser to game.php if the user is already logged in
  header("Location: game.php?name=".urlencode($_SESSION['name']));
  return;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti - Rock Paper Scissors</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to Rock Paper Scissors</h1>
<p>
<a href="login.php">Please Log In</a>
</p>
<p>
Attempt to go to
<a href="game.php">game.php</a> without logging in - it should fail with an error message.
</div>
</body>

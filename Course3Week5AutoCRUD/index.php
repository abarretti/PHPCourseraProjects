<?php
session_start();

if ( isset($_SESSION["name"]) ) {
  // Redirects the browser to game.php if the user is already logged in
  header("Location: view.php");
  return;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti - Autos Database</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to Autos Database</h1>
<p>
<a href="login.php">Please log in</a>
</p>
<p>
Attempt to go to
<a href="view.php">view.php</a> without logging in - it should fail with an error message.
</p>
<p>
Attempt to go to
<a href="add.php">add.php</a> without logging in - it should fail with an error message.
</p>
<p>
Attempt to go to
<a href="edit.php">edit.php</a> without logging in - it should fail with an error message.
</p>
<p>
Attempt to go to
<a href="delete.php">delete.php</a> without logging in - it should fail with an error message.
</p>
</div>
</body>

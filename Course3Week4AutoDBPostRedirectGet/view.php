<?php
require_once "pdo.php";
session_start();

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('User not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['addNew']) ) {
    header('Location: add.php');
    return;
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti's Automobile Tracker</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Tracking Autos for <?php echo $_SESSION["name"] ?></h1>
<h2>Automobiles</h2>
<?php
if( isset($_SESSION["success"]) ) {
    echo '<font color="green">'.$_SESSION["success"].'</font>';
    unset($_SESSION["success"]);
}
?>
<ul>
<?php
  $stmt = $pdo->query("SELECT make, year, mileage FROM autos");
  while ($row = $stmt->fetch()) {
    echo '<li>'.$row['year'].' '.$row['make'].' / '.$row['mileage'].'</li>';
  }
  $pdo = null;
?>
</ul>
<p>
<a href="add.php">Add New</a> |
<a href="logout.php">Logout</a>
</p>
</div>
</body>
</html>

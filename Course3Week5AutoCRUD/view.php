<?php
require_once "pdo.php";
session_start();

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('User not logged in');
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

if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}
?>
<ul>
<?php
  $stmt = $pdo->query("SELECT * FROM autos");
  if ($stmt->rowCount() == 0) {
    echo 'No rows found';
  }
  else {
  echo '<table border="1">
  <tr>
    <th>Make</th>
    <th>Model</th>
    <th>Year</th>
    <th>Mileage</th>
    <th>Action</th>
  </tr>';
    while ($row = $stmt->fetch()) {
      echo '<tr>
        <td>'.htmlentities($row['make']).'</td>
        <td>'.htmlentities($row['model']).'</td>
        <td>'.htmlentities($row['year']).'</td>
        <td>'.htmlentities($row['mileage']).'</td>
        <td><a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> /
        <a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a></td>
      </tr>';
    }
    echo '</table>';
  }
  $pdo = null;
?>
</ul>
<p>
<a href="add.php">Add New Entry</a><br><br>
<a href="logout.php">Logout</a>
</p>
</div>
</body>
</html>

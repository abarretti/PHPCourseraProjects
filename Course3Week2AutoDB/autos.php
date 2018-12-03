<?php
require_once "pdo.php";
session_start();

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1 ) {
    die('Name parameter missing');
}

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('User not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    session_destroy();
    header('Location: index.php');
    return;
}

// Prepare Array for insert into Database
if( ! isset($_SESSION['autoArray']) ) {
  $_SESSION['autoArray'] = [];
}

if ( isset($_POST["add"]) ) {

  $make = htmlentities($_POST['make']);
  $year = htmlentities($_POST['year']);
  $mileage = htmlentities($_POST['mileage']);

  if ( empty($make) ) {
    $message = 'Make is required';
  }
  elseif ( empty($year) || empty($mileage) ) {
    $message = 'Year and Mileage are required';
  }
  elseif ( !is_numeric($year) || !is_numeric($mileage) ) {
    $message = 'Mileage and year must be numeric';
  }
  elseif( $mileage < 0 ) {
    $message = 'Mileage cannot be less than 0';
  }
  else {
    $_SESSION['autoArray'][] = ['Make' => $make, 'Year' => $year, 'Mileage' => $mileage];
    $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:make, :year, :mileage)");
    $stmt->bindParam(':make', $make);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':mileage', $mileage);
    $stmt->execute();
    $pdo = null;

    $message = '<font color="green">Record inserted</font>';
  }
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
<?php
if( isset($message) ) {
  if ($message == "Record inserted") {
    echo '<font color="green">'.$message.'</font>';
  }
  else {
    echo '<font color="red">'.$message.'</font>';
  }
}
?>
<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
Make:
<input type="text" name="make"><br><br>
Year:
<input type="text" name="year"><br><br>
Mileage:
<input type="text" name="mileage"><br><br>
<input type="submit" name="add" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<ul>
<?php
  $pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc', 'anthony', 'lamborghini');
  $stmt = $pdo->query("SELECT make, year, mileage FROM autos");
  while ($row = $stmt->fetch()) {
    echo '<li>'.$row['year'].' '.$row['make'].' / '.$row['mileage'].'</li>';
  }
  $pdo = null;
?>
</ul>
</div>
</body>
</html>

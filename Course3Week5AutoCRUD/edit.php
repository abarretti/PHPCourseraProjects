<?php
require_once "pdo.php";
session_start();

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('User not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

if ( isset($_POST["edit"]) ) {

  $autos_id = $_POST['autos_id'];
  $make = $_POST['make'];
  $model = $_POST['model'];
  $year = $_POST['year'];
  $mileage = $_POST['mileage'];

  if ( empty($make) ) {
    $_SESSION["error"] = 'Make is required';
    header("Location: edit.php?autos_id=".$_POST['autos_id']);
    return;
  }
  elseif ( empty($model)) {
    $_SESSION["error"] = 'Model is required';
    header("Location: edit.php?autos_id=".$_POST['autos_id']);
    return;
  }
  elseif ( empty($year) || empty($mileage) ) {
    $_SESSION["error"] = 'Year and Mileage are required';
    header("Location: edit.php?autos_id=".$_POST['autos_id']);
    return;
  }
  elseif ( !is_numeric($year) || !is_numeric($mileage) ) {
    $_SESSION["error"] = 'Mileage and year must be numeric';
    header("Location: edit.php?autos_id=".$_POST['autos_id']);
    return;
  }
  elseif( $mileage < 0 ) {
    $_SESSION["error"] = 'Mileage cannot be less than 0';
    header("Location: edit.php?autos_id=".$_POST['autos_id']);
    return;
  }
  else {
    $stmt = $pdo->prepare("UPDATE autos SET make = :make, model = :model, year = :year, mileage = :mileage WHERE autos_id = :autos_id");
    $stmt->bindParam(':autos_id', $_POST['autos_id']);
    $stmt->bindParam(':make', $_POST['make']);
    $stmt->bindParam(':model', $_POST['model']);
    $stmt->bindParam(':year', $_POST['year']);
    $stmt->bindParam(':mileage', $_POST['mileage']);
    $stmt->execute();

    $_SESSION["success"] = "Record updated.";
    header("Location: view.php");
    return;
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
<h1>Editing Automobile</h1>

<?php

if ( ! isset($_GET['autos_id']) ) {
    $_SESSION['error'] = 'Missing autos_id';
    header('Location: view.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :autos_id");
$stmt->execute([":autos_id" => $_GET['autos_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header( 'Location: view.php' ) ;
    return;
}

if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}

$autos_id = $row['autos_id'];
$make = htmlentities($row['make']);
$model = htmlentities($row['model']);
$year = htmlentities($row['year']);
$mileage = htmlentities($row['mileage']);
?>

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="autos_id" value="<?php echo $autos_id; ?>">
Make:
<input type="text" name="make" value="<?php echo $make; ?>"><br><br>
Model:
<input type="text" name="model" value="<?php echo $model; ?>"><br><br>
Year:
<input type="text" name="year" value="<?php echo $year; ?>"><br><br>
Mileage:
<input type="text" name="mileage" value="<?php echo $mileage; ?>"><br><br>
<input type="submit" name="edit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

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

// Prepare Array for insert into Database
if( ! isset($_SESSION['autoArray']) ) {
  $_SESSION['autoArray'] = [];
}

if ( isset($_POST["add"]) ) {

  $make = htmlentities($_POST['make']);
  $year = htmlentities($_POST['year']);
  $mileage = htmlentities($_POST['mileage']);

  if ( empty($make) ) {
    $_SESSION["error"] = 'Make is required';
    header("Location: add.php");
    return;
  }
  elseif ( empty($year) || empty($mileage) ) {
    $_SESSION["error"] = 'Year and Mileage are required';
    header("Location: add.php");
    return;
  }
  elseif ( !is_numeric($year) || !is_numeric($mileage) ) {
    $_SESSION["error"] = 'Mileage and year must be numeric';
    header("Location: add.php");
    return;
  }
  elseif( $mileage < 0 ) {
    $_SESSION["error"] = 'Mileage cannot be less than 0';
    header("Location: add.php");
    return;
  }
  else {
    $_SESSION['autoArray'][] = ['Make' => $make, 'Year' => $year, 'Mileage' => $mileage];
    $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:make, :year, :mileage)");
    $stmt->bindParam(':make', $make);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':mileage', $mileage);
    $stmt->execute();
    $pdo = null;

    $_SESSION["success"] = "Record inserted";
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
<h1>Tracking Autos for <?php echo $_SESSION["name"] ?></h1>

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

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
Make:
<input type="text" name="make"><br><br>
Year:
<input type="text" name="year"><br><br>
Mileage:
<input type="text" name="mileage"><br><br>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

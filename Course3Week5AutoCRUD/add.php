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

if ( isset($_POST["add"]) ) {

  $make = $_POST['make'];
  $model = $_POST['model'];
  $year = $_POST['year'];
  $mileage = $_POST['mileage'];

  if ( empty($make) || empty($model) || empty($year) || empty($mileage) ) {
    $_SESSION["error"] = 'All values are required';
    header("Location: add.php");
    return;
  }

  if ( empty($make) ) {
    $_SESSION["error"] = 'Make is required';
    header("Location: add.php");
    return;
  }
  elseif ( empty($model)) {
    $_SESSION["error"] = 'Model is required';
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
    $stmt = $pdo->prepare("INSERT INTO autos (make, model, year, mileage) VALUES (:make, :model, :year, :mileage)");
    $stmt->bindParam(':make', $make);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':mileage', $mileage);
    $stmt->execute();

    $_SESSION["success"] = "Record added.";
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
if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}
?>

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
Make:
<input type="text" name="make"><br><br>
Model:
<input type="text" name="model"><br><br>
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

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

if ( isset($_POST["delete"]) ) {

  $stmt = $pdo->prepare("DELETE FROM autos WHERE autos_id = :autos_id");
  $stmt->execute([':autos_id' => $_POST['autos_id']]);

  $_SESSION["success"] = "Record deleted.";
  header("Location: view.php");
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
<h1>Deleting Automobile</h1>

<?php

if ( ! isset($_GET['autos_id']) ) {
    $_SESSION['error'] = 'Missing autos_id';
    header('Location: view.php');
    return;
}

$stmt = $pdo->prepare("SELECT autos_id, make, model, year FROM autos WHERE autos_id = :autos_id");
$stmt->execute([":autos_id" => $_GET['autos_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for autos_id';
    header( 'Location: view.php' ) ;
    return;
}

$autos_id = $row['autos_id'];
$make = htmlentities($row['make']);
$model = htmlentities($row['model']);
$year = htmlentities($row['year']);
echo '<h2>Delete record for '.$year.' '.$make.' '.$model.'?</h2>';
?>

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="autos_id" value="<?php echo $autos_id; ?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

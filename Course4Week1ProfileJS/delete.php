<?php
require_once "pdo.php";
session_start();

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('User not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST["delete"]) ) {

  $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :profile_id");
  $stmt->execute([':profile_id' => $_POST['profile_id']]);
  $_SESSION["success"] = "Record deleted.";
  header("Location: index.php");
  return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti's Resume Registry</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Deleting Profile</h1>

<?php

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name
  FROM Profile
  WHERE profile_id = :profile_id");
$stmt->execute([":profile_id" => $_GET['profile_id']]);
$row = $stmt->fetch();
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
$profile_id = $row['profile_id'];
$firstName = htmlentities($row['first_name']);
$lastName = htmlentities($row['last_name']);
?>

First Name: <?php echo $firstName; ?> <br><br>
Last Name: <?php echo $lastName; ?> <br><br>
<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

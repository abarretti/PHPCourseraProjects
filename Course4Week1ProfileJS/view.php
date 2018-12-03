<?php
require_once "pdo.php";
session_start();

?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti's Resume Registry</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Profile Information</h1>
<?php

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :profile_id");
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
$eMail = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
?>

First Name: <?php echo $firstName; ?>
<br><br>
Last Name: <?php echo $lastName; ?>
<br><br>
E-Mail: <?php echo $eMail; ?>
<br><br>
Headline: <?php echo $headline; ?>
<br><br>
Summary:
<br>
<?php echo $summary; ?>
<br><br>
<a href="index.php">Done</a>
</div>
</body>
</html>

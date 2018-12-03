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

$stmt = $pdo->prepare("SELECT *
  FROM Profile pr
  LEFT JOIN Position po ON pr.profile_id = po.profile_id
  WHERE pr.profile_id = :profile_id");
$stmt->execute([":profile_id" => $_GET['profile_id']]);
$rows = $stmt->fetchAll();
$positionArray = [];
foreach( $rows as $row ) {
  $profile_id = $row['profile_id'];
  $firstName = htmlentities($row['first_name']);
  $lastName = htmlentities($row['last_name']);
  $eMail = htmlentities($row['email']);
  $headline = htmlentities($row['headline']);
  $summary = htmlentities($row['summary']);

  if( isset($row['year']) && isset($row['description']) ) {
    $positionArray[] = ['year' => $row['year'], 'description' => $row['description'] ];
  }

}

if ( $rows === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

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
Position:
<ul>
<?php
foreach($positionArray as $position) {
  echo '<li>'.$position['year'].': '.$position['description'].'</li>';
}
?>
</ul>
<a href="index.php">Done</a>
</div>
</body>
</html>

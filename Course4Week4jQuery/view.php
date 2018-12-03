<?php
require_once "pdo.php";
session_start();

?>
<!DOCTYPE html>
<html>
<head>
<title>Anthony Barretti's Resume Registry</title>
<?php
require_once "bootstrap.php";
require_once "head.php";
?>
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

$stmt = $pdo->prepare("SELECT pr.profile_id, first_name,
  last_name, email, headline, summary, po.rank as posRank,
  po.year as posYear, description, e.rank as eduRank,
  e.year as eduYear, i.name as eduName
  FROM Profile pr
  LEFT JOIN Position po ON pr.profile_id = po.profile_id
  LEFT JOIN Education e ON pr.profile_id = e.profile_id
  LEFT JOIN Institution i ON e.institution_id = i.institution_id
  WHERE pr.profile_id = :profile_id
  ORDER BY posRank, eduRank");
$stmt->execute([":profile_id" => $_GET['profile_id']]);
$rows = $stmt->fetchAll();
$eduArray = [];
$posArray = [];
$eduCount = 1;
$posCount = 1;
foreach( $rows as $row ) {
  $profile_id = $row['profile_id'];
  $firstName = htmlentities($row['first_name']);
  $lastName = htmlentities($row['last_name']);
  $eMail = htmlentities($row['email']);
  $headline = htmlentities($row['headline']);
  $summary = htmlentities($row['summary']);

  if( $row['posRank'] == $posCount ) {
    $posArray[] = ['posYear' => $row['posYear'], 'description' => $row['description'] ];
    $posCount += 1;
  }

  if( $row['eduRank'] == $eduCount) {
    $eduArray[] = ['eduYear' => $row['eduYear'], 'eduName' => $row['eduName'] ];
    $eduCount += 1;
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
Education:
<ul>
<?php
foreach($eduArray as $edu) {
  echo '<li>'.$edu['eduYear'].': '.$edu['eduName'].'</li>';
}
?>
</ul>
Position:
<ul>
<?php
foreach($posArray as $pos) {
  echo '<li>'.$pos['posYear'].': '.$pos['description'].'</li>';
}
?>
</ul>
<a href="index.php">Done</a>
</div>
</body>
</html>

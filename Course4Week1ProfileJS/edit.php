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

if ( isset($_POST["save"]) ) {

  $profile_id = $_POST['profile_id'];
  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $eMail = $_POST['email'];
  $headline = $_POST['headline'];
  $summary = $_POST['summary'];

  if ( empty($firstName) || empty($lastName) || empty($eMail) || empty($headline) || empty($summary) ) {
    $_SESSION["error"] = 'All values are required';
    header("Location: edit.php?profile_id=".$_POST['profile_id']);
    return;
  }
  elseif ( strpos($eMail, '@') === false ) {
    $_SESSION["error"] = 'Invalid E-Mail Address';
    header("Location: edit.php?profile_id=".$_POST['profile_id']);
    return;
  }
  else {
    $stmt = $pdo->prepare("UPDATE Profile
      SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline, summary = :summary
      WHERE profile_id = :profile_id AND user_id = :user_id");
    $stmt->execute([
      ':user_id' => $_SESSION["user_id"],
      ':profile_id' => $profile_id,
      ':first_name' => $firstName,
      ':last_name' => $lastName,
      ':email' => $eMail,
      ':headline' => $headline,
      ':summary' => $summary
    ]);
    $_SESSION["success"] = "Record updated.";
    header("Location: index.php");
    return;
  }
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
<h1>Adding Profile for <?php echo $_SESSION["name"] ?></h1>
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

if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}

$profile_id = $row['profile_id'];
$firstName = htmlentities($row['first_name']);
$lastName = htmlentities($row['last_name']);
$eMail = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
?>

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
First Name:
<input type="text" name="first_name" size="60" value="<?php echo $firstName; ?>"><br><br>
Last Name:
<input type="text" name="last_name" size="60" value="<?php echo $lastName; ?>"><br><br>
E-Mail:
<input type="text" name="email" size="30" value="<?php echo $eMail; ?>"><br><br>
Headline:
<input type="text" name="headline" size="80" value="<?php echo $headline; ?>"><br><br>
Summary:
<textarea name="summary" rows="8" cols="80"><?php echo $summary; ?></textarea><br><br>
<input type="submit" name="save" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

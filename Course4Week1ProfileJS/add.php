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

if ( isset($_POST["add"]) ) {

  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $eMail = $_POST['email'];
  $headline = $_POST['headline'];
  $summary = $_POST['summary'];

  if ( empty($firstName) || empty($lastName) || empty($eMail) || empty($headline) || empty($summary) ) {
    $_SESSION["error"] = 'All values are required';
    header("Location: add.php");
    return;
  }
  elseif ( strpos($eMail, '@') === false ) {
    $_SESSION["error"] = 'Invalid E-Mail Address';
    header("Location: add.php");
    return;
  }
  else {
    $stmt = $pdo->prepare("INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
    VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)");
    $stmt->execute([
        ':user_id' => $_SESSION["user_id"],
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':email' => $eMail,
        ':headline' => $headline,
        ':summary' => $summary
    ]);
    $_SESSION["success"] = "Profile added";
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
if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}
?>

<form method="post" action=" <?php $_SERVER['PHP_SELF']; ?>">
First Name:
<input type="text" name="first_name" size="60"><br><br>
Last Name:
<input type="text" name="last_name" size="60"><br><br>
E-Mail:
<input type="text" name="email" size="30"><br><br>
Headline:
<input type="text" name="headline" size="80"><br><br>
Summary:
<textarea name="summary" rows="8" cols="80"></textarea><br><br>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>

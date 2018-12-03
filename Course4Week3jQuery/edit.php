<?php
require_once "pdo.php";
require_once "util.php";
session_start();

// Checks if the user logged in properly
if ( ! isset( $_SESSION["name"] ) ) {
    die('ACCESS DENIED');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST["save"]) ) {

  $profileErrMsg = validateProfile();
  $posErrMsg = validatePos();
  if ( is_string($profileErrMsg) || is_string($posErrMsg) ) {
    $_SESSION["error"] = is_string($profileErrMsg) ? $profileErrMsg : $posErrMsg;
    header("Location: edit.php?profile_id=".$_POST['profile_id']);
    return;
  }

  else {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $_POST['profile_id']]);

    $stmt = $pdo->prepare("INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
    VALUES (:user_id, :first_name, :last_name, :email, :headline, :summary)");
    $stmt->execute([
        ':user_id' => $_SESSION["user_id"],
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary']
    ]);

    $profile_id = $pdo->lastInsertId();

    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;

      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
      $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');

      $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':desc' => $desc)
      );

      $rank++;
    }

    $_SESSION["success"] = "Profile updated";
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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

$stmt = $pdo->prepare("SELECT *
  FROM Profile pr
  LEFT JOIN Position po ON pr.profile_id = po.profile_id
  WHERE pr.profile_id = :profile_id");
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
Position:
<input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
<input type="submit" name="save" value="Save">
<input type="submit" name="cancel" value="Cancel">
</form>
<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</div>
</body>
</html>

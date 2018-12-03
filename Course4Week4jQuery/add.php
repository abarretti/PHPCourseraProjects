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

if ( isset($_POST["add"]) ) {

  $profileErrMsg = validateProfile();
  $eduErrMsg = validateEdu();
  $posErrMsg = validatePos();
  if ( is_string($profileErrMsg) || is_string($posErrMsg) || is_string($eduErrMsg) ) {
    $_SESSION["error"] = (is_string($profileErrMsg) ? $profileErrMsg : (is_string($posErrMsg) ? $posErrMsg : $eduErrMsg));
    header("Location: add.php");
    return;
  }

  else {
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
      if ( ! isset($_POST['pos_year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;

      $year = $_POST['pos_year'.$i];
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

    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;

      $year = $_POST['edu_year'.$i];
      $school = $_POST['edu_school'.$i];

      //check if School is already in Institution table
      $stmt = $pdo->prepare("SELECT institution_id
        FROM Institution
        WHERE name = :school");
      $stmt->execute([":school" => $school]);
      $row = $stmt->fetch();
      if ( $row === false ) {
        $stmt = $pdo->prepare('INSERT INTO Institution (name)
          VALUES (:school)');
          $stmt->execute([":school" => $school]);
          $institution_id = $pdo->lastInsertId();
      } else {
        $institution_id = $row['institution_id'];
      }

      $stmt = $pdo->prepare('INSERT INTO Education
        (profile_id, institution_id, rank, year)
        VALUES (:pid, :institution_id, :rank, :year)');

      $stmt->execute([
      ':pid' => $profile_id,
      ':institution_id' => $institution_id,
      ':rank' => $rank,
      ':year' => $year]
      );
      $rank++;
    }

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
<?php
require_once "bootstrap.php";
require_once "head.php";
?>
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
Education:
<input type="submit" id="addEdu" value="+">
<div id="education_fields">
</div>
Position:
<input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
<script>
countEdu = 0;
//Education JS
$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addEdu').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine schools entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding school "+countEdu);
        $('#education_fields').append(
            '<div id="school'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#school'+countEdu+'\').remove();return false;"></p> \
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </div>');

        $('.school').autocomplete({
            source: "school.php"
        });
    });
});

countPos = 0;
$(document).ready(function(){
    window.console && console.log('Document ready called');
//Position JS
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
            <p>Year: <input type="text" name="pos_year'+countPos+'" value="" /> \
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

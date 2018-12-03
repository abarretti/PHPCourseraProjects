<?php // Do not put any HTML above this line
require_once "pdo.php";
session_start();

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

if ( isset($_SESSION["name"]) ) {
  // Redirects the browser to index.php if the user is already logged in
  header("Location: index.php");
  return;
}

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
  $salt = 'XyZzy12*_';
  $check = hash('md5', $salt . $_POST['pass']);

  $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = :email AND password = :password");
  $stmt->execute([':email' => $_POST['email'], ':password' => $check]);
  if ($stmt->rowCount() == 0) {
    $_SESSION["error"] = "Incorrect password";
    error_log("Login fail ".$_POST['email']." ".$check);
    header("Location: login.php");
    return;
  } else {
      $row = $stmt->fetch();
      $_SESSION["name"] = htmlentities($row['name']);
      $_SESSION["user_id"] = $row['user_id'];
      $_SESSION["success"] = "Login Succesful";
      error_log("Login success ".$_POST['email']);
      header("Location: index.php");
      return;
  }
}

// Fall through into the View

if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font>';
    unset($_SESSION["error"]);
}

?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Anthony Barretti's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
<label for="nam">User Name</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</div>
</body>

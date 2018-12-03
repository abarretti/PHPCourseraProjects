<?php // Do not put any HTML above this line
session_start();

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

if ( isset($_SESSION["name"]) ) {
  // Redirects the browser to autos.php if the user is already logged in
  header("Location: autos.php?name=".urlencode($_SESSION['name']));
  return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123
$failure = false;  // If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['who']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "Email and password are required";
    }
    if ( ! filter_var($_POST['who'], FILTER_VALIDATE_EMAIL)) {
        $failure = "Email must have an at-sign (@)";
    }

    else {
        $check = hash('md5', $salt . htmlentities($_POST['pass']));
        if ( $check == $stored_hash ) {
            // Redirect the browser to autos.php
            $_SESSION["name"] = htmlentities($_POST['who']);
            error_log("Login success ".$_POST['who']);
            header("Location: autos.php?name=".urlencode($_POST['who']));
            return;
        } else {
            $failure = "Incorrect password";
            error_log("Login fail ".$_POST['who']." ".$check);
        }
    }
}

// Fall through into the View
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
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( $failure !== false ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
}
?>
<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
<label for="nam">User Name</label>
<input type="text" name="who" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
</div>
</body>

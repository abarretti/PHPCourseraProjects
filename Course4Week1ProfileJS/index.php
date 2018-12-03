<?php
require_once "pdo.php";
session_start();

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
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
<h1>Anthony Barretti's Resume Registry</h1>

<?php
if( isset($_SESSION["success"]) ) {
    echo '<font color="green">'.$_SESSION["success"].'</font><br>';
    unset($_SESSION["success"]);
}

if( isset($_SESSION["error"]) ) {
    echo '<font color="red">'.$_SESSION["error"].'</font><br>';
    unset($_SESSION["error"]);
}

// Checks if the user logged in properly
if ( isset( $_SESSION["name"]) ) {
  echo '<a href="logout.php">Logout</a>';
} else {
  echo '<a href="login.php">Please log in</a>';
}

?>
<ul>
<?php
  $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile");
  if ($stmt->rowCount() == 0) {
    echo 'No rows found';
  }
  elseif ( !isset($_SESSION['name']) ) {
    echo '<table border="1">
    <tr>
      <th>Name</th>
      <th>Headline</th>
    </tr>';
      while ($row = $stmt->fetch()) {
        echo '<tr>
          <td><a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a></td>
          <td>'.htmlentities($row['headline']).'</td>
        </tr>';
      }
      echo '</table>';
  }
  else {
  echo '<table border="1">
  <tr>
    <th>Name</th>
    <th>Headline</th>
    <th>Action</th>
  </tr>';
    while ($row = $stmt->fetch()) {
      echo '<tr>
        <td><a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a></td>
        <td>'.htmlentities($row['headline']).'</td>
        <td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> /
        <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a></td>
      </tr>';
    }
    echo '</table>';
  }
  $pdo = null;
?>
</ul>
<p>
<?php
  if( isset($_SESSION['name'])) {
    echo '<a href="add.php">Add New Entry</a>';
  }
?>
</p>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>MH5 Password Cracker</title>
  <meta charset="UTF=8">
</head>
<body>

<h1>MD5 Cracker</h1>
<p>This application takes an MD5 has of a four digit PIN and checks all 10,000 possible four digit PINs to determine the PIN.</p>

<?php

function MD5PasswordCracker(string $hashedPW) {

  $time_pre = microtime(true);

  $counter = 0;
  $totalChecks = 0;
  $debugOutputString = "Debug Output: <br>";
  $correctPin = null;

  for($x = 0; $x <= 9 ; $x++) {
    for($y = 0; $y <= 9 ; $y++) {
      for($z = 0; $z <= 9 ; $z++) {
        for($a = 0; $a <= 9 ; $a++) {

          $testHashPW = hash( 'md5', (string)$x.$y.$z.$a );
          $testPW = (string)$x.$y.$z.$a;

          $counter += 1;

          if($counter <= 15 ) {
            $debugOutputString .= $testHashPW." ".$testPW;
            if( $counter != 15 ) {
              $debugOutputString .= "<br>";
            }
          }

          if( $testHashPW == $hashedPW ) {
            $correctPin = $testPW;
            $totalChecks = $counter;
          }

        }
      }
    }
  }

  if( !isset($correctPin) ) {
      $correctPin = "Not found";
      $totalChecks = $counter;
  }

  echo "<pre>".$debugOutputString."<br>";
  echo "Total Checks: ".$totalChecks."<br>";
  $time_post = microtime(true);
  $ellapsedTime = $time_post - $time_pre;
  echo "Ellapsed Time: ".$ellapsedTime."</pre>";

  echo "<p>PIN: ".$correctPin."</p>";
}//function end

if( isset($_GET['md5']) ) {
  MD5PasswordCracker($_GET['md5']);
}

?>

<form action= <?php echo $_SERVER['PHP_SELF'] ?> method="get">
  <input type="text" name="md5" size="40">
  <input type="submit" value="Crack MD5">
</form>

</body>
</html>

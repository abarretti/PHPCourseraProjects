<?php

function validateProfile() {
  if ( empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary']) ) {
    return 'All values are required';
  }
  if ( strpos($_POST['email'], '@') === false ) {
    return 'Invalid E-Mail Address';
  }
  return true;
}

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['pos_year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['pos_year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $year = $_POST['edu_year'.$i];
    $desc = $_POST['edu_school'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Education year must be numeric";
    }
  }
  return true;
}

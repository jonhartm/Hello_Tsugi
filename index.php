<?php
require_once "../config.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;

$LTI = LTIX::requireData();

// Handle the POST Data
$p = $CFG->dbprefix;
$target = Settings::linkGet('target');

if (isset($_POST['guess'])){ // Is POST set?
  if ($USER->instructor) { // It's an instructor
    if (isset($_POST['set'])) { // We're setting a new number
      Settings::linkSet('target', $_POST['guess']);
      $_SESSION['success'] = 'Target Updated - New Number: '.$_POST['guess'];
    }
  } else { // It's a student
    $message = '';
    if ($_POST['guess'] < $target) {
      $message = 'Higher...';
    } else if ($_POST['guess'] > $target) {
      $message = 'Lower...';
    } else {
      $message = 'Correct!';
    }
    $_SESSION['success'] = $message; // Tell the student how they did.
  }
  header( 'Location: '.addSession('index.php') ) ;
  return;
}

// Create the view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

echo('<form method="post">');
if ($USER->instructor) {
  echo('<p><label for="guess">Enter Number for Students to Guess:</label>');
  echo('<input type="text" name="guess" value=""><br/>');
  echo('<input type="submit" class="btn btn-normal" name="set" value="Set Guess">');
} else {
  echo('<p><label for="guess">Input Guess:</label>');
  echo('<input type="text" name="guess" value=""><br/>');
  echo('<input type="submit" class="btn btn-normal" name="set" value="Guess">');
}

echo('</form>');

$OUTPUT->footer();
?>

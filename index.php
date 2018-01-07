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
    $PDOX->queryDie("INSERT INTO {$p}helloTsugi
          (link_id, user_id, guesses, correct)
          VALUES ( :LI, :UI, 1, False)
          ON DUPLICATE KEY UPDATE guesses = guesses + 1",
          array(
              ':LI' => $LINK->id,
              ':UI' => $USER->id
          )
    );
    if ($_POST['guess'] < $target) {
      $message = 'Higher...';
    } else if ($_POST['guess'] > $target) {
      $message = 'Lower...';
    } else {
      $message = 'Correct!';
      // Set 'correct' to True for this user.
      $PDOX->queryDie("UPDATE {$p}helloTsugi
            SET correct = True
            WHERE link_id=:LI AND user_id=:UI",
            array(
                ':LI' => $LINK->id,
                ':UI' => $USER->id
            )
      );
    }
    $_SESSION['success'] = $message; // Tell the student how they did.
  }
  header( 'Location: '.addSession('index.php') ) ;
  return;
}

// Create rows for the instructor to see
if ( $USER->instructor ) {
    $rows = $PDOX->allRowsDie("SELECT
      (SELECT displayname FROM lti_user WHERE hellotsugi.user_id = lti_user.user_id) AS name,
      guesses, correct FROM {$p}helloTsugi
            WHERE link_id = :LI ORDER BY user_id",
            array(':LI' => $LINK->id)
    );
} else { // Get the status for this student
    $rows = $PDOX->rowDie("SELECT guesses, correct FROM {$p}helloTsugi
      WHERE link_id=:LI AND user_id=:UI",
      array(
          ':LI' => $LINK->id,
          ':UI' => $USER->id
      )
    );
}

// Create the view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

echo('<form method="post">');
if ($USER->instructor) {
  echo('Current Number: '.$target);
  echo('<p><label for="guess">Enter Number for Students to Guess:</label>');
  echo('<input type="text" name="guess" value=""><br/>');
  echo('<input type="submit" class="btn btn-normal" name="set" value="Set Number">');
} else {
  if ($rows['correct']){
    echo('You guessed the secret number '.$target.' in '.$rows['guesses'].' guesses!');
  } else {
    echo('Guesses so far: '.$rows['guesses']);
    echo('<p><label for="guess">Input Guess:</label>');
    echo('<input type="text" name="guess" value=""><br/>');
    echo('<input type="submit" class="btn btn-normal" name="set" value="Guess">');
  }

}
echo('</form>');

// Show the list of students who got it right
// Only appears if $rows != false
if ( $USER->instructor) {
    echo('<table border="1">'."\n");
    echo("<tr><th>User</th><th>Guesses</th><th>Correct</th></tr>\n");
    foreach ( $rows as $row ) {
        echo "<tr><td>";
        echo($row['name']);
        echo("</td><td>");
        echo($row['guesses']);
        echo("</td><td>");
        echo($row['correct']);
        echo("</td></tr>\n");
    }
    echo("</table>\n");
}

$OUTPUT->footer();
?>

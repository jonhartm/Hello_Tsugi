<?php
require_once "../config.php";

use \TSUGI\Core\LTIX;
use \Tsugi\UI\SettingsForm;

$LTI = \Tsugi\Core\LTIX::requireData(array('link_id'));

// Handle the POST Request
if ( SettingsForm::handleSettingsPost() ) {
    header('Location: '.addSession('index.php') ) ;
    return;
}

// Create the view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>

<p>I guess it's working</p>

<?php
$OUTPUT->footer();
?>

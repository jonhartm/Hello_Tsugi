<?php
require once "../config.php";

use \TSUGI\Core\LTIX;

$LAUNCH = LTIX::reqireData();

// Create the view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
?>
// Stuff
<p>I guess it's working</p>

<?php>
$OUTPUT->footer();
?>

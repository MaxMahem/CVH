<?php
/* get the data from the post */
$NSFWPost        = filter_input(INPUT_POST, 'NSFW');
$unvalidatedPost = filter_input(INPUT_POST, 'Unvalidated');

/* validate input */
$NSFW        = ($NSFWPost)        ? TRUE : FALSE;
$unvalidated = ($unvalidatedPost) ? TRUE : FALSE;

/* set session variables */
session_start();
$_SESSION['NSFW']        = $NSFW;
$_SESSION['unvalidated'] = $unvalidated;

$redirectURL = "http://" . $_SERVER['HTTP_HOST'] . '/CVH/settings/view.php';
header("Location: $redirectURL", 303);

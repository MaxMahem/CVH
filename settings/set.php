<?php

$NSFWPost        = filter_input(INPUT_POST, 'NSFW');
$unvalidatedPost = filter_input(INPUT_POST, 'Unvalidated');

$NSFW        = ($NSFWPost)        ? TRUE : FALSE;
$unvalidated = ($unvalidatedPost) ? TRUE : FALSE;

setcookie('NSFW',        $NSFW,        0, '/CVH');
setcookie('Unvalidated', $unvalidated, 0, '/CVH');

$redirectURL = "http://" . $_SERVER['HTTP_HOST'] . '/CVH/settings/view.php';
header("Location: $redirectURL", 303);
<?php

$NSFWPost        = filter_input(INPUT_POST, 'NSFW');
$unvalidatedPost = filter_input(INPUT_POST, 'Unvalidated');

setcookie('NSFW',        $NSFWPost,        0, '/CVH');
setcookie('Unvalidated', $unvalidatedPost, 0, '/CVH');

$redirectURL = "http://" . $_SERVER['HTTP_HOST'] . '/CVH/settings/view.php';
header("Location: $redirectURL", 303);
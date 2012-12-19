<?php

$NSFWPost        = filter_input(INPUT_POST, 'NSFW');
$unvalidatedPost = filter_input(INPUT_POST, 'Unvalidated');

setcookie('NSFW',        $NSFWPost);
setcookie('Unvalidated', $unvalidatedPost);

$redirectURL = "http://" . $_SERVER['HTTP_HOST'] . '/CVH/settings/view.php';
header("Location: $redirectURL", 303);
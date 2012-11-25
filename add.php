<?php

$filterConditions = array(
    'type'   => FILTER_SANITIZE_STRING,
    'NSFW'   => FILTER_SANITIZE_STRING,
    'text'   => FILTER_SANITIZE_STRING,
    'source' => FILTER_SANITIZE_STRING,
    'url'    => FILTER_SANITIZE_URL
);

$postData = filter_input_array(INPUT_POST, $filterConditions);

if (!empty($postData['type'])) { $type = $postData['type']; }
if (!empty($postData['NSFW'])) { $NSFW = $postData['NSFW']; }
if (!empty($postData['text'])) { $text = $postData['text']; }

?>

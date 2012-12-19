<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');

$filterConditions = array(
    'type'   => FILTER_SANITIZE_STRING,
    'NSFW'   => FILTER_SANITIZE_STRING,
    'text'   => FILTER_SANITIZE_STRING,
    'source' => FILTER_SANITIZE_STRING,
    'url'    => FILTER_SANITIZE_URL
);

$postData = filter_input_array(INPUT_POST, $filterConditions);

/** @todo: add more filter checking here */
if (!empty($postData['NSFW'])) { $NSFW = $postData['NSFW']; }
if (!empty($postData['text'])) { $text = $postData['text']; } 
    
if (!empty($postData['type'])) { 

    $card = new Card($postData['type']);
    $result = $card->addCard($postData['NSFW'], $postData['text'], $postData['source'], $postData['url']);

    if ($result) {
        $typeId[$card->getType()] = $card->getId(Card::HEX);
        $redirectDest = '/CVH/display/' . $typeId[Card::QUESTION] . '-' . $typeId[Card::ANSWER];
        $redirectURL = "http://" . $_SERVER['HTTP_HOST'] . $redirectDest;
        header("Location: $redirectURL", 303);
        die();
    }
}
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardPair.php');
    
/* get variables from post. Questions should actually be sent as either Q or A
 * immedialty followed by the id, IE A13. FILTER_SANITIZE_NUMBER_INT will get us
 * just the number. Makes things easier. */
$questionId = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_NUMBER_INT);
$answerId   = filter_input(INPUT_POST, 'answer',   FILTER_SANITIZE_NUMBER_INT);
    
/* check and make sure we got got post variables. 0 should NOT be a valid id, so
 * empty should work. */
if ((empty($questionId)) || (empty($answerId))) {
    throw new HttpHeaderException('Bad post string sent.');
}

$question = new Card(Card::QUESTION, $questionId);
$answer   = new Card(Card::ANSWER,   $answerId);

$pair = new CardPair($question, $answer);

/* submit a vote for the pair */
$result = $pair->vote();

/* check the return result, at this point I don't think it should be possible to
 * get anything besides true, but we'll leave the code in for later :) 
 * TODO: Add/set message for past results here/errors, should be a session variable */
if($result) {
    /* send back around for another vote */
    $redirectDest = '/CVH/';
} else {
    /* send back around for another vote anyways! */
    $redirectDest = '/CVH/';
}

/* Redirect. If everything went well we should be heading to /CVH otherwise /CVH/bad.php */
$redirectURL = "http://" . $_SERVER['HTTP_HOST'] . $redirectDest;
header('Location: ' . $redirectURL, 303);

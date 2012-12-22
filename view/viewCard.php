<?php
/**
 * /view/question.php display data on question card.
 */

/* contains the card class used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* filter_input is probably not necessary but we use it just to be safe */
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$id   = hexdec(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING));

/* create View for page */
$viewCard = new View('View' . ' '. ucfirst($type) . ' ' . 'Card');

/* check if we got some input */
if (empty($type) || empty($id)) {
    /** @todo: better error handling here */
    echo 'No input!';
    exit;
}

/* get the card */
$card = new Card($type);
$card->getCard($id);

if ($type == Card::QUESTION) { $setType = Card::ANSWER; }
if ($type == Card::ANSWER)   { $setType = Card::QUESTION; }

$topCards = new CardSet($setType);

$topCards->getTopCards($card, 5);

?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<?= $viewCard->displayHead(); ?>

<body>
    <div id="wrapper">
    
    <?= $viewCard->displayHeader(); ?>
    
    <div id="main">
	
    <section class="questions">
        <div class="cardbox">
            <?= $card->displayCard(); ?>
        </div>
    </section>
    
    This card has recived <?= $card->numVotes(); ?> votes.

    <div class="clear"></div>
    
    <?= $topCards->displayAllCards(); ?>
    
    <div class="clear"></div>
    
    </div> <!-- End of #main -->
    
    </div> <!-- End of #wrapper -->
    
    <?= $viewCard->displayFooter(); ?>  
</body>
</html>
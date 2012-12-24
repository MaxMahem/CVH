<?php
/**
 * /view/card.php display data on a card.
 */

/* contains the card class used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* filter_input is probably not necessary but we use it just to be safe */
$type  = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$getId = filter_input(INPUT_GET, 'id',   FILTER_SANITIZE_STRING);

$id = hexdec($getId);

/* create View for page */
$viewCard = new View('View' . ' '. ucfirst($type) . ' ' . 'Card');

/* check if we got some input */
if (empty($type) || empty($id)) {
    /** @todo: better error handling here */
    echo 'No input!';
    exit;
}

/* get the card */
$card = new Card($type, $id);

if ($type == Card::QUESTION) { $setType = Card::ANSWER; }
if ($type == Card::ANSWER)   { $setType = Card::QUESTION; }

$topCards = new CardSet($setType, $viewCard->NSFW, $viewCard->unvalidated);

$topCards->getTop($card, 5);

?>
<?= $viewCard->displayHead(); ?>

<div id="wrapper">
    
    <?= $viewCard->displayHeader(); ?>
    
    <div id="main">
	
    <section>
        <h1><?= ucfirst($card->getType()); ?></h1>
        <div class="cardbox">
            <?= $card->displayCard(); ?>
        </div>
    </section>
    
    This card has received <?= $card->numVotes(); ?> votes.

    <div class="clear"></div>
    
    <section>
        <h1>Top <?= ucfirst($topCards->getType() . 's'); ?></h1>
<?php foreach ($topCards as $topCard) { ?>
        <div class="cardbox">
            <?= $topCard->displayCard() ?>
        </div>
<?php } ?>
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $viewCard->displayFooter(); ?>
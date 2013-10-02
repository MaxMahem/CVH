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
$getId = filter_input(INPUT_GET, 'id',   FILTER_SANITIZE_NUMBER_INT);

$id = hexdec($getId);

/* create View for page */
$viewCard = new View('View' . ' '. ucfirst($type) . ' ' . 'Card');

/* check if we got some input */
if (empty($type) || empty($id)) {
    /** @todo: better error handling here */
    echo 'No input!';
    var_dump($_GET);
    exit;
}

/* get the card */
$card = new Card($type, $id);

/* get card date */
$cardAdded = strtotime($card->added);

/* Card set type needs to be the opposite of the primary card type */
$setType = ($type == Card::QUESTION) ? Card::ANSWER : Card::QUESTION;

$topCards = new CardSet($setType, $viewCard->NSFW, $viewCard->unvalidated);

$topCards->getTop($card, 5);

?>
<?= $viewCard->displayHead(); ?>

<div id="wrapper">
    
    <?= $viewCard->displayHeader(); ?>
    
    <div id="main">
	
    <section>
        <h2><?= ucfirst($card->type . 's'); ?></h2>
        <div class="cardbox">
            <?= $card->display(NULL); ?>
        </div>

        <article>
            <h2>Stats:</h2>
            This card has received <?= $card->numVotes(); ?> votes.<br>
            It was added on <time datetime="<?= date("c", $cardAdded); ?>"><?= date('F j, Y', $cardAdded); ?></time>
        </article>
    </section>

    <div class="clear"></div>
    
    <section>
        <h2>Top <?= ucfirst($topCards->cardType . 's'); ?></h2>
<?php foreach ($topCards as $topCard) { ?>
        <div class="cardbox">
            <?= $topCard->display(Card::LINK) ?>
        </div>
<?php } ?>
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $viewCard->displayFooter(); ?>
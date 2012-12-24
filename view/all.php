<?php
/**
 * /view/viewAll.php display all cards in the DB.
 */

/* contains the card class used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* filter_input is probably not necessary but we use it just to be safe */
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

/* check to see if we got a good type */
if (($type != Card::QUESTION) && ($type != Card::ANSWER)) {
    /** @todo better error handling
     *  @todo handle quesetion or answer plurals */
    echo "Bad Type: $type";
    exit;
}

/* create View for page */
$view = new View('View All' . ' '. ucfirst($type) . 's');
    
$cards = new CardSet($type, $view->NSFW, $view->unvalidated);
$cards->getAll();
    
?>
<?= $view->displayHead(); ?>

<div id="wrapper">
    
<?= $view->displayHeader(); ?>
    
<div id="main">
	
    <section class="<?=$cards->type; ?>">
        <h1><?= ucfirst($cards->type) . 's'; ?></h1>
<?php foreach ($cards as $card) { ?>
        <div class="cardbox">
            <?= $card->displayCard(Card::LINK); ?>
        </div>
<?php } ?>
    </section>

    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $view->displayFooter(); ?>
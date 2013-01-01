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
$typeGet = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$pageGet = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

$page = (empty($pageGet)) ? 0 : $pageGet;

/* check to see if we got a good type */
if (($typeGet != Card::QUESTION) && ($typeGet != Card::ANSWER)) {
    /** @todo better error handling
     *  @todo handle quesetion or answer plurals */
    echo "Bad Type: $type";
    exit;
} else {
    $type = $typeGet;
}

/* create View for page */
$viewAll = new View('View All' . ' '. ucfirst($type) . 's');
    
$cards = new CardSet($type, $viewAll->NSFW, $viewAll->unvalidated);
$cards->getAll($page);
    
?>
<?= $viewAll->displayHead(); ?>
    
<?= $viewAll->displayHeader(); ?>
	
<section class="<?=$cards->cardType . 's'; ?>">
    <h2><?= ucfirst($cards->cardType) . 's'; ?></h2>
<?php foreach ($cards as $card) { ?>
    <div class="cardbox">
        <?= $card->display(Card::LINK); ?>
    </div>
<?php } ?>
    
    <footer>
        <nav>
            <h3><?=  ucfirst($cards->cardType . 's'); ?> Navigation</h3>
            <a href="<?=$page - 1; ?>">&LT;&LT;</a> <?=$page; ?> <a href="<?=$page + 1; ?>">&GT;&GT;</a>
        </nav>
    </footer>
    
        
</section>
    
<?= $viewAll->displayFooter(); ?>
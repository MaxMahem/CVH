<?php
/**
 * /view/viewAll.php display all cards in the DB.
 */

/* contains the card class used to create the cards */
include_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/CardSet.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/Card.php');
/* contains the view class used for view elements. */
include_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/View.php');

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
$viewAll = new View('View All' . ' '. ucfirst($type) . 's');
    
$cards = new CardSet($type);
$cards->getAllCards();

$displayURL = "/CVH/view/$type/";
    
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<?= $viewAll->displayHead(); ?>

<body>
    <div id="wrapper">
    
    <?= $viewAll->displayHeader(); ?>
    
    <div id="main">
	
    <?= $cards->displayAllCards(); ?>

    <div class="clear"></div>
    
    </div> <!-- End of #main -->
    
    </div> <!-- End of #wrapper -->
    
    <?= $viewAll->displayFooter(); ?>  
</body>
</html>
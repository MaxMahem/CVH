<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$index = new View();

/* get Cookie for NSFW/Unvalidated Settings */
$NSFWCookie = filter_input(INPUT_COOKIE, 'NSFW');
// $unvalidatedCookie = filter_input(INPUT_COOKIE, 'Unvalidated');

$NSFW = ($NSFWCookie == 'true') ? TRUE : FALSE;
    
/* Get the question cards */
$question = new Card(Card::QUESTION, Card::RANDOM_CARD, $NSFW);

/* Get the Answer cards, currently we get 3 */
$answers = new CardSet(Card::ANSWER);
$answers->getRandom(3, $NSFW);

$permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .  $question->getID(Card::HEX) . "-";
$voteURL = "/CVH/vote/" .  $question->getId(Card::HEX) . "-";
?>
<?= $index->displayHead(); ?>

<div id="wrapper">
    
<?= $index->displayHeader(); ?>
    
<div id="main">
    
    <section class='instructions'>
        <p>Pick the card you like the best!</p>
        <p>Or <a href="/CVH">RELOAD</a> this page to get new questions.</p>
    </section>
    
    <section class="questions">
        <div class="cardbox">
            <?= $question->displayCard(); ?>
        </div>
    </section>
    
    <div class="clear"></div>

    <section class="answers">
<?php foreach ($answers as $answer) { ?>
        <div class="cardbox">
            <?= $answer->displayCard($voteURL . $answer->getId(Card::HEX)); ?>
            <div class="permalink"><a href="<?php echo $permURL . $answer->getId(Card::HEX); ?>" >Permalink</a></div>
        </div>
<?php } ?>
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $index->displayFooter(); ?>
<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/RandomCard.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$index = new View();

$seed= rand();

/* Get the question cards */
$question = new RandomCard(Card::QUESTION, $index->NSFW, $seed);

/* Get the Answer cards, currently we get 3 */
$answers = new CardSet(Card::ANSWER, $index->NSFW, $index->unvalidated);
$answers->getRandom(3, $seed);

$voteURL = "/CVH/vote/" . dechex($question->id) . "-";
?>
<?= $index->displayHead(); ?>
   
<?= $index->displayHeader(); ?>
    
<div id="main">
    
    <section class='instructions'>
        <h2>Instructions</h2>
        <p>Pick the card you like the best!</p>
    </section>
    
    <section class="questions">
        <h2>Questions</h2>
        <?= $question->display(NULL); ?>
        <a class='arrow' href='<?=$seed; ?>/Q2'>
            <svg viewBox="0 0 30 100" height="11.2em" width="3em">
                <polygon points="25,50 5,100, 5,0" />
            </svg>
        </a>
    </section>
    
    <div class="clear"></div>

    <section class="answers">
        <h2>Answers</h2>

        <ul>
<?php foreach ($answers as $answer) { ?>
            <li><?= $answer->display($voteURL . dechex($answer->id)); ?>
<?php } ?>
        </ul>
        
        <a class='arrow' href='<?=$seed; ?>/A2'>
            <svg viewBox="0 0 30 100" height="11.2em" width="3em">
                <polygon points="25,50 5,100, 5,0" />
            </svg>
        </a>
        
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
<?= $index->displayFooter(); ?>
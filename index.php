<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$index = new View();
    
/* Get the question cards */
$question = new Card(Card::QUESTION, Card::RANDOM_CARD, $index->NSFW);

/* Get the Answer cards, currently we get 3 */
$answers = new CardSet(Card::ANSWER, $index->NSFW);
$answers->getRandom(4);

$voteURL = "/CVH/vote/" .  $question->getId(Card::HEX) . "-";
?>
<?= $index->displayHead(); ?>

<div id="wrapper">
    
<?= $index->displayHeader(); ?>
    
<div id="main">
    
    <section class='instructions'>
        <h2>Instructions</h2>
        <p>Pick the card you like the best!</p>
        <p>Or <a href="/CVH">RELOAD</a> this page to get new questions.</p>
    </section>
    
    <section class="questions">
        <h2>Questions</h2>
        <div class="cardbox">
            <?= $question->display(NULL); ?>
        </div>
        <div class="cardbox">
            <article class='card question link'><h3>Reload</h3><a class='answerlink' href='.'>Get me a different question!</a></article>
        </div>
    </section>
    
    <div class="clear"></div>

    <section class="answers">
        <h2>Answers</h2>
<?php foreach ($answers as $answer) { ?>
        <div class="cardbox">
            <?= $answer->display($voteURL . $answer->getId(Card::HEX)); ?>
        </div>
<?php } ?>
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $index->displayFooter(); ?>
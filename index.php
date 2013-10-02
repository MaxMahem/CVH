<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* set variables as necessary */
$seed = rand();

/* create View for page */
$index = new View();

/* Get the question cards */
$questions = new CardSet(Card::QUESTION, $index->NSFW, $index->unvalidated);
$questions->getRandom(1, $seed);

/* get the question (there is only 1) */
foreach ($questions as $question) {}

/* get question ID */
$questionId = 'Q' . $question->id;

/* Get the Answer cards, currently we get 3 */
$answers   = new CardSet(Card::ANSWER,   $index->NSFW, $index->unvalidated);
$answers->getRandom(3, $seed);

$nextQuestion = '/CVH/view/card/random/question/S' . $seed . '/P1/N1';
$nextAnswers  = '/CVH/view/card/random/answer/S'   . $seed . '/P1/N3/' . $questionId;
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
        <a class='arrow' href='<?=$nextQuestion; ?>'>
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
            <li><?= $answer->display(Card::VOTE, $questionId); ?>
<?php } ?>
        </ul>
        
        <a class='arrow' href='<?=$nextAnswers; ?>'>
            <svg viewBox="0 0 30 100" height="11.2em" width="3em">
                <polygon points="25,50 5,100, 5,0" />
            </svg>
        </a>
        
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
<?= $index->displayFooter(); ?>
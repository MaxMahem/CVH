<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/RandomCard.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* get variables from page (if present) */
$seedGet         = filter_input(INPUT_GET, 'seed',         FILTER_SANITIZE_NUMBER_INT);
$questionPageGet = filter_input(INPUT_GET, 'questionPage', FILTER_SANITIZE_NUMBER_INT);
$answerPageGet   = filter_input(INPUT_GET, 'answerPage',   FILTER_SANITIZE_NUMBER_INT);

/* set variables as necessary */
$seed         = (empty($seedGet))         ? rand() : $seedGet;
$questionPage = (empty($questionPageGet)) ? 1      : $questionPageGet;
$answerPage   = (empty($answerPageGet))   ? 1      : $answerPageGet;

/* create View for page */
$index = new View();

/* Get the question cards */
$questions = new CardSet(Card::QUESTION, $index->NSFW, $index->unvalidated);
$questions->getRandom(1, $seed, $questionPage - 1);

foreach ($questions as $question) {}

/* Get the Answer cards, currently we get 3 */
$answers   = new CardSet(Card::ANSWER,   $index->NSFW, $index->unvalidated);
$answers->getRandom(3, $seed, $answerPage - 1);

$voteURL = "/CVH/vote/" . dechex($question->id) . "-";

$nextQuestion = '/CVH/R' . $seed . '/Q' . strval($questionPage + 1) . '/A' . strval($answerPage);
$nextAnswers  = '/CVH/R' . $seed . '/Q' . strval($questionPage)     . '/A' . strval($answerPage + 1);
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
            <li><?= $answer->display($voteURL . dechex($answer->id)); ?>
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
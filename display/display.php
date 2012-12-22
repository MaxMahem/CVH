<?php
    /* retrieve the get info from the url. Pages are sent in the format:
     * /CVH/display/QUESTIONID-ANSWERID, and transformed via the .htaccess to:
     * /CVH/display.php?Q=QUESTIONID&A=ANSWERID */

    /* filter_input is probably not necessary but we use it just to be safe */
    $questionId = hexdec(filter_input(INPUT_GET, 'Q', FILTER_SANITIZE_STRING));
    $answerId   = hexdec(filter_input(INPUT_GET, 'A', FILTER_SANITIZE_STRING));
    
    /* we got some variables, process them. */
    
    /* contains the card class used to create the cards */
    require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
    /* contains the view class used for view elements. */
    require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

    /* create View for page */
    $display = new View();
    
    /* check if the question id is set */
    if (!empty($questionId)) {
        /* get that card */
        $question = new Card(Card::QUESTION, $questionId);
    } else {
        /* Query random question */
        $question = new Card(Card::QUESTION, Card::RANDOM_CARD);
    }
            
    /* check if the answer id is empty */
    if (!empty($answerId)) {
        /* Query answer id */
        $answer = new Card(Card::ANSWER, $answerId);
    } else {
        /* Query random answer */
        $answer = new Card(Card::ANSWER, Card::RANDOM_CARD);
    }
    
    /* Query Vote Totals */
    $votes = Card::numVotes($question, $answer);

    if ($votes == 1) {
        $voteWord = 'vote.';
    } else {
        $voteWord = 'votes.';
    }
?>
<?= $display->displayHead(); ?>

<div id="wrapper">
    
<?= $display->displayHeader(); ?>
    
<div id="main">
	
    <div class="cardbox">
        <?= $question->displayCard(); ?>
    </div>
    
    <div class="instructions">
        <p>This combination has received <br />
        <span class="votes"><?= $votes; ?></span><br />
        <?php echo $voteWord . PHP_EOL; ?></p>
    </div>
    
    <div class="clear"></div>
    
    <div class="cardbox">
        <?= $answer->displayCard(); ?>
    </div>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $display->displayFooter(); ?>
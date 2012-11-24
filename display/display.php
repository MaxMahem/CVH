<?php
    /* retrieve the get info from the url. Pages are sent in the format:
     * /CVH/display/QUESTIONID-ANSWERID, and transformed via the .htaccess to:
     * /CVH/display.php?Q=QUESTIONID&A=ANSWERID */

    /* filter_input is probably not necessary but we use it just to be safe */
    $questionId = hexdec(filter_input(INPUT_GET, 'Q', FILTER_SANITIZE_STRING));
    $answerId   = hexdec(filter_input(INPUT_GET, 'A', FILTER_SANITIZE_STRING));
    
    /* we got some variables, process them. */
    
    /* the db-connection file is should define DBHOST, DBUSER, DBPASS, and DBNAME */
    include('../../../db-connection.php');
    include('../card.php');
    
    /* Connect to the DB. */
    $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

    /* check if the question id is set */
    if (!empty($questionId)) {
        /* get that card */
        $question = new Card($mysqlLink, Card::QUESTION, $questionId);
    } else {
        /* Query random question */
        $question = new Card($mysqlLink, Card::QUESTION, Card::RANDOM_CARD, TRUE);
    }
            
    /* check if the answer id is empty */
    if (!empty($answerId)) {
        /* Query answer id */
        $answer = new Card($mysqlLink, Card::ANSWER, $answerId);
    } else {
        /* Query random answer */
        $answer = new Card($mysqlLink, Card::ANSWER, Card::RANDOM_CARD, TRUE);
    }
    
    /* Query Vote Totals */
    $votes = Card::numVotes($question, $answer, $mysqlLink);

    if ($votes == 1) {
        $voteWord = 'vote.';
    } else {
        $voteWord = 'votes.';
    }
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<head>
    <meta charset="utf-8" />
    <title>Cards vs Humans - Display</title>
    <link rel="stylesheet" type="text/css" href="/CVH/cvh.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 
    <script type="text/javascript" src="/CVH/cvh.js"></script>
</head>

<body>
    <div id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </div>
	
    <div class="cardbox">
        <?= $question->displayCard(); ?>
    </div>
    
    <div class="instructions">
        <p>This combination has received <br />
        <span class="votes"><?php echo $votes; ?></span><br />
        <?php echo $voteWord . PHP_EOL; ?></p>
    </div>
    
    <div class="clear"></div>
    
    <div class="cardbox">
        <?= $answer->displayCard(); ?>
        <div class="permalink"><a href="<?= Card::permURL($question, $answer); ?>" >Permalink</a></div>
    </div>
</body>
</html>
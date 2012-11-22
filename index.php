
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<head>
    <meta charset="utf-8" />
    <title>Cards vs Humans</title>
    <link rel="stylesheet" type="text/css" href="cvh.css" />
</head>

<?php
    /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME 
     * with their appropriate values, and should be located outside of the webroort
     * which we assume is one level up */
    include('../../db-connection.php');
    
    /* contians the card class used to create the cards */
    include('card.php');
    
    /* TODO maybe add more error checking here, I don't like returning this info to the user though */
    $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if (!$mysqlLink) {
        echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
    }

    /* Get the question cards */
    $question = new Card($mysqlLink, Card::QUESTION, Card::RANDOM_CARD, TRUE, 1);
    
    /* Get the Answer cards, currently we get 3 */
    /* @todo: Consider a better way of getting multiple cards? 3 DB Calls is inefficent/don't work right */
    for ($i = 0; $i < 3; $i++) {
        $answers[] = new Card($mysqlLink, Card::ANSWER, Card::RANDOM_CARD, TRUE);
    }

    $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .  $question->getID(Card::HEX) . "-";
    $voteURL = "/CVH/vote/" .  $question->getId(Card::HEX) . "-";
?>

<body>
    <div id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </div>
    
    <?= $question->displayCard(); ?>
    
    <div class="instructions">
        Pick the card you like the best!
    </div>
    
    <div class="clear"></div>
    
<?php foreach ($answers as $answer) { ?>
    <?= $answer->displayCard($voteURL) . PHP_EOL; ?>
<?php } ?>
    
    <div class="card answer bad">
        <a class="answerlink" href="/CVH">THESE ALL SUCK!</a>
    </div>
    <div class="clear"></div>

<?php foreach ($answers as $answer) { ?>
    <div class="permalink">
        <p><a href="<?php echo $permURL . $answer->getId(Card::HEX); ?>" >Permalink</a></p>
    </div>
<?php } ?>
</body>
</html>
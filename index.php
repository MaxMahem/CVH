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
    $question = new Card(Card::QUESTION);
    $question->randomCard(TRUE);

    /* Get the Answer cards, currently we get 3 */
    /* @todo: Consider a better way of getting multiple cards? 3 DB Calls is inefficent/don't work right */
    for ($i = 0; $i < 3; $i++) {
        $answers[$i] = new Card(Card::ANSWER);
        $answers[$i]->randomCard(TRUE);
    }

    $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .  $question->getID(Card::HEX) . "-";
    $voteURL = "/CVH/vote/" .  $question->getId(Card::HEX) . "-";
?>

<body>
    <div id="wrapper">
    <header id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </header>
    
    <div id="main">
    
    <div class="cardbox">
        <?= $question->displayCard(); ?>
    </div>

    <div class="instructions">
        <p>Pick the card you like the best!</p>
        <p>Or <a href="/CVH">RELOAD</a> this page to get new questions.</p>
        <p>Or <a href="suggest.php">Suggest something better?</a></p>
    </div>
    
    <div class="clear"></div>

    <div class="answers">
<?php foreach ($answers as $answer) { ?>
        <div class="cardbox">
            <?= $answer->displayCard($voteURL) . PHP_EOL; ?>
        <div class="permalink"><a href="<?php echo $permURL . $answer->getId(Card::HEX); ?>" >Permalink</a></div>
    </div>
<?php } ?>
    </div>
    
    <div class="clear"></div>
    
    </div> <!-- End of #main -->
    
    </div> <!-- End of #wrapper -->
    <footer id="footer">
        Madeby: <a href="mailto:maxtmahem@gmail.com">Austin Stanley</a>
    </footer>
</body>
</html>
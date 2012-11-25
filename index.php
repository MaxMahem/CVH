<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <title>Cards vs Humans</title>
    <link rel="stylesheet" type="text/css" href="cvh.css" />
</head>

<?php
    /* contians the card class used to create the cards */
    include_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/card.php');

    /* Get the question cards */
    $question = new Card(Card::QUESTION);
    $question->randomCard(TRUE, 1);

    /* Get the Answer cards, currently we get 3 */
    /* @todo: Consider a better way of getting multiple cards? 3 DB Calls is inefficent/don't work right */
    for ($i = 0; $i < 3; $i++) {
        $answers[$i] = new Card(Card::ANSWER);
        $answers[$i]->randomCard(TRUE, 1);
    }

    $voteURL = "/CVH/vote/" .  $question->getId(Card::HEX) . "-";
?>

<body>
    <div id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </div>

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
        <div class="permalink"><a href="<?= Card::permURL($question, $answer); ?>" >Permalink</a></div>
    </div>
<?php } ?>
    </div>
</body>
</html>
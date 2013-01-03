<?php
/* contains the card classes used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');

/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* get variables from page (if present) */
$typeGet   = filter_input(INPUT_GET, 'type',   FILTER_SANITIZE_STRING);
$seedGet   = filter_input(INPUT_GET, 'seed',   FILTER_SANITIZE_NUMBER_INT);
$pageGet   = filter_input(INPUT_GET, 'page',   FILTER_SANITIZE_NUMBER_INT);
$numberGet = filter_input(INPUT_GET, 'number', FILTER_SANITIZE_NUMBER_INT);
$voteGet   = filter_input(INPUT_GET, 'vote',   FILTER_SANITIZE_STRING);

/* set variables as necessary */
$type = (empty($typeGet)) ? Card::QUESTION : $typeGet;
$seed = (empty($seedGet)) ? rand()         : $seedGet;
$page = (empty($pageGet)) ? 1              : $pageGet;
$vote = (empty($voteGet)) ? NULL           : $voteGet;

/* default number varies based on type, Questions get one, answers get 3 */
$defaultNumber = ($type == Card::QUESTION) ? 1 : 3;
$number = (empty($numberGet)) ? $defaultNumber : $numberGet;

/* create View for page */
$display = new View();

/* Get the cards */
$cards = new CardSet($type, $display->NSFW, $display->unvalidated);
$cards->getRandom($number, $seed, $page - 1);

$nextPage = $page + 1;
$nextURL  = "/CVH/view/random/$type/S$seed/P$nextPage/N$number";

$linkURL = (empty($vote)) ? NULL : "/CVH/vote/$vote-ID" ;
?>
<?= $display->displayHead(); ?>
   
<?= $display->displayHeader(); ?>
    
<section class="<?=$type . 's'; ?>">
    <h2><?=ucfirst($type . 's'); ?></h2>
    <ul>
<?php foreach ($cards as $card) { ?>
        <li><?= $card->display($linkURL); ?>
<?php } ?>
    </ul>
    <a class='arrow' href='<?=$nextURL; ?>'>
        <svg viewBox="0 0 30 100" height="11.2em" width="3em">
            <polygon points="25,50 5,100, 5,0" />
        </svg>
    </a>
</section>
    
<?= $display->displayFooter(); ?>
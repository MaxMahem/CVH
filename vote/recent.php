<?php
/* contains the card class used to create the cards */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/VoteSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$viewVotes = new View('View Recent Votes');
    
$voteCards = new VoteSet($viewVotes->NSFW, $viewVotes->unvalidated);
$voteCards->getAll();

// var_dump($voteCards);
?>
<?= $viewVotes->displayHead(); ?>
    
<?= $viewVotes->displayHeader(); ?>
	
<section class="cards">
    <h2>Most recent cards</h2>

<?php foreach ($voteCards as $cardPair) { ?>
    <section id="<?= $cardPair->id ?>" class="cardpair">
    <div class="cardbox">
            <?= $cardPair->display(Card::LINK); ?>
            Last Voted On: <?= $cardPair->added; ?></br>
            Votes: <?= $cardPair->getVotes(); ?>
        </div>
    </section>
    <div class="clear"></ div>
<?php } ?>
        
</section>

<?= $viewVotes->displayFooter(); ?>
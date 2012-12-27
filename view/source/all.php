<?php
/**
 * /view/viewAll.php display all cards in the DB.
 */

/* contains the card class used to create the sources */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/SourceSet.php');
/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$viewAll = new View('View All Sources');
    
$sources = new SourceSet();
$sources->getAll();
    
?>
<?= $viewAll->displayHead(); ?>

<div id="wrapper">
    
<?= $viewAll->displayHeader(); ?>
    
<div id="main">
	
    <section class="<?=$cards->type; ?>">
        <h1>Sources</h1>
        <ul>
<?php foreach ($sources as $source) { ?>
            <li><?php echo $source->display(Card::ANSWER); ?>
<?php } ?>
        </ul>
    </section>

    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $viewAll->displayFooter(); ?>
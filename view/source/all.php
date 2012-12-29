<?php
/**
 * /view/source/all.php display all sources in the DB.
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
	
    <section class="sources">
        <h2>Sources</h2>
        <ul>
        <?php foreach ($sources as $source) { ?>
            <li><?php echo $source->display(Card::ANSWER); ?>
                <details open>
                    <?php $source->getCards($viewAll->NSFW, $viewAll->unvalidated); ?>
                    <details>
                        <summary>Questions (<?=count($source->questionCards); ?>)</summary>
                        <?php 
                            foreach ($source->questionCards as $question) {
                                echo $question->display(Card::LINK);
                            }
                        ?>
                    </details>
                    <details>
                        <summary>Answers (<?=count($source->answerCards); ?>)</summary>
                        <?php 
                            foreach ($source->answerCards as $answer) {
                                echo $answer->display(Card::LINK);
                            }
                        ?>
                    </details>
                 </details>
        <?php } ?>
        </ul>
    </section>

    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $viewAll->displayFooter(); ?>
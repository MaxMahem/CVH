<?php
/* contains the view class used for view elements. */
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

/* create View for page */
$view = new View('Settings');

$NSFWChecked        = ($view->NSFW)        ? 'checked' : '';
$unvalidatedChecked = ($view->unvalidated) ? 'checked' : '';

?>    
<?= $view->displayHead(); ?>

<div id="wrapper">
    
<?= $view->displayHeader(); ?>
    
<div id="main">

    <fieldset>
    <legend>CVH Options</legend>
    <form action='set.php' method='post'>
        <label for='NSFWCheckbox'        title='Display NSFW Content'>
            <input id='NSFWCheckbox'        name='NSFW'        value='true' type='checkbox' <?=$NSFWChecked        ?>>NSFW Cards
        </label>
        <label for='UnvalidatedCheckbox' title='Display Unvalidated Cards'>
            <input id='UnvalidatedCheckbox' name='Unvalidated' value='true' type='checkbox' <?=$unvalidatedChecked ?>>Unvalidated Cards
        </label><br>
        <input type='submit' value='Apply'>
        
        To set these functions we use a cookie. If that bothers you, don't set them.
    </form>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $view->displayFooter(); ?>
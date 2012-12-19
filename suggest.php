<?php
    /* contains the view class used for view elements. */
    include_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/View.php');

    /* create View for page */
    $suggest = new View('Add Card');
?>
<?= $suggest->displayHead(); ?>

<div id="wrapper">

<?= $suggest->displayHeader(); ?>
    
<div id="main">
    
    <div class="cardbox">
        <form action="add.php" method="post">
            <input type="hidden" name="type" value="question" />
            <div class="card question">
                <div class="NSFWtag">
                    <select name="NSFW">
                        <option value="SFW" class="SFW">SFW</option>
                        <option value="NSFW">NSFW</option>
                    </select>
                </div>
                
                <textarea name="text" placeholder="Enter Question Text"></textarea>
            
                <div class="source">
                    <input class="" name="source" placeholder="Enter Your Name" /><br />
                    <input class="" type="url" name="url"    placeholder="Enter Credit URL (optional)" />
                </div>
            </div>
        
            <input type="submit" name="submit" value="Submit" />
        
        </form>
    </div>
     
    <div class="cardbox">
        <form action="add.php" method="post">
            <input type="hidden" name="type" value="answer" />
            <div class="card answer">
                <div class="NSFWtag">
                    <select name="NSFW">
                        <option value="SFW" class="SFW">SFW</option>
                        <option value="NSFW">NSFW</option>
                    </select>
                </div>
                
                <textarea name="text" placeholder="Enter Answer Text"></textarea>
            
                <div class="source">
                    <input class="" name="source" placeholder="Enter Your Name" /><br />
                    <input class="" type="url" name="url"    placeholder="Enter Credit URL (optional)" />
                </div>
            </div>
        
        <input type="submit" name="submit" value="Submit" />
        
        </form>
    </div>
        
    <div class="instructions">
        <p>Questions should be in a form which can be answered by a single noun.</p>
        <p>Please mark your question Safe For Work (SFW) or <span class="NSFW">Not Safe For Work (NSFW)</span> as appropriate.</p>
    </div>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $suggest->displayFooter(); ?>
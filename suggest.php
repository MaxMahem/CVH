<?php
    /* contains the view class used for view elements. */
    require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/View.php');

    /* create View for page */
    $suggest = new View('Add Card');
?>
<?= $suggest->displayHead(); ?>

<div id="wrapper">

<?= $suggest->displayHeader(); ?>
    
<div id="main">
    <article>
        <h2>Add Question</h2>
        <form action="add.php" method="post">
            <input type="hidden" name="type" value="question" />
            <section class="card question">
                <h3>Question Details</h3>
                <select class='NSFW' name="NSFW">
                    <option value="SFW" class="SFW">SFW</option>
                    <option value="NSFW">NSFW</option>
                </select>
                
                <textarea name="text" placeholder="Enter Question Text" required></textarea>
            
                <address>
                    <input class=""            name="source" placeholder="Enter Your Name"  required><br />
                    <input class="" type="url" name="url"    placeholder="Enter Credit URL">
                </address>
            </section>
            <section>
                <h3>Question Submit</h3>
                <input type="submit" name="submit" value="Submit" />
            </section>
        </form>
        <section>
            <h3>Instructions</h3>
            <p>Questions should be in a form which can be answered by a single noun.</p>
        </section>       
    </article>

    <section>
        <h2>Add Answer</h2>
        <form action="add.php" method="post">
            <input type="hidden" name="type" value="answer" />
            <div class="card answer">
                <select class='NSFW' name="NSFW">
                        <option value="SFW" class="SFW">SFW</option>
                        <option value="NSFW">NSFW</option>
                </select>
                
                <textarea name="text" placeholder="Enter Answer Text"></textarea>
            
                <address>
                    <input class="" name="source" placeholder="Enter Your Name" /><br />
                    <input class="" type="url" name="url"    placeholder="Enter Credit URL (optional)" />
                </address>
            </div>
        
        <input type="submit" name="submit" value="Submit" />
        
        </form>
    </section>
    
    <section class="instructions">
        <h2>Instructions</h2>
        <p>Please mark your question Safe For Work (SFW) or <span class="NSFW">Not Safe For Work (NSFW)</span> as appropriate.</p>
    </section>
    
    <div class="clear"></div>
    
</div> <!-- End of #main -->
    
</div> <!-- End of #wrapper -->
    
<?= $suggest->displayFooter(); ?>
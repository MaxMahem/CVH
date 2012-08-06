<?php
    /* retrieve the get info from the url. Pages are sent in the format:
     * /CVH/display/QUESTIONID-ANSWERID, and transformed via the .htaccess to:
     * /CVH/display.php?Q=QUESTIONID&A=ANSWERID 
     * filter_input is probably not necessary but we use it just to be safe */
    $getQuestionId = filter_input(INPUT_GET, 'Q', FILTER_SANITIZE_STRING);
    $getAnswerId   = filter_input(INPUT_GET, 'A', FILTER_SANITIZE_STRING);
    
    /* filter_input will return null if the variable is not set, false if the
     * filter fails, or the variable. Since '0' would be a valid variable for us
     * and php evaluates that to false, we just check to see if the variable is
     * set. It's possible some bad variables could slip through this, but they 
     * will just get messed up output, or fail elseware, GIGO. */
    /* Even more complicated, we actually only require a question OR and answer,
     * if we get don't get one of them, that's fine, we'll return a random 
     * result instead. */
    if (!(isSet($getQuestionId) || isset($getAnswerId))) {
        /* we didn't get any good variables, redirect to our bad url page */
        $redirectDest = "/CVH/bad/QuitMonkeyingWithTheURLs";
    } else {
        /* we got some variables, process them. */
              
        /* the db-connection file is should define DBHOST, DBUSER, DBPASS, and DBNAME 
        * with their appropriate values, and be located outside of the webroort, two
        * leveles up from this page */
        include('../../../db-connection.php');
    
        /* Connect to the DB. */
        $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

        /* check the status of the db link */
        if (!$mysqlLink) {
            /* if mysqlLink is false, we had a problem. Handle it. */
            $redirectDest = "/CVH/bad/dbError";
        } else {
            /* DB link is good. */

            /* check if the question id is set */
            if (isset($getQuestionId)) {
                /* Query question id */
                $questionId     = hexdec($getQuestionId);
                $questionResult = mysqli_query($mysqlLink, "SELECT questions.id, questions.question, sources.source, sources.url " .  
                                                             "FROM questions INNER JOIN sources ON questions.source_id = sources.id " . 
                                                             "WHERE questions.id = $questionId ");
            } else {
                /* Query random question */
                $questionResult = mysqli_query($mysqlLink, "SELECT questions.id, questions.question, sources.source, sources.url " .  
                                                             "FROM questions INNER JOIN sources ON questions.source_id = sources.id " . 
                                                             "ORDER BY RAND() LIMIT 0,1");
            }
            
            /* check if the answer id is set */
            if (isset($getAnswerId)) {
                /* Query answer id */
                $answerId       = hexdec($getAnswerId);
                $answerResult   = mysqli_query($mysqlLink,"SELECT answers.id,    answers.answer,     sources.source, sources.url " . 
                                                            "FROM answers    INNER JOIN sources ON answers.source_id   = sources.id " .
                                                            "WHERE answers.id    = $answerId ");
            } else {
                /* Query random answer */
                $answerResult   = mysqli_query($mysqlLink,"SELECT answers.id,    answers.answer,     sources.source, sources.url " . 
                                                            "FROM answers    INNER JOIN sources ON answers.source_id   = sources.id " .
                                                            "WHERE answers.NSFW = 0 " .
                                                            "ORDER BY RAND() LIMIT 0,3");            
            }

            /* check the results of those queries */
            if (!($questionResult && $answerResult)) {
                /* query failed */
                $redirectDest = "/CVH/bad/queryFailed";
            } else {
                /* query succeded, fetch the results */
                $question       = mysqli_fetch_assoc($questionResult);
                $answer         = mysqli_fetch_assoc($answerResult);
                
                /* check if we got an answer, if for some reason we returned no rows this would be null
                 * the only reason this should be is bad info passed in the URL. */
                if (is_null($question) || is_null($answer)) {
                    $redirectDest = "/CVH/bad/dontknowthat";
                } else {            
                    /* Query Vote Totals - We might get a random question which already 
                     * has a vote total, so we use the query results instead of the id's */
                    $voteResult  = mysqli_query($mysqlLink, "SELECT * FROM questions_answers_votes " .
                                                              "WHERE question_id = " . $question['id'] .
                                                              " AND  answer_id   = " . $answer['id']);
                    
                    /* check the vote query */
                    if (!($voteResult)) {
                        /* query failed */
                        $redirectDest = "/CVH/bad/queryFailed";
                    } else {
                        $vote    = mysqli_fetch_assoc($voteResult);
                        
                        /* get the number of votes */
                        if (is_null($vote)) {
                            /* we returned no rows, which means there is no record and no votes */
                            $voteTally = '0';
                        } else {
                            $voteTally = $vote['vote_tally'];
                        }
                        
                        /* check if vote should be plural */
                        if ($voteTally == 1) {
                            $voteWord = 'vote.';
                        } else {
                            $voteWord = 'votes.';
                        }
                        
                        /* set the perm URL */
                        $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .
                                     strtoupper(dechex($question['id'])) . "-" .
                                     strtoupper(dechex($answer['id']));
                    }
                }
            }
        }
    }
    
    /* Redirect if we had an error. */
    if (isset($redirectDest)) {
        $redirectURL = "http://" . $_SERVER['HTTP_HOST'] . $redirectDest;
        header('Location: ' . $redirectURL, 303);
        die();
    }
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">  

<head>
    <meta charset="utf-8" />
    <title>Cards vs Humans - Display</title>
    <link rel="stylesheet" type="text/css" href="/CVH/cvh.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 
    <script type="text/javascript" src="/CVH/cvh.js"></script>
</head>

<body>
    <div id="header">
        <h1><a href="/CVH">Cards vs Humans</a></h1>
    </div>
	
    <div class="card question">
        <?php echo $question['question']; ?>
        <a href="<?php echo $question['url']; ?>" class="source">
<?php 
    if ($question['source'] == 'Cards Against Humanity') {
        echo '<img src="/CVH/CAH-Cards-White.svg" alt="Cards Against Humanity" />'; 
    }
    echo $question['source'] . PHP_EOL;
?>
        </a>
    </div>
    
    <div class="instructions">
        This combination has received <br />
        <span class="votes"><?php echo $voteTally; ?></span><br />
        <?php echo $voteWord . PHP_EOL; ?>
    </div>
    
    <div class="clear"></div>
    
    <div class="card answer">
        <?php echo $answer['answer']; ?>
        <a href="<?php echo $answer['url']; ?>" class="source">
<?php 
    if ($answer['source'] == 'Cards Against Humanity') {
        echo '<img src="/CVH/CAH-Cards-White.svg" alt="Cards Against Humanity" />';
    }
    echo $answer['source'] . PHP_EOL;
?>
        </a>
    </div>
    
    <div class="clear"></div>

    <div class="permalink">
        <p><a href="<?php echo $permURL; ?>" >Permalink</a></p>
    </div>
</body>
</html>
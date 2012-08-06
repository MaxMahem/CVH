<?php    
    /* retrieve the get info from the url. Pages are sent in the format:
     * /CVH/vote/QUESTIONID-ANSWERID, and transformed via the .htaccess to:
     * /CVH/vote.php?Q=QUESTIONID&A=ANSWERID 
     * filter_input is probably not necessary here, but we use it just to be
     * extra safe */
    $getQuestionId = filter_input(INPUT_GET, 'Q', FILTER_SANITIZE_STRING);
    $getAnswerId   = filter_input(INPUT_GET, 'A', FILTER_SANITIZE_STRING);
    
    /* filter_input will return null if the variable is not set, false if the
     * filter fails, or the variable. Since '0' would be a valid variable for us
     * and php evaluates that to false, we just check to see if the variable is
     * set. It's possible some bad variables could slip through this, but they 
     * should fail on the insert. No check on Malicous behavior yet */
    if (!(isSet($getQuestionId) && isset($getAnswerId))) {
        /* we didn't get good variables, redirect to our bad url page */
        $redirectDest = "/CVH/bad/QuitMonkeyingWithTheURLs";
    } else {
        /* we got some variables, process them. */
        
        /* questionId and answerId should be in hex (just cause) convert them */
        $questionId = hexdec($getQuestionId);
        $answerId   = hexdec($getAnswerId);
        
        /* the db-connection file is should define DBHOST, DBUSER, DBPASS, and DBNAME 
        * with their appropriate values, and be located outside of the webroort, two
        * leveles up from this page */
        include('../../../db-connection.php');
    
        /* Connect to the DB. */
        $mysqlLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

        /* check the status of the db link */
        if (!$mysqlLink) {
            /* if mysqlLink is false, we had a problem. Handle it. */
            /* TODO: at somepoint add some error handling to bad.php */
            $redirectDest = "/CVH/bad/dbError";
        } else {
            /* DB link is good. */

            /* Do vote insert. If we already have a value, update the vote_tally instead. */
            $voteResult = mysqli_query($mysqlLink, "INSERT INTO questions_answers_votes (question_id, answer_id, vote_tally) " . 
                                                     "VALUES ($questionId, $answerId, 1) " .
                                                     "ON DUPLICATE KEY UPDATE vote_tally = vote_tally + 1");
        
            /* since this is an INSERT query, we should get a TRUE on success and FALSE on failure. */
            if (!$voteResult) {
                /* insert unsuccesful */
                $redirectDest = "/CVH/bad/queryFailed";
            } else {
                /* our vote was succesfully inserted go back and let the user vote again */
                $redirectDest = "/CVH/";
            }
        }
    }
    
    /* Redirect. If everything went well we should be heading to /CVH otherwise /CVH/bad.php */
    $redirectURL = "http://" . $_SERVER['HTTP_HOST'] . $redirectDest;
    header('Location: ' . $redirectURL, 303);
    die();
?>
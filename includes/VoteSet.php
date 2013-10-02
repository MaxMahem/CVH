<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardPair.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Set.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VoteSet
 *
 * @author astanley
 */
class VoteSet extends Set {
    protected $NSFW;
    protected $unvalidated;
    
    const RECENT = 'recent';
    const TOP    = 'top';
    
    /** CardSet Constructor
     * 
     * @param string $type        Type of card set, either Card::QUESTION or CARD::ANSWER
     * @param bool   $NSFW        Return NSFW results or not. Defaults to FALSE
     * @param bool   $unvalidated Return unvalidated cards or not. Defaults to FALSE.
     *
     * @throws InvalidArgumentException If given bad data it is unhappy.
     */
    public function VoteSet($NSFW = FALSE, $unvalidated = FALSE) {
        if ((!is_bool($NSFW)) || (!is_bool($unvalidated))) {
            $message = "Bad arguments passed to new VoteSet" . PHP_EOL
                     . "NSFW:        $NSFW" . PHP_EOL
                     . "unvalidated: $unvalidated" . PHP_EOL;
            throw new InvalidArgumentException($message);
        }
        
        $this->NSFW        = $NSFW;
        $this->unvalidated = $unvalidated;
    }
    
    public function getAll($type = self::TOP, $page = 0) {
        /* validate input */
        if (($type !== self::TOP) && ($type !== self::RECENT)) {
            throw new InvalidArgumentException("Invalid type: $type passed to getAll");
        }
        if (!is_integer($page)) {
            throw new InvalidArgumentException("Invalid page: $page passed to getAll");
        }
        
        /* set the correct offset/page */
        $offset = $page * self::COUNT;
        $this->page = $page;

        /* this query will get all the votes from */
        $selectClause[] = "`questions_answers_votes`.`question_id`";
        $selectClause[] = "`questions_answers_votes`.`answer_id`";
        $selectClause[] = "`questions_answers_votes`.`vote_tally`";
        $selectClause[] = "`questions_answers_votes`.`updated`";
        
        foreach(array('questions', 'answers') as $type) {
            $selectClause[] = "`$type`.`id`";
            $selectClause[] = "`$type`.`NSFW`";
            $selectClause[] = "`$type`.`validated`";
        }
        
        $select = "SELECT" . ' ' . implode(' , ', $selectClause);
                
        $from   = "FROM   `questions_answers_votes`, "
                .        "`questions`, "
                .        "`answers`";
        
        $whereClause[] = (!$this->NSFW)        ? "`questions`.`NSFW`      = FALSE" : 'TRUE';
        $whereClause[] = (!$this->unvalidated) ? "`questions`.`validated` = TRUE"  : 'TRUE';
        $whereClause[] = (!$this->NSFW)        ? "`answers`.`NSFW`        = FALSE" : 'TRUE';
        $whereClause[] = (!$this->unvalidated) ? "`answers`.`validated`   = TRUE"  : 'TRUE';
        
        $whereClause[] = "`questions_answers_votes`.`question_id` = `questions`.`id`";
        $whereClause[] = "`questions_answers_votes`.`answer_id`   = `answers`.`id`";
        
        $where = "WHERE" . ' ' . implode(' AND ', $whereClause);
        
        $limit = "LIMIT $offset," . ' ' . self::COUNT;
        
        /* order by the appropriate type */
        if ($type === self::RECENT) {
            $orderField = 'updated';
        } else {
            $orderField = 'vote_tally';
        }
        
        $order = "ORDER BY `questions_answers_votes`.`$orderField` DESC";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' '. $limit;

        $this->retrieve($query);      
    }

    protected function retrieve($query) {       
        /* get connection to DB */
        $mysqli = $this->dbConnect();
        
        /* get the data */
        $result = $mysqli->query($query);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $query", $mysqli->errno);
        }
        
        /* get all the Card Pairs */
        while ($row = mysqli_fetch_assoc($result)) {            
            $question = new Card(Card::QUESTION, $row['question_id']);
            $answer   = new Card(Card::ANSWER, $row['answer_id']);
            
            $this->data[] = new CardPair($question, $answer, $row['vote_tally'], $row['updated']);
        } 
    }
}

?>
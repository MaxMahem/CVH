<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');

/**
 * Description of CardPair
 *
 * @author MaxMahem
 */
class CardPair {
    private $question;
    private $answer;

    private $questionId;
    private $answerId;
    
    public function CardPair(Card $question, Card $answer) {
        if (($question->type != Card::QUESTION) || (!is_numeric($question->id))) {
            throw new InvalidArgumentException("Invalid Question passed to new CardPair");
        }
        if (($answer->type   != Card::ANSWER)   || (!is_numeric($answer->id))) {
            throw new InvalidArgumentException("Invalid Answer passed to new CardPair");
        }
        
        $this->question = $question;
        $this->answer   = $answer;
        
        $this->questionId = $this->question->id;
        $this->answerId   = $this->answer->id;
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new LogicException("Attempted to get property $property which does not exist.");
        }
    }

    public function vote() {
        $mysqli = $this->dbConnect();
        
        /* Do vote insert. If we already have a value, update the vote_tally instead. */
        $insert = "INSERT INTO questions_answers_votes (question_id, answer_id, vote_tally)";
        $values = "VALUES ($this->questionId, $this->answerId, 1)";
        $on     = "ON DUPLICATE KEY UPDATE vote_tally = vote_tally + 1";
        
        $result = $mysqli->query($insert . ' ' . $values . ' ' . $on);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                          . "QUERY: $insert", $mysqli->errno);
        }
        
        return $result;
    }
    
    public function permalink() {
        return "/CVH/view/pair/Q$this->questionId/A$this->answerId";
    }

        /** dbConnect()
     * Makes a connection to the database
     *
     * @return mysqli
     */
    private function dbConnect() {
        /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME
         * with their appropriate values, and should be located outside of the webroot  */
        require($_SERVER['DOCUMENT_ROOT'] . '/../db-connection.php');
        
        /* connect to DB */
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        if ($mysqli->connect_errno) {
            throw new mysqli_sql_exception("Error connecting to MySQL: $mysqli->connect_error", $mysqli->errno);
        }
        
        return $mysqli;
    }

}

?>

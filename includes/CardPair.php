<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');

/**
 * Description of CardPair
 *
 * @author MaxMahem
 */
class CardPair extends Item {
    private $question;
    private $answer;
    private $votes;
    
    public function CardPair(Card $question, Card $answer, $votes = NULL, $added = NULL) {
        if (($question->type != Card::QUESTION) || (!is_numeric($question->id))) {
            throw new InvalidArgumentException("Invalid Question passed to new CardPair");
        }
        if (($answer->type   != Card::ANSWER)   || (!is_numeric($answer->id))) {
            throw new InvalidArgumentException("Invalid Answer passed to new CardPair");
        }
        
        $this->question = $question;
        $this->answer   = $answer;
        
        /* pair the numbers for an id, using Carnot Pairing function */
        $this->id = CardPair::pair($question->id, $answer->id);
        
        /* added actually refers to when the pair was last voted on */
        $this->votes = $votes;
        $this->added = $added;
    }

    public function vote() {
        $mysqli = $this->dbConnect();
        
        /* Do vote insert. If we already have a value, update the vote_tally instead. */
        $insert = "INSERT INTO questions_answers_votes (id, question_id, answer_id, vote_tally)";
        $values = "VALUES (" . $this->id . ", ". $this->question->id . ", " . $this->answer->id . ", 1)";
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
        return "/CVH/view/pair/Q$this->question->id/A$this->answer->id";
    }
    
    public function display($linkType = Card::LINK) {
        $display  = $this->question->display($linkType) . PHP_EOL;
        $display .= $this->answer->display($linkType);
        
        return $display;
    }
    
    public function retrieve() {
        $this->question->retrieve();
        $this->answer->retrieve();
        
        /* @todo: do queries to retrieve added and votes if possible from questions_answers_votes */
    }
    
    public function getVotes() {
        /* an item might legitmatly have no votes, if it doesn't, we return 0 */
        /* @todo: consider if this is actually true */
        if ($this->votes === null) {
            $votes = 0;
        } else {
            $votes = $this->votes;
        }
        
        return $votes;
    }
    
    /** pair($questionId, $answerId) 
     * Generates a Carnot pair of two numbers.
     * 
     * @param int $questionId question number.
     * @param int $answerId   answer number.
     * @return int the carnot pair of the two numbers.
     */
    protected static function pair($questionId, $answerId) {
        $x = $questionId;
        $y = $answerId;
        return (($x + $y) * ($x + $y + 1)) / 2 + $y;
    }
}

?>

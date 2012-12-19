<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * a set of cards.
 *
 * @author MaxMahem
 */
class CardSet {
    private $type;
    private $cards;
    
    function CardSet($type) {
        if (($type != Card::QUESTION) && ($type != Card::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new CardSet");
        }
    
        $this->type = $type;
    }
    
    /** dbConnect()
     * Makes a connection to the Card database
     *
     * @return mysqli
     */
    private function dbConnect() {
        /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME
         * with their appropriate values, and should be located outside of the webroot  */
        include_once($_SERVER['DOCUMENT_ROOT'] . '/../db-connection.php');
        
        /** @todo: maybe add more error checking here, I don't like returning this info to the user though */
        $mysqliLink = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
        if (!$mysqliLink) {
            echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . PHP_EOL;
            return false;
        }

        return $mysqliLink;
    }

    
    public function getAllCards() {        
        $mysqliLink = $this->dbConnect();
        
        /* tables are plural, so add an s */
        $table = $this->type . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT $table.id";      
        $from   = "FROM $table";

        /* build the query */
        $query = $select . ' ' . $from;

        /* get the data */
        $result = mysqli_query($mysqliLink, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return exit;
        }

        /* get all the cards's */
        while ($row = mysqli_fetch_assoc($result)) {
            $this->cards[$row['id']] = new Card($this->type);
            $this->cards[$row['id']]->getCard($row['id']);
        }
    }
    
    public function getTopCards(Card $pairCard, $num = 4) {
        $pairId   = $pairCard->getId(Card::DECIMAL);
        $pairType = $pairCard->getType();
        
        if ($pairType == $this->type) {
            throw new InvalidArgumentException("CardSet->getTopCards called with bad Card type, $pairType, pair card must be the opposite type as the paired set.");
        }
        
        $mysqliLink = $this->dbConnect();
        
        $select = "SELECT `questions_answers_votes`.`$this->type" . "_id`";
        $from   = "FROM   `questions_answers_votes`";

        $whereClauses[] = "`questions_answers_votes`.`$pairType" . "_id`=$pairId";
        $whereClauses[] = "`questions_answers_votes`.`vote_tally` != 0";
        
        $where = "WHERE" . ' ' . implode(' AND ', $whereClauses);
        $order = "ORDER BY `questions_answers_votes`.`vote_tally`";
        $limit = "LIMIT 0, $num";
        
        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' ' . $limit;
    
        /* get the data */
        $result = mysqli_query($mysqliLink, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return exit;
        }

        /* get all the cards's */
        while ($row = mysqli_fetch_assoc($result)) {
            $this->cards[$row['id']] = new Card($this->type);
            $this->cards[$row['id']]->getCard($row['id']);
        }
    }
    
    public function getSourceCards($sourceId) {
        $mysqliLink = $this->dbConnect();
        
        /* tables are plural, so add an s */
        $table = $this->type . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM `$table`";
        $where  = "WHERE `$table`.`source_id`=$sourceId";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where;

        /* get the data */
        $result = mysqli_query($mysqliLink, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return exit;
        }

        /* get all the cards's */
        while ($row = mysqli_fetch_assoc($result)) {
            $this->cards[$row['id']] = new Card($this->type);
            $this->cards[$row['id']]->getCard($row['id']);
        }
    }
    
    public function displayAllCards() {
        $display  = "<div class=\"$this->type\">";
        
        foreach ($this->cards as $card) {
            $display .= '<div class="cardbox">';
            $display .= $card->displayCard();
            $display .= '</div>';
        }
        
        $display .= '</div>';
            
        return $display;
    }
}

?>

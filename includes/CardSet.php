<?php
/**
 * a set of cards.
 *
 * @author MaxMahem
 */
class CardSet implements IteratorAggregate {
    private $type;
    private $cards;
    private $NSFW;
    
    public function CardSet($type, $NSFW = FALSE) {
        if (($type != Card::QUESTION) && ($type != Card::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new CardSet");
        }
        if (!is_bool($NSFW)) {
            throw new InvalidArgumentException("Non bool NSFW: $NSFW passed to new CardSet");
        }
        
        $this->NSFW = $NSFW;
        $this->type = $type;
    }
    
    public function getIterator() {
        return new ArrayIterator($this->cards);
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getAll($start = '0', $number = '30') {        
        /* tables are plural, so add an s */
        $table = $this->type . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM `$table`";
        $where  = ($this->NSFW == FALSE) ? "WHERE $table.NSFW = FALSE" : '';
        $limit  = "LIMIT $start, $number";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $limit;

        $this->getData($query);
    }
    
    public function getRandom($number = '3') {
        /* tables are plural, so add an s */
        $table = $this->type . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM `$table`";
        $where  = ($this->NSFW == FALSE) ? "WHERE $table.NSFW = FALSE" : '';
        $order  = "ORDER BY RAND()";
        $limit  = "LIMIT 0, $number";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' ' . $limit;

        $this->getData($query);
    }


    public function getTop(Card $pairCard, $number = 4) {
        $pairId   = $pairCard->getId(Card::DECIMAL);
        $pairType = $pairCard->getType();
        
        if ($pairType == $this->type) {
            throw new InvalidArgumentException("CardSet->getTopCards called with bad Card type, $pairType, pair card must be the opposite type as the paired set.");
        }
        
        $select = "SELECT `questions_answers_votes`.`$this->type" . "_id`";
        $from   = "FROM   `questions_answers_votes`";

        $whereClauses[] = "`questions_answers_votes`.`$pairType" . "_id`=$pairId";
        $whereClauses[] = "`questions_answers_votes`.`vote_tally` != 0";
        
        $where = "WHERE" . ' ' . implode(' AND ', $whereClauses);
        $order = "ORDER BY `questions_answers_votes`.`vote_tally`";
        $limit = "LIMIT 0, $number";
        
        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' ' . $limit;
    
        $this->getData($query);
    }
    
    public function getSource($sourceId) {       
        /* tables are plural, so add an s */
        $table = $this->type . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM   `$table`";
        $where  = "WHERE  `$table`.`source_id`=$sourceId";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where;
        
        $this->getData($query);
    }
    
    
    private function getData($query) {       
        /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME
         * with their appropriate values, and should be located outside of the webroot  */
        require($_SERVER['DOCUMENT_ROOT'] . '/../db-connection.php');
        
        /* connect to DB */
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        if ($mysqli->connect_errno) {
            throw new mysqli_sql_exception("Error connecting to MySQL: $mysqli->connect_error", $mysqli->errno);
        }
        
        /* get the data */
        $result = $mysqli->query($query);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $query", $mysqli->errno);
        }
        
        /* get all the cards's */
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $this->cards[$id] = new Card($this->type, $id);
        }       
    }    
}
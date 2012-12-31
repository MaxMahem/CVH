<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Card.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Set.php');

/**
 * a set of cards.
 *
 * @author MaxMahem
 */
class CardSet extends Set {
    protected $cardType;
    protected $NSFW;
    protected $unvalidated;
    
    /** CardSet Constructor
     * 
     * @param string $type        Type of card set, either Card::QUESTION or CARD::ANSWER
     * @param bool   $NSFW        Return NSFW results or not. Defaults to FALSE
     * @param bool   $unvalidated Return unvalidated cards or not. Defaults to FALSE.
     *
     * @throws InvalidArgumentException If given bad data it is unhappy.
     */
    public function CardSet($type, $NSFW = FALSE, $unvalidated = FALSE) {
        if (
            (($type != Card::QUESTION) && ($type != Card::ANSWER)) || 
            (!is_bool($NSFW)) || 
            (!is_bool($unvalidated))
           )    {
            $message = "Bad arguments passed to new CardSet" . PHP_EOL
                     . "Type:        $type" . PHP_EOL
                     . "NSFW:        $NSFW" . PHP_EOL
                     . "unvalidated: $unvalidated" . PHP_EOL;
            throw new InvalidArgumentException($message);
        }
        
        $this->NSFW        = $NSFW;
        $this->cardType    = $type;
        $this->unvalidated = $unvalidated;
    }
    
    public function getAll($page = 0) {
        $offset = $page * self::COUNT;
        $this->page = $page;
        
        /* tables are plural, so add an s */
        $table = $this->cardType . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM   `$table`";
        
        $whereClause[] = (!$this->NSFW)        ? "`$table`.`NSFW` = FALSE"     : 'TRUE';
        $whereClause[] = (!$this->unvalidated) ? "`$table`.`validated` = TRUE" : 'TRUE';
        $where = "WHERE" . ' ' . implode(' AND ', $whereClause);
        
        $limit  = "LIMIT $offset," . ' ' . self::COUNT;

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $limit;

        $this->retrieve($query);
    }
    
    public function getRandom($number = '3') {
        /* tables are plural, so add an s */
        $table = $this->cardType . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM `$table`";
        $where  = ($this->NSFW == FALSE) ? "WHERE $table.NSFW = FALSE" : '';
        $order  = "ORDER BY RAND()";
        $limit  = "LIMIT 0, $number";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' ' . $limit;

        $this->retrieve($query);
    }


    public function getTop(Card $pairCard, $number = 4) {
        $pairId   = $pairCard->id;
        $pairType = $pairCard->type;
        
        if ($pairType == $this->cardType) {
            throw new InvalidArgumentException("CardSet->getTopCards called with bad Card type, $pairType, pair card must be the opposite type as the paired set.");
        }
        
        /* Important, retrieve function expects a field as 'id' so important to
         * do 'AS id' here. */
        $select = "SELECT `questions_answers_votes`.`{$this->cardType}_id` AS id";
        $from   = "FROM   `questions_answers_votes`";

        $whereClauses[] = "`questions_answers_votes`.`{$pairType}_id`=$pairId";
        $whereClauses[] = "`questions_answers_votes`.`vote_tally` != 0";
        $where = "WHERE" . ' ' . implode(' AND ', $whereClauses);
        
        $order = "ORDER BY `questions_answers_votes`.`vote_tally`";
        $limit = "LIMIT 0, $number";
        
        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order . ' ' . $limit;
    
        $this->retrieve($query);
    }
    
    public function getSource(Source $source) {       
        /* tables are plural, so add an s */
        $table = $this->cardType . 's';

        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM   `$table`";
        $where  = "WHERE  `$table`.`source_id`=$source->id";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where;
        
        $this->retrieve($query);
    }
    
    protected function retrieve($query) {       
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
            $this->data[$id] = new Card($this->cardType, $id);
        }       
    }    
}
<?php

/**
 * @todo add error checking maybe?
 */
class Card {
    private $id;
    private $type;
    private $text;
    private $NSFW;
    private $source;
    private $sourceURL;

    const RANDOM_CARD = -1;
    
    const QUESTION    = 'question';
    const ANSWER      = 'answer';
    
    const HEX         = 'hex';
    const DECIMAL     = 'decimal';
    
    const LINK        = 'link';
    
    /** Card($type) constructor
     * Creates a new card. Type and id are required.
     * 
     * @param string $type type of card, either Card::QUESTION or Card::ANSWER
     * @param int    $id   id of card to get or Card::RANDOM for random card
     * @param bool   $NSFW return NSFW cards or not. Default false.
     */
    function Card($type, $id, $NSFW = FALSE) {
        if (($type != self::QUESTION) && ($type != self::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new Card");
        }
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Non numeric id: $id passed to new Card");
        }
        if (!is_bool($NSFW)) {
            throw new InvalidArgumentException("Non bool NSFW: $NSFW passed to new Card");
        }
        
        $this->type = $type;
        $this->id   = $id;
        $this->NSFW = $NSFW;
        
        $this->retrieveCard();
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
    
    /** retrieveCard()
     * retrieve's data about a card from the DB.
     * 
     * If a card id isn't set, it gets a random card.
     * @todo consider removing random card functionality into seperate function.
     * 
     * @return boolean  returns true on success, false on failure.
     */
    private function retrieveCard() {
        $mysqliLink = $this->dbConnect();
        
        /* the DB we query (questions or answers) is plural. So it should be
         * equal to the type (question or answer) variable plus an s. */
        $table = $this->type . 's';

        /* fields to be selected */
        $selectClauses[] = "$table.id";
        $selectClauses[] = "$table.text";
        $selectClauses[] = "$table.NSFW";
        $selectClauses[] = "sources.source";
        $selectClauses[] = "sources.url";
                
        /* build select from selectClauses array */
        $select = "SELECT" . ' ' . implode(', ', $selectClauses);
        
        $from = "FROM $table INNER JOIN sources ON $table.source_id = sources.id";

        /* having a do nothing whereClause makes later logic easier. We don't have to evaluate
         * for empty whereClauses, we can implode them all. */
        $whereClauses[] = 'TRUE';
        
        /* if we got a specific id, we want to return that row specifically. */
        if ($this->id != self::RANDOM_CARD) {
            $whereClauses[] = "$table.id = $this->id";
        }
        
        /* If we are getting a RANDOM card, and NSFW is TRUE we want to exclude this clause
         * If we are getting a RANDOM card, and NSFW is FALSE we want to include this clause
         * If we aren't getting a RANDOM card, we want to exclude this clause.
         * Including this clause will exclude NSFW entries. */
        if (($this->id == self::RANDOM_CARD) && ($this->NSFW == FALSE)) {
            $whereClauses[] = "$table.NSFW = FALSE";
        }

        /* build the where of the query. The different clauses get linked by AND */
        $where = "WHERE" . ' ' . implode(' AND ', $whereClauses);

        /* if we get a id of 0, we want a random result, do this with an order by rand() statment. */
        if ($this->id == self::RANDOM_CARD) {
            $order = "ORDER BY RAND() LIMIT 0,1";   /* random result */
        } else {
            $order = "";
        }

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order;

        /* get the data */
        $result = mysqli_query($mysqliLink, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        $data = mysqli_fetch_assoc($result);

        /* Assign the data to class varaibles. */
        $this->id        = $data['id'];
        $this->text      = $data['text'];
        $this->NSFW      = $data['NSFW'];
        $this->source    = $data['source'];
        $this->sourceURL = $data['url'];
        
        return true;
    }
    
    /** insertCard()
     * inserts the current card into the DB.
     * 
     * Inserts the current card into the DB, as well as the source if not
     * present. Currently does not validate a cards data to make sure its good.
     * Sources are checked for uniqueness, cards are not, yet.
     * 
     * @todo Add validation of data, probably seperate function.
     * @todo Add checking for card uniqueness.
     * 
     * @return int id of card after insert, false on failure.
     */
    private function insertCard() {
        /* get connection to DB */
        $mysqliLink = $this->dbConnect();

        /* Insert the source into the DB. The first part is standard, but if we
         * hit an address that isn't unique, we do some magic. id=LASTER_INSERT_ID(id)
         * shouldn't make any change to the DB, but will arrange our values such
         * that mysqli_insert_id will return the id of the duplicate value. */
        $sourceInsert = "INSERT INTO `sources` (`source`,        `url`)" . ' '
                      . "VALUES                ('$this->source', '$this->sourceURL')" . ' '
                      . "ON DUPLICATE KEY UPDATE `id` = LAST_INSERT_ID(`id`)";
        
        $sourceResult = mysqli_query($mysqliLink, $sourceInsert);
        
        /* check for query errors */
        if (!$sourceResult) {
            echo "QUERY:" . ' ' . $sourceInsert . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        
        $table = $this->type . 's';
        
        /* this should return us the id of the Source */
        $sourceId = mysqli_insert_id($mysqliLink);
        
        $cardInsert = "INSERT INTO `$table` (`text`,        `NSFW`,        `source_id`)" . ' '
                    . "VALUES               ('$this->text', '$this->NSFW', '$sourceId' )";
        
        $cardResult = mysqli_query($mysqliLink, $cardInsert);
        
        if (!$cardResult) {
            echo "QUERY:" . ' ' . $cardInsert . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        
        /* get the id of the card created */
        $cardId = mysqli_insert_id($mysqliLink);
        
        return $cardId;
    }
    
    /**addCard
     * adds data for current card, and adds it to the database.
     * 
     * @todo   add more card validation
     * 
     * @param  boolean $NSFW      true if the card is NSFW, false if not.
     * @param  string  $text      text of the card
     * @param  string  $source    source of the card
     * @param  string  $sourceURL URL of the card source
     * @return int id of new card.
     */
    public function addCard($NSFW, $text, $source, $sourceURL) {
        
        if ($NSFW == 'NSFW') {
            $this->NSFW = TRUE;
        } else {
            $this->NSFW = FALSE;
        }

        $this->text      = $text;
        $this->source    = $source;
        $this->sourceURL = $sourceURL;
        
        /* this should return the id of our new card on success, and false on
         * failure. */
        $cardId = $this->insertCard();
        
        $this->id = $cardId;
        
        return $cardId;
    }

    /** displayCard
    * Returns a properly formated card for display.
    * @todo move to view class.
    *
    * @param   string  $linkURL     the link the card should go to, if any.
    * @return  string  HTML code for the card.
    */
    public function displayCard($linkURL = self::LINK) {
        $classes[] = 'card';
        $classes[] = $this->type;

        if ($this->NSFW)      { $classes[] = 'NSFW'; }
        if ($linkURL != NULL) { $classes[] = 'vote'; }
        $class = implode(' ', $classes);

        $result .= "<article class='$class'>";
        
        /* header for the card, if NSFW we add a hgroup and a tag */
        $result .= ($this->NSFW) ? "<hgroup>" : '';
        $result .= "<h1>" . ucfirst($this->type) . ": $this->id </h1>";
        $result .= ($this->NSFW) ? "<h2 class='NSFW'>NSFW</h2></hgroup>" : '';

        if ($linkURL != NULL) {
            /* if we got self::LINK for a value, we want to simply point our link
             * at a link for this specific card */
            if ($linkURL == self::LINK) {
                $linkURL = "/CVH/view/$this->type/" . $this->getId(self::HEX);
            }
            /* if we recieved a vote URL, embeded the card text inside a voting link */
            $result .= "<a class='answerlink' href='" . $linkURL . "'>";
            $result .= $this->text;
            $result .= "</a>";
        } else {
            /* if we didn't recieve a vote URL, just spit out the card text. */
            $result .= $this->text;
        }

        $result .= "<address title='source'><a href='$this->sourceURL' rel='author'>";

        switch ($this->source) {
            case 'Cards Against Humanity':
                $result .=  "<img src=\"/CVH/CAH-Cards-$this->type.svg\" alt=\"Cards Against Humanity\" />";
                break;
            case 'Cards vs Humans':
                $result .= '<img src="/CVH/CVH_Logo.svg" alt="Cards vs Humans" />';
                break;
        }

        $result .= $this->source . '</a></address>';
        $result .= '</article>' . PHP_EOL;

        return $result;
    }
   
    /** numVotes()
     * Returns the number of votes this card has recieved.
     *
     * @return  int the number of votes this card has recieved.
     */
    public function numVotes() {
        /* get connection to DB */
        $mysqliLink = self::dbConnect();
        
        /* $typeId is the type + _id, which should be the id row name we want */
        $typeId = $this->type . '_id';
    
        $select  = "SELECT   `questions_answers_votes`.`$typeId`, SUM(`questions_answers_votes`.`vote_tally`) as `vote_tally`";
        $from    = "FROM     `questions_answers_votes`";
        $where   = "WHERE    `questions_answers_votes`.`$typeId`=$this->id";
        $groupBy = "GROUP BY `questions_answers_votes`.`$typeId`";
        
        /* build the query */
        $query   = $select . ' ' . $from . ' ' . $where . ' ' . $groupBy;
        
        $result = mysqli_query($mysqliLink, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        $data   = mysqli_fetch_assoc($result);
        
        /* get the number of votes */
        if (is_null($data)) {
            /* we returned no rows, which means there is no record and no votes */
            $votes = 0;
        } else {
            $votes = $data['vote_tally'];
        }
                
        return $votes;
    }
    
    /** getId
     * Returns the current id in either HEX or DECIMAL format. Default is decimal.
     * 
     * @param  string $format either 'hex' or 'decimal' default is decimal.
     * @return string/int id of card asked for in hex or decimal.
     */
    public function getId($format = self::DECIMAL) {
        /* check input format */
        if (($format != self::DECIMAL) && ($format != self::HEX)) {
            throw new InvalidArgumentException("Invalid format: $format passed to Card->getId");
        }
        
        /* check card id */
        if ((!isset($this->id)) || ($this->id == self::RANDOM_CARD)) {
            if (!isset($this->id)) {
                throw new LogicException("Card->getId called on card without id");
            }
            if ($this->id == self::RANDOM_CARD) {
                throw new LogicException("Card->getId called on card set to a RANDOM_CARD");
            }
        }
                    
        if ($format == self::DECIMAL) { $id = $this->id; }
        if ($format == self::HEX)     { $id = strtoupper(dechex($this->id)); }

        return $id;
    }
    
    /** getType
     * Returns the card type.
     * 
     * @return string the card type, either 'question' or 'answer'
     */
    public function getType() {
        return $this->type;
    }
}
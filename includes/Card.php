<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Item.php');

class Card extends Item {
    protected $type;
    protected $text;
    protected $NSFW;
    protected $source;
    
    const QUESTION = 'question';
    const ANSWER   = 'answer';
    
    const LINK     = 'link';
    
    /** Card($type) constructor
     * Creates a new card. Type and id are required.
     * 
     * @param string $type type of card, either Card::QUESTION or Card::ANSWER
     * @param int    $id   id of card to get, or null if adding a new card.
     */
    public function Card($type, $id = NULL) {
        if (($type != self::QUESTION) && ($type != self::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new Card");
        }
        $this->type = $type;
        
        if (isset($id)) {
            if (!is_numeric($id)) {
                throw new InvalidArgumentException("Non numeric id: $id passed to new Card");
            }
            
            $this->id   = $id;       
            $this->retrieve();            
        }
    }
    
    /** retrieveCard()
     * retrieve's data about a card from the DB.
     * 
     * @return boolean  returns true on success, false on failure.
     */
    protected function retrieve() {
        $mysqliLink = $this->dbConnect();
        
        /* the DB we query (questions or answers) is plural. So it should be
         * equal to the type (question or answer) variable plus an s. */
        $table = $this->type . 's';

        /* fields to be selected */
        $selectClauses[] = "`$table`.`id`";
        $selectClauses[] = "`$table`.`text`";
        $selectClauses[] = "`$table`.`NSFW`";
        $selectClauses[] = "`$table`.`source_id`";
        $selectClauses[] = "`$table`.`added`";
                
        /* build select from selectClauses array */
        $select = "SELECT" . ' ' . implode(', ', $selectClauses);
        $from   = "FROM  $table";        
        $where  = "WHERE $table.id = $this->id";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where;

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
        $this->added     = $data['added'];
        
        $this->source    = new Source($data['source_id']);

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
    protected function insert() {

    }
    
   /** displayCard
    * Returns a properly formated card for display.
    *
    * @param   string  $linkURL     the link the card should go to, if any.
    * @return  string  HTML code for the card.
    */
    public function display($linkURL = Card::LINK) {
        /* setup classes for card */
        $classes[] = 'card';
        $classes[] = $this->type;
        $classes[] = ($this->NSFW)      ? 'NSFW' : '';
        $classes[] = ($linkURL != NULL) ? 'link' : '';
        $class = trim(implode(' ', $classes));

        $result .= "<article class='$class'>";
        
        /* header for the card, if NSFW we add a hgroup and a tag */
        $result .= ($this->NSFW) ? "<hgroup>" : '';
        $result .= "<h3>" . ucfirst($this->type) . ": $this->id </h3>";
        $result .= ($this->NSFW) ? "<h4 class='NSFW'>NSFW</h4>" : '';
        $result .= ($this->NSFW) ? "</hgroup>" : '';

        if ($linkURL != NULL) {
            /* if we got self::LINK for a value, we want to simply point our link
             * at a link for this specific card */
            if ($linkURL == Card::LINK) {
                $linkURL = "/CVH/view/card/$this->type/" . dechex($this->id);
            }
            
            /* if we recieved a linkUrl, embeded the card text inside the link */
            $result .= "<a class='answerlink' href='" . $linkURL . "'>";
            $result .= $this->text;
            $result .= "</a>";
        } else {
            /* if we didn't recieve a vote URL, just spit out the card text. */
            $result .= $this->text;
        }

        $result .= $this->source->display($this->type);
        
        $result .= "</article>" . PHP_EOL;
        
        return $result;
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
    public function add($NSFW, $text, $sourceText, $sourceURL) {      
        $this->NSFW = $NSFW;
        $this->text = $text;
        
        $source = new Source();
        $sourceId = $source->add($sourceText, $sourceURL);
        
        if (!$sourceId) {
            echo 'Failed adding source.';
            return FALSE;
        }
                
        $mysqli = $this->dbConnect();
        
        $table = $this->type . 's';
                
        $insert = "INSERT INTO `$table` (`text`,        `NSFW`,        `source_id`)" . ' '
                . "VALUES               ('$this->text', '$this->NSFW', '$sourceId')";
        
        /* get the data */
        $result = $mysqli->query($insert);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $insert", $mysqli->errno);
        }
        
        /* get the id of the card created */
        $this->id = $mysqli->insert_id;
        
        return $this->id;
    }
  
    /** numVotes()
     * Returns the number of votes this card has recieved.
     *
     * @return  int the number of votes this card has recieved.
     */
    public function numVotes() {
        /* get connection to DB */
        $mysqli = $this->dbConnect();
        
        /* $typeId is the type + _id, which should be the id row name we want */
        $typeId = $this->type . '_id';
    
        $select  = "SELECT   `questions_answers_votes`.`$typeId`, SUM(`questions_answers_votes`.`vote_tally`) as `vote_tally`";
        $from    = "FROM     `questions_answers_votes`";
        $where   = "WHERE    `questions_answers_votes`.`$typeId` = $this->id";
        $groupBy = "GROUP BY `questions_answers_votes`.`$typeId`";
        
        /* build the query */
        $query  = $select . ' ' . $from . ' ' . $where . ' ' . $groupBy;
        
       /* get the data */
        $result = $mysqli->query($query);
                
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $query", $mysqli->errno);
        }
        $data = mysqli_fetch_assoc($result);
        
        /* get the number of votes */
        if (is_null($data)) {
            /* we returned no rows, which means there is no record and no votes */
            $votes = 0;
        } else {
            $votes = $data['vote_tally'];
        }
                
        return $votes;
    }
}
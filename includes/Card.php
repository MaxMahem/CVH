<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Source.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Item.php');

class Card extends Item {
    protected $type;
    protected $text;
    protected $NSFW;
    protected $source;
    
    const NEWCARD  = -1;
    
    const QUESTION = 'question';
    const ANSWER   = 'answer';
    
    const LINK     = 'link';
    const VOTE     = 'vote';
    
    /** Card($type) constructor
     * Creates a new card. Type and id are required.
     * 
     * @param string $type type of card, either Card::QUESTION or Card::ANSWER
     * @param int    $id   id of card to get, or null if adding a new card.
     */
    public function Card($type, $id) {
        if (($type != self::QUESTION) && ($type != self::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new Card");
        }
        $this->type = $type;
        
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Non numeric id: $id passed to new Card");
        }  
        $this->id = $id;
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
    * @param   string  $linkType    the link the card should go to, if any.
    * @param   string  $voteId      id of the card used in voting
    * @return  string               HTML code for the card.
    */
    public function display($linkType = Card::LINK, $voteId = NULL) {
        /* check arguments */
//        if (!(($linkType == NULL) || ($linkType == Card::LINK) || ($linkType == Card::VOTE))) {
//            throw new InvalidArgumentException("Bad linkType $linkType passed.");
//        }
        if (($linkType == Card::LINK) && (!empty($voteId))) {
            throw new InvalidArgumentException("No voteId $voteId passed with type Card::VOTE");
        }
        
        /* check if the card is empty and if so, retrieve it */
        if (empty($this->text)) { $this->retrieve(); }
        
        /* setup classes for card */
        $classes[] = 'card';
        $classes[] = $this->type;
        /* linkType is either 'vote' 'link' or NULL so makes an okay class decleration solo */
        $classes[] = $linkType;
        $class = trim(implode(' ', $classes));
        
        /* add the card id as ID, first letter of type + id number */
        $cardId = strtoupper($this->type[0]) . $this->id;

        $result[] = "<article class='$class' id='$cardId'>";       /* open article */
        
        /* header for the card, if NSFW we add a hgroup and a tag */
        $result[] = ($this->NSFW) ? "<hgroup>" : '';
        $result[] = "<h3>" . ucfirst($this->type) . ": $this->id </h3>";
        $result[] = ($this->NSFW) ? "<h4 class='NSFW'>NSFW</h4>": '';
        $result[] = ($this->NSFW) ? "</hgroup>": '';

        /* format the url according to the $linkType */
        switch ($linkType) {
            case self::LINK:
                $link = "/CVH/view/card/$this->type/$this->id";
                $result[] = "<a class='cardlink' href='$link'>$this->text</a>";
                break;
            case self::VOTE:
                $link = "/CVH/vote/$cardId-$voteId";
                $result[] = $this->text;
                break;
            case NULL:
                $result[] = $this->text;
                break;
        }

        $result[] = $this->source->display($this->type);
        
        $result[] = "</article>";
        
        if ($linkType == self::VOTE) {
            $result[] = "<form method='post' action='/CVH/vote/vote' name='$cardId'>";
            $result[] = "<input type='hidden' name='answer'   value='$cardId'>";
            $result[] = "<input type='hidden' name='question' value='$voteId'>";
            $result[] = "<noscript><input type='submit' name='submit'   value='vote'></noscript>";
            $result[] = "</form>";
        }
        
        $results = implode(PHP_EOL, $result);
        return $results;
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

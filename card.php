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
    const QUESTION = 'question';
    const ANSWER = 'answer';
    const HEX = 'hex';
    const DECIMAL = 'decimal';
    
    function Card($type) {
        $this->type = $type;
    }
    
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
    
    private function retrieveCard() {
        $mysqliLink = $this->dbConnect();
        
        /* the DB we query (questions or answers) is plural. So it should be
         * equal to the type (question or answer) variable plus an s. */
        $table = $this->type . 's';

        /**
         *  Note that $db.$type should be equal to 'questions.question' or
         * or 'answers.answer'
         * @todo Consider merging to a single table?
         */
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
        $data   = mysqli_fetch_assoc($result);

        /* Assign the data to class varaibles. Technically we could use the
         * array instead as is (which was the orginial implementation), but
         * decided this way was cleaner. */
        $this->id        = $data['id'];
        $this->text      = $data['text'];
        $this->NSFW      = $data['NSFW'];
        $this->source    = $data['source'];
        $this->sourceURL = $data['url'];
    }
    
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
        
        $result = mysqli_query($mysqliLink, $sourceInsert);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $sourceInsert . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        
        $table = $this->type . 's';
        
        /* this should return us the id of the Source */
        $sourceId = mysqli_insert_id($mysqliLink);
        
        $cardInsert = "INSERT INTO `$table` (`text`,        `NSFW`,        `source_id`)" . ' '
                    . "VALUES               ('$this->text', '$this->NSFW', '$sourceId' )";
        
        $result = mysqli_query($mysqliLink, $cardInsert);
        
        if (!$result) {
            echo "QUERY:" . ' ' . $cardInsert . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        
        /* get the id of the card created */
        $cardId = mysqli_insert_id($mysqliLink);
        
        return $cardId;
    }
    
    public function getCard($id) {
        if (empty($id)) { return FALSE; }

        /* set the details */
        $this->id   = $id;
        
        return $this->retrieveCard();
    }
    
    public function randomCard($NSFW = FALSE) {
        /* set the details */
        $this->NSFW = $NSFW;
        $this->id   = self::RANDOM_CARD;
        
        return $this->retrieveCard();
    }
    
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
    *
    * @param   string  $voteURL    the first part of the URL that should be
    *  used for voting, if desired. The id of the card will be appended to it.
    * @return  string  HTML code for the card.
    */
    public function displayCard($voteURL = NULL) {
        $classes[] = 'card';
        $classes[] = $this->type;

        if ($this->NSFW)      { $classes[] = 'NSFW'; }
        if ($voteURL != NULL) { $classes[] = 'vote'; }

        $class = implode(' ', $classes);
        $result .= "<div class=\"$class\" id=\"" . $this->getId(self::DECIMAL) . "\">";

        /* if NSFW, add the tag */
        if ($this->NSFW) {
            $result .= "<div class=\"NSFWtag NSFW\">NSFW</div>";
        }

        /**
        * @todo Possibly add code for handling voting on questions?
        */
        if ($voteURL != NULL) {
            /* if we recieved a vote URL, embeded the card text inside a voting link */
            $result .= "<a class='answerlink' href='" . $voteURL . $this->getId(self::HEX) . "'>";
            $result .= $this->text;
            $result .= "</a>";
        } else {
            /* if we didn't recieve a vote URL, just spit out the card text. */
            $result .= $this->text;
        }

        $result .= '<a href="' . $this->sourceURL . '" class="source">';

        switch ($this->source) {
            case 'Cards Against Humanity':
                $result .=  "<img src=\"/CVH/CAH-Cards-$this->type.svg\" alt=\"Cards Against Humanity\" />";
                break;
            case 'Cards vs Humans':
                $result .= '<img src="/CVH/CVH_Logo.svg" alt="Cards vs Humans" />';
                break;
        }

        $result .= $this->source . '</a>';
        $result .= '</div>';

        return $result;
    }

    public static function permURL($question, $answer) {
        $permURL = "http://" . $_SERVER['HTTP_HOST'] . "/CVH/display/" .
                   $question->getID(Card::HEX) . "-" . $answer->getID(Card::HEX);

        return $permURL;
    }
    
    public static function numVotes($question, $answer) {
        /* get connection to DB */
        $mysqliLink = self::dbConnect();
        
        $voteQuery  = "SELECT * FROM questions_answers_votes" . ' ' .
                      "WHERE question_id = " . $question->getId(card::DECIMAL) . ' ' .
                      "AND answer_id = " . $answer->getId(card::DECIMAL);
        $result = mysqli_query($mysqliLink, $voteQuery);
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
    
    public function getId($format = self::DECIMAL) {
        if ($format == self::DECIMAL) {
            $id = $this->id;
        }

        if ($format == self::HEX) {
            $id = strtoupper(dechex($this->id));
        }

        return $id;
    }
    
    public function getType() {
        return $this->type;
    }
}
?>

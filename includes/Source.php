<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Item.php');

/**
 * Class for a Card Source
 *
 * @author MaxMahem
 */
class Source extends Item {
    protected $source;
    protected $url;
    protected $questionCards;
    protected $answerCards;
    
    public function display($type = NULL) {
        $result .= "<address><a title='source' href='$this->url' rel='author'>";

        switch ($this->source) {
            case 'Cards Against Humanity':
                $result .=  "<img src=\"/CVH/CAH-Cards-$type.svg\" alt=\"Cards Against Humanity\" />";
                break;
            case 'Cards vs Humans':
                $result .= '<img src="/CVH/CVH_Logo.svg" alt="Cards vs Humans" />';
                break;
        }

        $result .= $this->source . '</a></address>';
        
        return $result;
    }
    
    public function getCards($NSFW = FALSE, $unvalidated = FALSE) {
        $this->questionCards = new CardSet(Card::QUESTION, $NSFW, $unvalidated);
        $this->questionCards->getSource($this);
        
        $this->answerCards   = new CardSet(Card::ANSWER,   $NSFW, $unvalidated);
        $this->answerCards->getSource($this);
    }

    public function add($source = 'Anonymous', $url = NULL) {
        if (!is_string($source)) {
            throw new InvalidArgumentException("Non string $source passed.");
        }
        $this->source = $source;
        
        /* check for empty or invalid URL, set to null if so */
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = NULL;
        } else {
            $this->url = $url;
        }
        
        /* get connection to DB */
        $mysqli = $this->dbConnect();

        /* Insert the source into the DB. The first part is standard, but if we
         * hit an address that isn't unique, we do some magic. id=LASTER_INSERT_ID(id)
         * shouldn't make any change to the DB, but will arrange our values such
         * that mysqli_insert_id will return the id of the duplicate value. */
        $insert = "INSERT INTO `sources` (`source`,        `url`)" . ' '
                . "VALUES                ('$this->source', '$this->url')" . ' '
                . "ON DUPLICATE KEY UPDATE `id` = LAST_INSERT_ID(`id`)";
        
        /* get the data */
        $result = $mysqli->query($insert);
                
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $insert", $mysqli->errno);
        }
        
        /* insert is done, get id */
        $this->id = $mysqli->insert_id;
        
        return $this->id;
    }

    public function retrieve() {
        $mysqli = $this->dbConnect();
        
        $select = "SELECT `sources`.`source`, `sources`.`url`";
        $from   = "FROM   `sources`";
        $where  = "WHERE  `sources`.`id` = $this->id";

        $query = $select . ' ' . $from . ' ' . $where;

        /* get the data */
        $result = $mysqli->query($query);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $query", $mysqli->errno);
        }
        
        /* set data */
        $row = mysqli_fetch_assoc($result);
        
        $this->source = $row['source'];
        $this->url    = $row['url'];
    }
}

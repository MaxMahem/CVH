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


    protected function retrieve() {
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
    
    protected function insert() { }
}

?>

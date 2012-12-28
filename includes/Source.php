<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/CardSet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/CVH/includes/Item.php');

/**
 * Class for a Card Source
 *
 * @author MaxMahem
 */
class Source {
    private $source;
    private $url;
    private $questionCards;
    private $answerCards;
    
    /** Source constructor
     * Creates a source. Id is required, if source & url are not provided, it will be fetched.
     * 
     * @param int    $id     id of source. Required.
     * @param string $source Name of the source.
     * @param string $url    for the source (if any).
     */
    function Source($id, $source = NULL, $url = NULL) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Non numeric id: $id passed to new Source");
        }
        
        $this->id = $id;
        
        /* if we didn't get a $source, then we need to retrieve it (and maybe the url) */
        if (empty($source)) {
            $this->retrieve();
        } else {
            $this->source = $source;
            $this->url    = $url;
        }
    }
    
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
}

?>

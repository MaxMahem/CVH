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
    private $url;

    const RANDOM_CARD = -1;
    const QUESTION = 'question';
    const ANSWER = 'answer';
    const HEX = 'hex';
    const DECIMAL = 'decimal';

    function Card($mysqlLink, $type, $id = self::RANDOM_CARD, $NSFW = FALSE, $maxAnswers = 1) {
        $this->type = $type;

        /* the DB we query (questions or answers) is plural. So it should be
         * equal to the type (question or answer) variable plus an s. */
        $table = $type . 's';

        /**
         *  Note that $db.$type should be equal to 'questions.question' or
         * or 'answers.answer'
         * @todo Consider merging to a single table?
         */
        $select = "SELECT $table.id, $table.$type, $table.NSFW, sources.source, sources.url";
        $from   = "FROM   $table INNER JOIN sources ON $table.source_id = sources.id";

        /* having a do nothing whereClause makes later logic easier. We don't have to evaluate
         * for empty whereClauses, we can implode them all. */
        $whereClauses[] = 'TRUE';

        /* If we are asked for a question, limit the number of answers to maxAnswers */
        if ($type == self::QUESTION) {
            $whereClauses[] = "$table.number_of_answers <= $maxAnswers";
        }

        /* If NSFW is true, and we didn't get a specific card, then want to
         * include NSFW entries, and we nothing needs to be done. Otherwise we
         * need to add this clause. Applies to both questions and answers. */
        if (($NSFW == FALSE) AND ($id == self::RANDOM_CARD)) {
            $whereClauses[] = "$table.NSFW = FALSE";
        }

        /* if we got a specific id, we want to return that row specifically. */
        if ($id != self::RANDOM_CARD) {
            $whereClauses[] = "$table.id = $id";
        }

        /* build the where of the query. The different clauses get linked by AND */
        $where = "WHERE " . implode(' AND ', $whereClauses);

        /* if we get a id of 0, we want a random result, do this with an order by rand() statment. */
        if ($id == self::RANDOM_CARD) {
            $order = "ORDER BY RAND() LIMIT 0,1";   /* random result */
        } else {
            $order = "";
        }

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order;

        /* get the data */
        $result = mysqli_query($mysqlLink, $query);
        $data   = mysqli_fetch_assoc($result);

        /* Assign the data to class varaibles. Technically we could use the
         * array instead as is (which was the orginial implementation), but
         * decided this way was cleaner. */
        $this->id        = $data['id'];
        $this->text      = $data[$type];
        $this->NSFW      = $data['NSFW'];
        $this->source    = $data['source'];
        $this->sourceURL = $data['url'];
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

        $result .= '<a href="' . $this->url . '" class="source">';

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

    public function getId($format = self::DECIMAL) {
        if ($format == self::DECIMAL) {
            $id = $this->id;
        }

        if ($format == self::HEX) {
            $id = strtoupper(dechex($this->id));
        }

        return $id;
    }
}
?>

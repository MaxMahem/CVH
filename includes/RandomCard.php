<?php


/**
 * Description of RandomCard
 *
 * @author MaxMahem
 */
class RandomCard extends Card {
    
    private $validated;
    private $seed;
    
    /** RandomCard($type) constructor
     * Creates a new card. Type and id are required.
     * 
     * @param string $type type of card, either Card::QUESTION or Card::ANSWER
     * @param int    $id   id of card to get or Card::RANDOM for random card
     * @param bool   $NSFW return NSFW cards or not. Default false.
     */
    public function RandomCard($type, $NSFW = FALSE, $validated = FALSE, $seed = NULL) {
        if (($type != self::QUESTION) && ($type != self::ANSWER)) {
            throw new InvalidArgumentException("Invalid type: $type passed to new RandomCard");
        }
        if (!is_bool($NSFW)) {
            throw new InvalidArgumentException("Non bool NSFW: $NSFW passed to new RandomCard");
        }
        
        $this->type = $type;
        $this->NSFW = $NSFW;
        $this->validated = $validated;
        $this->seed = $seed;
        
        $this->retrieve();
    }
    
    protected function retrieve() {
        $mysqli = $this->dbConnect();
        
        /* the DB we query (questions or answers) is plural. So it should be
         * equal to the type (question or answer) variable plus an s. */
        $table = $this->type . 's';
                
        /* build select from selectClauses array */
        $select = "SELECT `$table`.`id`";
        $from   = "FROM   `$table`";

        /* having a do nothing whereClause makes later logic easier. We don't have to evaluate
         * for empty whereClauses, we can implode them all. */
        $whereClauses[] = 'TRUE';
                
        /* Including this clause will exclude NSFW entries. */
        if ($this->NSFW == FALSE) {
            $whereClauses[] = "$table.NSFW = FALSE";
        }
        
//        if ($this->validated == FALSE) {
//            $whereClauses[] = "$table.Validated = FALSE";
//        }

        /* build the where of the query. The different clauses get linked by AND */
        $where = "WHERE" . ' ' . implode(' AND ', $whereClauses);

        $order = "ORDER BY RAND($seed) LIMIT 0,1";   /* random result */

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $order;

        /* get the data */
        $result = mysqli_query($mysqli, $query);
        
        /* check for query errors */
        if (!$result) {
            echo "QUERY:" . ' ' . $query . PHP_EOL;
            echo "Errormessage: " . mysqli_error($mysqliLink) . PHP_EOL;
            return false;
        }
        $data = mysqli_fetch_assoc($result);

        /* Assign the data to class varaibles. */
        $this->id        = $data['id'];

        return parent::retrieve();
    }
}
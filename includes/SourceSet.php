<?php
/**
 * a set of sources.
 *
 * @author MaxMahem
 */
class SourceSet extends Set {
    
    public function SourceSet() {
        $this->dataType = 'Source';
    }

    public function getAll($start = '0', $number = '30') {        
        /* this query will get all the cards of the selected type */
        $select = "SELECT `sources`.`id`";      
        $from   = "FROM   `sources`";
        $where  = "WHERE  TRUE";
        $limit  = "LIMIT $start, $number";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $limit;

        $this->retrieve($query);
    }      
}
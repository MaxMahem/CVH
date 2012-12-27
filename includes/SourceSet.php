<?php
/**
 * a set of sources.
 *
 * @author MaxMahem
 */
class SourceSet implements IteratorAggregate {
    private $sources;
    
    /** SourceSet Constructor
     *
     */
    public function SourceSet() {
    }
    
    /**
     * Makes a sourceset iterable! Black magic as far as I'm concurned.
     * 
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->sources);
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw LogicException("Attempted to get SourceSet property $property which does not exist.");
        }
    }
    
    public function getAll($start = '0', $number = '30') {        
        /* this query will get all the cards of the selected type */
        $select = "SELECT `sources`.`id`";      
        $from   = "FROM   `sources`";
        $where  = "WHERE  TRUE";
        $limit  = "LIMIT $start, $number";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where . ' ' . $limit;

        $this->retrieveData($query);
    }    
    
    private function retrieveData($query) {       
        /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME
         * with their appropriate values, and should be located outside of the webroot  */
        require($_SERVER['DOCUMENT_ROOT'] . '/../db-connection.php');
        
        /* connect to DB */
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        if ($mysqli->connect_errno) {
            throw new mysqli_sql_exception("Error connecting to MySQL: $mysqli->connect_error", $mysqli->errno);
        }
        
        /* get the data */
        $result = $mysqli->query($query);
        
        /* check for query errors */
        if (!$result) {
            throw new mysqli_sql_exception("My SQL Query Error: $mysqli->error" . PHP_EOL
                                         . "QUERY: $query", $mysqli->errno);
        }
        
        /* get all the sources */
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $this->sources[$id] = new Source($id);
        }       
    }    
}
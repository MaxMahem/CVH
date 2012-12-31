<?php

/**
 * Description of Set
 *
 * @author MaxMahem
 */
class Set implements IteratorAggregate, Countable {    
    protected $data;
    protected $dataType;
    protected $table;
    protected $page;
    
    const COUNT = 25;

    public function Set($dataType, $page = 0) {
        $this->page  = $page;
        
        $this->dataType = $dataType;
        
        $table = $this->dataType . 's';
        
        /* this query will get all the cards of the selected type */
        $select = "SELECT `$table`.`id`";      
        $from   = "FROM   `$table`";
        $where  = "WHERE  TRUE";

        /* build the query */
        $query = $select . ' ' . $from . ' ' . $where;

        $this->retrieve($query);
    }

    /**
     * Makes a set itterable! We actually return a LimitIterator, latery we will
     * implement paging.
     * 
     * @return \ArrayIterator
     */
    final public function getIterator() {
        if (empty($this->data)) {
            return new ArrayIterator(array ());
        } else {
            $offset = $this->page * self::COUNT;
            
            $arrayIterator = new ArrayIterator($this->data);
            $limitIterator = new LimitIterator($arrayIterator, $offset, self::COUNT);
            return $limitIterator;
        }
    }
    
    /**
     * Makes the cardset countable.
     * 
     * @return int the number of cards in the set
     */
    final public function count() {
        return count($this->data);
    }
    
    final public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new LogicException("Attempted to get property $property which does not exist.");
        }
    }
        
    protected function retrieve($query) {       
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
        
        /* get all the cards's */
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $this->data[$id] = new $this->dataType($id);
        }       
    }    
}
<?php

abstract class Item {
    protected $id;
    protected $added;
    
    /** Item($id) constructor
     * Creates a new item, if given an id, tries and retrieves it.
     * 
     * @param int    $id
     */
    public function Item($id = NULL) {
        if (isset($id)) {
            $this->id = $id;
            $this->retrieve();
        }
    }
    
    final public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new LogicException("Attempted to get property $property which does not exist.");
        }
    }
    
    /** retrieveCard()
     * retrieve's an items data from the DB.
     * 
     * @return boolean  returns true on success, false on failure.
     */
    abstract public function retrieve();
    
    /** dbConnect()
     * Makes a connection to the database
     *
     * @return mysqli
     */
    final protected function dbConnect() {
        /* the db-connection file is assumed to define DBHOST, DBUSER, DBPASS, and DBNAME
         * with their appropriate values, and should be located outside of the webroot  */
        require($_SERVER['DOCUMENT_ROOT'] . '/../db-connection.php');
        
        /* connect to DB */
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        if ($mysqli->connect_errno) {
            throw new mysqli_sql_exception("Error connecting to MySQL: $mysqli->connect_error", $mysqli->errno);
        }
        
        return $mysqli;
    }
}
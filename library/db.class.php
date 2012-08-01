<?php

class Db {
    
    /**
     * @var mysqli
     */
    private $database = NULL;
    
    /**
     * Creates a new Db-Object and establishes Connection 
     */
    public function __construct() {
        $this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
    
    /**
	 * Establishes db connection
	 */
	private function connect($host, $user, $pwd, $dbname) {
        $this->database = mysqli_init();
		$this->database->real_connect($host, $user, $pwd, $dbname);
		if ($this->database === NULL || $this->database->connect_errno) {
            die("Failed to connect to database!");
		}
        $this->database->set_charset("utf8");
	}
    
    /**
     * Get the DB handle
     * 
     * @return mysqli
     */
    public function get() {
        return $this->database;
    }
}
?>

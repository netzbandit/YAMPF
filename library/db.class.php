<?php

class Db {
    
    private $dbHandle = 0;
    
    /**
     * Creates a new Db-Object and establishes Connection 
     */
    public function __construct() {
        $this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
    
    /**
	 * Establishes db connection
     * 
	 * @return boolean success?
	 */
	private function connect($address, $account, $pwd, $name) {
		$this->dbHandle = mysql_connect($address, $account, $pwd) or die("Can't connect!");
		if ($this->dbHandle != 0) {
			if (mysql_select_db($name, $this->dbHandle)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
    
    /**
     * Get the DB handle
     * 
     * @return int
     */
    public function get() {
        return $this->dbHandle;
    }
    
}

?>

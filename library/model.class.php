<?php

/**
 * Base class for SPPOs (self persistent php obejcts)
 * 
 * This class provides basic funtionallity for DB acces
 * (read, insert, ubdate)
 * New tables can created easily by extending this class
 * 
 * @author Stephan Lachmuth
 * 
 */
abstract class Model {

	protected $_dbHandle;
	protected $dataTypes = array();
	protected $data = array();

	/**
	 * Default constructor. Initializes the object
	 */
	public function __construct() {
		global $database;
        
        $this->_dbHandle = $database->get();
		$this->initClass();
	}

	/** GETTER */
	public function __get($name) {
		$enc = false;
		$nbsp = false;
		if (stristr($name, "__enc")) {
			$enc = true;
			$name = str_ireplace("__enc", "", $name);
		}

		if (stristr($name, "__nbsp")) {
			$nbsp = true;
			$name = str_ireplace("__nbsp", "", $name);
		}

		if (isset($this->data[$name])) {
			$ret = $this->data[$name];

			if ($enc === true) {
				$ret = htmlentities(trim($ret));
			}
			if ($nbsp === true) {
				$ret = str_replace(' ', '&nbsp;', trim($ret));
			}

			return $ret;
		} else {
			return null;
		}
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	/* ------ abstract methods ------ */

	/**
	 * Initializes table columns as fields
	 */
	public abstract function initClass();

	/**
	 * Creates a new object
	 * @return Model a new object.
	 */
	public abstract function createObject();

	/**
	 * Returns the "WHERE" clause for the primary key
	 * (i.e.: "id=1")
	 * @return string the WHERE clause for the PK
	 */
	public abstract function getPkWhere();

	/**
	 * Checks if the object has the PK (id) set
	 */
	public abstract function hasId();

	/* ------ methods that can be overridden ------ */

	/**
	 * Returns the ORDER BY clause
	 * z.B. "lastname ASC, createdate DESC"
	 * @return string ORDER BY Clause
	 */
	public function getOrderBy() {
		return "";
	}
	
    /**
     * Returns the table name
     * @return string the table name
     */
	public function getTableName() {
		return strtolower(get_class($this))."s";
	}

	/**
	 * Should the field "id" be set after save?
	 * @return boolean
	 */
	public function needsIdAfterSave() {
		return false;
	}

	/**
	 * Additional checks/tasks before deleting the object from DB.
     * If this functions returns false, the object will NOT be deleted.
	 * @return boolean
	 */
	public function beforeDelete() {
		return true;
	}

	/**
	 * Additional tasks after deleting the object from DB.
	 */
	public function afterDelete() {
		// nope
	}

	/* ------ final methods ------ */

	/**
	 * Dhecks if the object is already in the DB.
	 * @return boolean
	 */
	public final function existsInDB() {

		if ($this->hasId()) {
			$all = $this->fetchAll($this->getPkWhere());
			if (count($all) > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stores the object in DB (save or update)
	 */
	public final function persist() {
		if ($this->existsInDB()) {
			$this->update();
		} else {
			$this->save();
		}
	}

	/**
	 * Deletes the object from the DB.
	 */
	public final function delete() {
		if ($this->beforeDelete() === true) {
			$sql = "DELETE FROM " . $this->getTableName() . " WHERE " . $this->getPkWhere();
			@mysql_query($sql);
		}

		$this->afterDelete();
	}

	/** 
     * Saves a new object in the DB.
     */
	private final function save() {
		$columnnames = "";
		$values = "";
		foreach ($this->dataTypes as $col => $type) {
			$columnnames .= "," . $col;
			if (isset($this->data[$col])) {
				$values .= "," . $this->getDataPart($this->data[$col], $type);
			} else {
				$values .= ",NULL";
			}
		}
		$columnnames = substr($columnnames, 1);
		$values = substr($values, 1);

		$sql = "INSERT INTO " . $this->getTableName() . " (" . $columnnames . ") VALUES (" . $values . ")";
		$res = @mysql_query($sql);
		if ($this->needsIdAfterSave() === true) {
			$this->data['id'] = mysql_insert_id();
		}

		if (mysql_errno() > 0) {
			addDebug(mysql_error());
		}
	}

	/**
     * Stores an existing object in the DB.
     */
	private final function update() {
		$updatestring = "";
		foreach ($this->dataTypes as $col => $type) {
			$updatestring .= "," . $col . "=";
			if (isset($this->data[$col])) {
				$updatestring .= $this->getDataPart($this->data[$col], $type);
			} else {
				$updatestring .= "NULL";
			}
		}

		$updatestring = substr($updatestring, 1);

		$sql = "UPDATE " . $this->getTableName() . " SET " . $updatestring . " WHERE " . $this->getPkWhere();

		$res = @mysql_query($sql);

		if (mysql_errno() > 0) {
			addDebug(mysql_error());
		}
	}

	/**
     * Creates a new SQL-part for this field.
     * (Only adds ' around string typed fields)
     */
	private final function getDataPart($data, $type) {
		$ret = "";
		switch ($type) {
			case 'number':
				$ret = $data;
				break;
			case 'string':
				$ret = "'". mysql_real_escape_string($data) ."'";
				break;
			default:
				$ret = "'". mysql_real_escape_string($data) ."'";
		}

		return $ret;
	}

	/**
	 * Fills an object with DB data.
	 */
	public final function fill($row) {
		foreach ($row as $key => $value) {
            $this->data[$key] = $value;
		}
	}


    /**
     * Searches for objects matching the query.
     * @param string $query a complete SQL query
     * @return array List of objects (or empty list) 
     */
    public final function fetchAllQuery($query) {
		$res = mysql_query($query);

		$list = array();
		while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
			$obj = $this->createObject();
			$obj->fill($row);
			$list[] = $obj;
		}
		return $list;
		
	}
	
	/**
     * Searches for objects matching the query.
     * @param string $where WHERE clause (without WHERE)
     * @return array List of objects (or empty list) 
	 */
	public final function fetchAll($where = "") {
		$query = "SELECT * FROM " . $this->getTableName();
		if ($where != "") {
			$query .= " WHERE " . $where;
		}
		$orderby = $this->getOrderBy();
		if ($orderby != "") {
			$query .= " ORDER BY " . $orderby;
		}

		return $this->fetchAllQuery($query);
	}

	/**
	 * Fetches an object from the DB.
     * @param string $where WHERE clause. If omitted, the object is fetched by the PK.
	 * @return boolean indicating something is found.
	 */
	public final function fetch($where = null) {
		if($where === null) {
			$query = "SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPkWhere();
		} else {
			$query = "SELECT * FROM " . $this->getTableName() . " WHERE " . $where;
		}
		
		$result = mysql_query($query);
		if($result === false) {
			return false;
		}
		if ($row = mysql_fetch_assoc($result)) {
			$this->fill($row);
			return true;
		}
		return false;
	}


}

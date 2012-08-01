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

    /**
     * @var mysqli
     */
    protected $db;
    protected $dataTypes = array();
    protected $data = array();

    /**
     * Default constructor. Initializes the object
     */
    public function __construct() {
        global $database;

        $this->db = $database->get();
        $this->initClass();
    }

    /** GETTER */
    public function __get($name) {
        $enc = FALSE;
        $nbsp = FALSE;
        if (stristr($name, "__enc")) {
            $enc = TRUE;
            $name = str_ireplace("__enc", "", $name);
        }

        if (stristr($name, "__nbsp")) {
            $nbsp = TRUE;
            $name = str_ireplace("__nbsp", "", $name);
        }

        if (isset($this->data[$name])) {
            $ret = $this->data[$name];

            if ($enc === TRUE) {
                $ret = htmlentities(trim($ret));
            }
            if ($nbsp === TRUE) {
                $ret = str_replace(' ', '&nbsp;', trim($ret));
            }

            return $ret;
        } else {
            return NULL;
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
        $tmp = strtolower(get_class($this));
        if (TRUE === str_ends_with($tmp, "y")) {
            $tmp = substr($tmp, 0, -1);
            $tmp .= "ies";
        } else {
            $tmp .= "s";
        }
        return DB_PREFIX . $tmp;
    }

    /**
     * Should the field "id" be set after save?
     * @return boolean
     */
    public function needsIdAfterSave() {
        return FALSE;
    }

    /**
     * Additional checks/tasks before deleting the object from DB.
     * If this functions returns false, the object will NOT be deleted.
     * @return boolean
     */
    public function beforeDelete() {
        return TRUE;
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
                return TRUE;
            }
        }

        return FALSE;
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
        if ($this->beforeDelete() === TRUE) {
            $sql = "DELETE FROM " . $this->getTableName() . " WHERE " . $this->getPkWhere();
            $this->db->query($sql);
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
        $this->db->query($sql);
        if ($this->needsIdAfterSave() === TRUE) {
            $this->data['id'] = $this->db->insert_id;
        }

        if ($this->db->errno > 0) {
            addDebug($this->db->error);
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

        $this->db->query($sql);

        if ($this->db->errno > 0) {
            addDebug($this->db->error);
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
                $ret = "'" . $this->db->escape_string($data) . "'";
                break;
            default:
                $ret = "'" . $this->db->escape_string($data) . "'";
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
        addDebug($query);
        $result = $this->db->query($query);
        $list = array();
        if ($result !== false && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $obj = $this->createObject();
                $obj->fill($row);
                $list[] = $obj;
            }
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
    public final function fetch($where = NULL) {
        if ($where === NULL) {
            $query = "SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPkWhere();
        } else {
            $query = "SELECT * FROM " . $this->getTableName() . " WHERE " . $where;
        }

        $result = $this->db->query($query);
        if ($result === false || $result->num_rows == 0) {
            return FALSE;
        }
        $row = $result->fetch_assoc();
        if ($row !== NULL) {
            $this->fill($row);
            return TRUE;
        }
        return FALSE;
    }

}

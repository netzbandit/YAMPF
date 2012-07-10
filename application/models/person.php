<?php
/**
 * Example class for DB access.
 * 
 * The table (persons) would consist of the fields
 * - id (auto increment)
 * - first_name
 * - last_name
 * - age 
 *
 * @see Model
 * @author stephan
 */
class Person extends Model {
    
   // only the abstract methods are implemented 
    
    /**
     * This method has to return an object
     * of this very class.
     * 
     * @return \Person 
     */
    public function createObject() {
        return new Person();
    }
    
    /**
     * Here you create the where clause for
     * selecting an object by the primary key.
     * 
     * @return string the where clause 
     */
    public function getPkWhere() {
        return 'id=' . $this->data['id'];
        
    }
    
    /**
     * Checks if an id is set (that means if the object is already
     * stored in the db or not)
     * 
     * @return bool 
     */
    public function hasId() {
        return (isset($this->data['id']) && $this->data['id'] > 0);
    }
    
    /**
     * In this function you define all data fields of
     * the object (== the columns of the table)
     * 
     * For each field you have to specify the type.
     * Allowed are only string and number. They only differ
     * in the creation of the insert/update statements (having
     * quotes around them or not). 
     */
    public function initClass() {
        $this->dataTypes = array(
            'id'         => 'number',
            'first_name' => 'string',
            'last_name'  => 'string',
            'age'        => 'number'
        );
    }
}

?>

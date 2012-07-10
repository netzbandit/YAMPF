<?php
/**
 * Example class for DB access.
 * The table would consist of the fields
 * - id (auto increment)
 * - first_name
 * - last_name
 * - age 
 *
 * 
 * @author stephan
 */
class Person extends Model {
    
   // only the abstract methods are implemented 
    
    public function createObject() {
        return new Person();
    }
    public function getPkWhere() {
        return 'id=' . $this->data['id'];
        
    }
    public function hasId() {
        return (isset($this->data['id']) && $this->data['id'] > 0);
    }
    public function initClass() {
        $this->dataTypes = array();
    }
}

?>

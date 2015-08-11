<?php
    /**
     * This file contains the SQL::MySQL::Result::Change class
     * 
     * @author Gerd Rönsch
     * @package MySQL
     */
    
    namespace SQL\MySQL\Result {
        
        class Change extends \SQL\Result\Change {

            /**
             * Get the number of affected rows in the pervious executed query.
             * 
             * @return integer
             */
            public function countAffectedRows()
            {
                return $this->_resource;
            }

            public function init() {}

        }
    }    
?>

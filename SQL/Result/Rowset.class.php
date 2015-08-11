<?php
    /**
     *
     *
     */
    
    namespace SQL\Result {
        
        abstract class Rowset extends \SQL\Result {

            protected $_fetch_mode = \SQL\FETCHMODE_OBJECT;


            public function setFetchmode( $mode )
            {
                $this->_fetch_mode = $mode;
            }
            
            public abstract function countRows();

            public abstract function countColumns();

            public abstract function fetchAll();

            public abstract function fetch();

            public function getFetchmode() {
                return $this->_fetch_mode;
            }

        }

    }    
?>

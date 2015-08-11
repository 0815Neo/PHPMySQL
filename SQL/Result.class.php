<?php
    /**
     *
     *
     */
    
    namespace SQL {
        
        abstract class Result {
            
            protected $_resource = NULL;
            protected $_query = NULL;

            public function __construct( $resource, $query )
            {
                $this->_resource = $resource;
                $this->_query = $query;

                $this->init();
            }

            public function getResource()
            {
                return $this->_resource;
            }

            public function getQuery()
            {
                return $this->_query;
            }

            abstract function init();

        }

    }    
?>

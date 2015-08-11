<?php
    /**
     *
     *
     */
    
    namespace SQL {

        abstract class Table implements \SQL\TableDataGateway{

            protected static $_schema = NULL;
            protected static $_name = NULL;
            protected static $_primaries = array();
            protected $_link = NULL;



            public function __construct( \SQL\ConnectionAdapter $link )
            {
                $this->_link = $link;

                if( NULL === static::$_name )
                {
                    throw new Exception( 'The static protected property $_name must be defined in the child class!' );
                }

                if( NULL === static::$_schema )
                {
                    throw new Exception( 'The static protected property $_schema must be defined in the child class!' );
                }

                $this->init();
            }

            protected abstract function init();

        }

    }    
?>

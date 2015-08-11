<?php
    /**
     *
     *
     */
    
    namespace SQL {
        
        abstract class ConnectionAdapter {
            
            protected $_flags = array();

            protected $_link = NULL;

            const DEBUG_FILE_NAME = '/tmp/sql.log';

            
            const STATE_NOT_CONNECTED =     -1;
            const STATE_CONNECTED =          0;



            /**
             * Calls the connect(_persistent) method of his child
             *
             * @param array $properties Defined at child class
             * @param boolean $persistent Call connect or connect_persistent
             * @return \SQL\ConnectionAdapter or NULL on failure
             */
            public function __construct( array $properties, $persistent = false ) {

                if( $persistent )
                {
                    return $this->connect_persistent( $properties ); 
                }
                else
                {
                    return $this->connect( $properties );
                }

            }

            abstract public function connect( array $properties );
            abstract public function connect_persistent( array $properties );
            abstract public function disconnect();
            abstract public function selectSchema( $schema );
            abstract public function executeQuery( $query );
            abstract public function getConnectionInfo();
            abstract public function getConnectionState();
            abstract public function getLastErrorMessage();
            abstract public function getLastErrorCode();
            abstract public function getErrorMessage( $code );
            abstract public function escape( $str );
            abstract public function getSelectedSchema();

            /**
             * Shows a debug message.
             * You can turn on the display with the flag FLAG_DEBUG.
             * The format is defined with FLAG_DEBUG_MODE_*.
             *
             * @param string $message
             */
            protected function debug( $message )
            {
                if( array_key_exists( FLAG_DEBUG, $this->_flags ) )
                {
                    if( array_key_exists( FLAG_DEBUG_MODE_CLI, $this->_flags ) )
                    {
                        printf( "Debug Message: %s\n", $message );
                    }

                    if( array_key_exists( FLAG_DEBUG_MODE_FILE, $this->_flags ) )
                    {
                        file_put_contents( self::DEBUG_FILE_NAME, sprintf( "%s %s\n", date( "c"), $message ), FILE_APPEND );
                    }

                    if( array_key_exists( FLAG_DEBUG_MODE_HTML, $this->_flags ) )
                    {
                        printf( "<p><b>Debug Message: </b> %s</p>\n", $message );
                    }
                }
            }

            /**
             * Set a flag (FLAG_*)
             *
             * @param integer $flag SQL\FLAG_*
             */
            public function setFlag( $flag ) {
                $this->_flags[$flag] = true;
            }

            /**
             * Sets multiple flags at once.
             * Call the method setFlag.
             *
             * @param array $flags
             */
            public function setFlags( array $flags ) {
                foreach( $flags as $flag )
                {
                    $this->setFlag( $flag );
                }
            }

            /**
             * Remove flag.
             *
             * @param integer $flag SQL\FLAG_*
             */
            public function removeFlag( $flag ) {
                if( $this->isSetFlag( $flag ) )
                {
                    unset( $this->_flags[$flag] );
                }
            }

            /**
             * Removes multible flags at once.
             * Call the method removeFlag
             *
             * @param array $flags
             */
            public function removeFlags( array $flags ) {
                foreach( $flags as $flag )
                {
                    $this->removeFlag( $flag );
                }
            }

            private function isSetFlag( $flag )
            {
                return array_key_exists( $flag, $this->_flags );
            }


        }

    }    
?>

<?php
    /**
     * This file contains the SQL::Connection::Adapter class.
     *
     * @author Gerd Rönsch
     */

    namespace SQL\MySQL {
        
        /**
         * A implementation of the SQL::ConnectionAdapter for MySQL. It use the mysqli.
         */
        class ConnectionAdapter extends \SQL\ConnectionAdapter {

            /** List the required properties. */
            private static $_required_properties = array(
                'host',
                'user',
                'user_password',
                'schema'
            );

            /** List the standard properties with values. */
            private static $_std_properties = array(
                'charset' => 'utf8',
                'port' => 3306
            );



            /**
             * Connect to a MySQL server.
             * 
             * @param array $properties 
             *  The array keys are the name of the property.\n 
             *  Properties are: 
             *  - host
             *  - user
             *  - user_password
             *  - schema.
             *  - charset (default: string(utf8))
             *  - port (default: int(3306))
             * @throws SQL::MySQL::Exception If the connecting wasn't sucessfully
             * @throws SQL::MySQL::Exception If the method wasn't able to set the charset
             */
            public function connect( array $properties )
            {   
                self::check_properties( $properties );

                $this->_link = @new \mysqli( 
                    $properties['host'], 
                    $properties['user'], 
                    $properties['user_password'], 
                    $properties['schema'], 
                    $properties['port'] 
                ); 

                if( $this->_link->connect_error )
                {
                    throw new Exception( mysqli_connect_error( $this->_link ), mysqli_connect_errno( $this->_link ) );
                }

                if( !$this->setCharset( $properties['charset'] ) )
                {
                    throw new Exception( "Was not able to set the charset!" );
                }
            }



            /**
             * Connect to a MySQL server persistent. For documentation see SQL::MySQL::ConnectionAdapter::connect
             */
            public function connect_persistent( array $properties ) {
                self::check_properties( $properties );

                $properties['host'] = sprintf( "p:%s", $properties['host'] );

                return $this->connect( $properties );
            }



            /**
             * Disconnect from MySQL server.
             */
            public function disconnect()
            {
                $this->_link->close();
            }



            /**
             * Chose the default schema (database).
             *
             * @retval bool 
             *  Returns true on success or false on failure.
             */
            public function selectSchema( $schema )
            {
                return $this->_link->select_db( $schema );
            }


            
            /**
             * Send a query to the database server for execution.
             *
             * @param string $query
             * @retval SQL::Result
             * If the query was an SELECT statement a SQL::MySQL::Result::Rowset ist returned.\n If the query was successfully executed and was an UPDATE, DELETE or an INSERT statement the method return a SQL::MySQL::Result::Change.\n Return NULL on failure.
             */
            public function executeQuery( $query )
            {
                $this->debug( $query );

                $res = $this->_link->query( $query );

                if( $res === true )
                {
                    return new \SQL\MySQL\Result\Change( $this->_link->affected_rows, $query );
                }
                else if( $res instanceof \mysqli_result )
                {
                    return new \SQL\MySQL\Result\Rowset( $res, $query );
                }
                else
                {
                    return NULL;
                }
            }



            /**
             * Return informations about the current connection.
             * If you not installed mysqlnd, the method will not work and throw an Exception.
             *
             * @retval array 
             * @throws \SQL\MySQL\Exception If mysqlnd is not installed
             */
            public function getConnectionInfo()
            {
                if( !function_exists( "mysqli_get_connection_stats" ) )
                {
                    throw new Exception( "This method is only available if you installed mysqlnd!" );
                }

                return mysqli_get_connection_stats( $this->_link );
            }



            /**
             * Return the current connection state.
             *
             * @retval integer SQL::ConnectionAdapter::STATE_CONNECTED or SQL::ConnectionAdapter::STATE_NOT_CONNECTED
             */
            public function getConnectionState()
            {
                if( @$this->_link->ping() === true )
                {
                    return \SQL\ConnectionAdapter::STATE_CONNECTED;
                }
                else
                {
                    return \SQL\ConnectionAdapter::STATE_NOT_CONNECTED;
                }
            }



            /**
             * Return the last error message (generated by server)
             *
             * @retval string
             */
            public function getLastErrorMessage()
            {
                return $this->_link->error;
            }



            /**
             * Return the last error code (returned from server)
             *
             * @retval integer
             */
            public function getLastErrorCode()
            {
                return $this->_link->errno;
            }



            /**
             * This method is not available in this Adapter
             *
             * @param integer $code
             * @throw \SQL\MySQL\Exception Everytime thrown
             */
            public function getErrorMessage( $code )
            {
                throw new Exception( "This method isn't useable with the MySQL Adapter" );
            }



            /**
             * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection.
             *
             * @param string $str
             * @retval string
             */
            public function escape( $str )
            {
                return $this->_link->real_escape_string( $str );
            }



            /**
             * This method is not available with this Adapter
             */
            public function getSelectedSchema()
            {
                throw new exception( "This method isn't useable with the MySQL Adapter" );
            }



            /**
             * Set the charset of the current connection.
             *
             * @param string $charset
             * @retval boolean 
             *  Returns true on success or false on failure.
             */
            public function setCharset( $charset ) 
            {
                return $this->_link->set_charset( $charset );
            }



            /**
             * Check the properties of the method SQL::MySQL::ConnectionAdapter::connect.
             * Adds also default values if they are not set automaticly.
             *
             * @param array $properties
             */
            private static function check_properties( array &$properties )
            {
                foreach( self::$_required_properties as $property )
                {
                    if( !array_key_exists( $property, $properties ) )
                    {
                        throw new Exception( sprintf( "The property '%s' is not set!", $property ) );
                    }
                }
                
                foreach( self::$_std_properties as $property_name => $property_value )
                {
                    if( !array_key_exists( $property_name, $properties ) )
                    {
                        $properties[$property_name] = $property_value;
                    }
                }
            }


        }
    }    
?>

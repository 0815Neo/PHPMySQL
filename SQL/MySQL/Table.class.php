<?php
    /**
     * This file contains the SQL::MySQL::Table class
     * 
     * @author Gerd Rönsch
     * @package MySQL
     */
    
    namespace SQL\MySQL {
        
        abstract class Table extends \SQL\Table {

            /**
             * Store the column information.
             * 
             * @var array
             */
            protected $_columns = array();
            
            /**
             * @var array
             */
            protected $_insert_buffer = array();
            
            /**
             * @var array 
             */
            protected $_delete_buffer = array();

            protected $_insert_buffer_max_size = 1000;
            
            protected $_insert_buffer_size = 0;
            
            protected $_delete_buffer_max_size = 1000;
            
            protected $_delete_buffer_size = 0;



            /**
             * Initalise the child class.<br />
             * Get the columns an their informations from the table and saves it to the protected property $_columns.
             */
            public function init() 
            {
                $res = $this->_link->executeQuery( sprintf( 
                    "SELECT * FROM %s LIMIT 1;",
                    $this->getAbsoluteName()
                ) );


                if( NULL === $res || !($res instanceof \SQL\Result\Rowset) )
                {
                    throw new Exception( sprintf(
                        "Error while trying to get columns from table %s",
                        $this->getAbsoluteTableName
                    ) );
                }

                $this->_columns = $res->getColumns();

            }

            /**
             * Return an array of values representing a table line.<br /> 
             * The row is identified by the primary_key(s) value(s). You must specify all primary keys!
             * @code
             * $row = findRow( array( 'id' => 12 ), array( 'name' ) );
             * 
             * if( count( $row ) === 0 )
             * {
             *     printf( "No row was found!\n" );
             * }
             * else
             * {
             *     printf( "Row was found. The column name contains: %s\n", $row['name'] );
             * }
             * @endcode
             * 
             * @param array $key_values The array key(s) are the name of the primary key(s). Example [id] => int(8)
             * @param array $columns The columns that will be selected and returned
             * @throw SQL::MySQL::Exception If not all key_values are specified
             * @retval array  
             *  A table row. The array keys are the name of the table column. If nothing was found an empty array ist returned.
             */
            public function findRow( array $key_values, array $columns = array() )
            {
                $columns_str = "";

                // If the count of columns is an empty array, all columns will be selected
                if( count( $columns ) === 0 )
                {
                    $columns_str = "*";
                }
                else
                {
                    $columns_str = implode( ",", $columns_str );
                }


                // Execute query
                $res = $this->_link->executeQuery( sprintf( 
                    "SELECT %s FROM %s WHERE %s",
                    $columns_str,
                    $this->getAbsoluteName(),
                    $this->keysToCondition( $key_values )
                ) );


                // Check if result is correct (query succeed)
                if( $res === NULL or !($res instanceof \SQL\Result\Rowset) )
                {
                    return array();
                }


                // Check if the row count is 1 (Happens if not all primary key values are specified)
                if( $res->countRows() > 1 )
                {
                    throw new Exception( 
                        sprintf( "The result have mor than one row. You possible forgot to describe all primary keys in \$key_values" )
                    );
                }
                elseif( $res->countRows() === 1 )
                {
                    $ret = $res->fetchAll( \SQL\FETCHMODE_ARRAY_ASSOC );
                    
                    return $ret[0];
                }
                else
                {
                    return array();
                }
            }

            /**
             * Delete a row from a table.<br />
             * The row is identified by the primary_key(s) value(s). You must specifiy all primary keys or this method will return false.<br />
             * <b>Example Code:</b><br />
             * @code
             * if( deleteRow( array( 'id' => 201 ) ) === true )
             *     printf( "Row was successfully deleted\n" );
             * else
             *     printf( "Error while deleting row!\n" );
             * @endcode
             *
             * @param array $key_values The array key(s) are the name of the primary key(s).
             * @retval bool  
             *  Return true If the rows was successfully deleted. <br />Returns false if the row does not exists, or wasn't deleted or more than one row was affected.
             */
            public function deleteRow( array $key_values )
            {
                $res = $this->_link->executeQuery( sprintf( 
                    "DELETE FROM %s WHERE %s",
                    $this->getAbsoluteName(),
                    $this->keysToCondition( $key_values )
                ) );

                if( $res === NULL || !($res instanceof \SQL\Result\Change ) )
                {
                    return false;
                }

                if( $res->countAffectedRows() !== 1 )
                {
                    return false;
                }

                return true;
            }

            /**
             * Update a table row identified by the primary key(s).
             *
             * @param array $key_values The array key(s) are the name of the primary key(s). Example [id] => int(8).
             * @param array $values The new values. The array key is the name of the column and the array value is the new value of the column.
             * @retval bool
             *  Return true on succes. Return false on failure.
             */
            public function updateRow( array $key_values, array $values ) {
                $column_values_str = $this->valuesToStatement( $values );
                
                $res = $this->_link->executeQuery( sprintf(
                    "UPDATE %s SET %s WHERE %s",
                    $this->getAbsoluteName(),
                    $column_values_str,
                    $this->keysToCondition( $key_values )
                ) );

                if( $res === NULL || !($res instanceof \SQL\Result\Change ) )
                {
                    return false;
                }

                return true;
            }

            /**
             * Insert a new row into the table.
             * @code
             * if( $tbl->insertRow( array( 'id' => NULL, 'name' => "hans" ) ) )
             * {
             *     print( "Row was added!\n" );
             * }
             * else
             * {
             *     print( "Was not able to add row!\n" );
             * }
             * @endcode
             *
             * @param array $values The new values. The array key is the name of the column and the array value is the new value of the column.
             * @retval bool
             *  Return true on succes false on failure.
             */
            public function insertRow( array $values ) {
                $values_str = $this->valuesToStatement( $values );

                $res = $this->_link->executeQuery( sprintf( 
                    "INSERT INTO %s SET %s",
                    $this->getAbsoluteName(),
                    $values_str
                ) );
                
                if( $res === NULL || !($res instanceof \SQL\Result\Change) )
                {
                    return false;
                }

                return true;
            }

            /**
             * Insert multible rows.
             * @code
             * $err_msg = "";
             * $rows = array(
             *     array( 'id' => NULL, 'name' => "peter" ),
             *     array( 'id' => NULL, 'name' => "hans" )
             * );
             * 
             * if( $tbl->insertRows( $rows, $err_msg )
             * {
             *     printf( "Rows are added!\n" );
             * }
             * else
             * {
             *     printf( "Error occured: %s\n", $err_msg );
             * }
             * @endcode
             *
             * @param array $rows A array of values. The elements in the array have the following the format of the param of the method SQL::MySQL::Table::insertRow.
             * @param string &$err_msg Contains an error message if raised.
             * @retval bool
             *  Return true on success. Return false on failure
             */
            public function insertRows( array $rows, &$err_msg ) {
                foreach( $rows as $row )
                {
                    if( false === $this->insertRow( $row ) )
                    {
                        $err_msg = $this->_link->getLastErrorMessage();
                        return false;
                    }
                }


            }

            /**
             * Get rows from the current table as array.
             * @code
             * $res = $tbl->getRows( array('name'), order_by(), 10 );
             *
             * print_r( $res );
             * // Output:
             * // array(10) =>
             * //     [0] => array(
             * //         'name' => "hans"
             * //     ), 
             * //     [1] => array(
             * //         'name' => "peter"
             * //     ), ...
             * @endcode
             *
             * @param array $columns A list of columns that will be selected. If the array is empty, all columns will be selected.
             * @param array $order_by A list of columns that indicate the ordering. If the array is empty, no ordering is applied. The array keys are the name of the column and the value can be DESC or ASC. (lowercase is allowed).
             * @param integer $limit The max. number of rows that returned by the method. If 0 is entered, all lines returned.
             * @retval array
             *  The first dimension is the row number and the second deminsion is the column. (Fetchmode of rows: SQL::FETCHMODE_ARRAY_ASSOC
             */
            public function getRows( array $columns = array(), array $order_by = array(), $limit = 0 )
            {
                $limit_str = $this->limitToStatement( $limit );
                $order_str = $this->orderToStatement( $order_by );
                $columns_str = $this->columnsToStatement( $columns );
                
               
                $res = $this->_link->executeQuery( sprintf( 
                    "SELECT %s FROM %s %s %s",
                    $columns_str,
                    $this->getAbsoluteName(),
                    $order_str,
                    $limit_str
                ) );

                if( NULL === $res || !($res instanceof \SQL\Result\Rowset) )
                {
                    throw new Exception( $this->_link->getLastErrorMessage() );
                }

                return $res->fetchAll( \SQL\FETCHMODE_ARRAY_ASSOC );
            }

            /**
             * Check if a row exists.
             * 
             * @code
             * if( $tbl->rowExists( array( 'id' => 12 ) )
             * {
             *     printf( "Row exists!\n" );
             * }
             * else
             * {
             *     printf( "Row does'n exists!\n" );
             * }
             * @endcode
             * 
             * @param array $key_values The array key(s) are the name of the primary key(s). Example [id] => int(8)
             * @return boolean
             */
            public function rowExists( array $key_values ) {
                return 1 === count( $this->findRow( $key_values ) );
            }

            /**
             * Retun the columns of the table with informations. 
            * 
             * @return array Return an array of objects which contains field definition information or FALSE if no field information is available. (name, orgname, table, orgtable, max_length, length, charsetnr, flags, type, decimals).
             */
            public function getColumns()
            {
                return $this->_columns;
            }

            /**
             * Checks if the table has all of the specified columns.<br />
             * <b>Example Code:</b><br />
             * @code
             * $wrong_column = "";
             * $res = $obj->columnsExists( array( 'name', 'id' ), $wrong_column );
             *
             * if( $res === false )
             * {
             *     printf( "The column '%s' does not exists!", $wrong_column );
             * }
             * @endcode
             * 
             * @param array $columns An array with names of columns as values.
             * @param[out] string $wrong_column The name of the first column that not exists.
             *
             * @retval bool
             *  Return true if alle columns exists otherwise false.
             */
            public function columnsExists( array $columns, &$wrong_column )
            {
                foreach( $columns as $column )
                {
                    if( !array_key_exists( $column, $this->_columns ) )
                    {
                        $wrong_column = $column;
                        return false;
                    }
                }

                return true;
            }
            
            
            
            /***************************************************************************************************************
             * Buffer functionality                                                                                        *
             ***************************************************************************************************************/
            
            public function deleteRowBuffered( array $key_values )
            {
                foreach( $key_values as $col => $value )
                {
                    if( $this->_delete_buffer_size >= $this->_delete_buffer_max_size )
                    {
                        $this->flushDeleteBuffer();
                    }
                    else
                    {
                        $this->_delete_buffer_size++;
                        $this->_delete_buffer[$col][] = $value;
                    }
                }
            }
            
            public function insertRowBuffered( array $values )
            {
                foreach( $values as $col => $value )
                {
                    if( $this->_insert_buffer_size >= $this->_insert_buffer_max_size )
                    {
                        $this->flushInsertBuffer();
                    }
                    else
                    {
                        $this->_insert_buffer_size++;
                        $this->_insert_buffer[$col][] = $value;
                    }
                }
            }
            
            public function insertRowsBuffered( array $rows )
            {
                foreach( $rows as $row )
                {
                    $this->insertRowBuffered( $row );
                }
            }
            
            public function setInsertBufferSize( $size = 1000 )
            {
                $this->_insert_buffer_max_size = $size;
            }
            
            public function setDeleteBufferSize( $size = 1000 )
            {
                $this->_delete_buffer_max_size = $size;
            }
            
            public function getInsertBufferSize()
            {
                return $this->_insert_buffer_size;
            }
            
            public function getDeleteBufferSize() 
            {
                return $this->_delete_buffer_size;
            }
            
            public function flushInsertBuffer()
            {
                
            }
            
            public function flushDeleteBuffer()
            {
                
            }
            
            public function clearInsertBuffer()
            {
                $this->_insert_buffer = array();
            }
            
            public function clearDeleteBuffer()
            {
                $this->_delete_buffer = array();
            }




            /***************************************************************************************************************
             *                                                                                                             *
             * Private Methods                                                                                             *
             *                                                                                                             *
             ***************************************************************************************************************/

            /**
             * Format a value to the MySQL format.
             * @code
             * var_dump( self::formatValue( 12 ) );                 // Output: int(12)
             * var_dump( self::formatValue( NULL ) );               // Output: string(4) "NULL";
             * var_dump( self::formatValue( "NULL" ) );             // Output: string(4) "NULL";
             * var_dump( self::formatValue( "2012-01-01" ) );       // Output: string(24) "'2012-01-01 00:00:00'";
             * var_dump( self::formatValue( "teststr" ) );          // Output: string(9) "teststr";
             * @endcode
             *
             * @param $value The value that will be formated
             * @retval string
             *  If the value is numeric and not a string it will return the value as int/float or double.<br />
             *  If the value is a string with the content NULL or the value is NULL it will return a string with the content NULL. <br />
             *  If the value is fetchable from the function strtotime the method will return a string with the dateformat Y-m-d h:m:s. <br />
             *  If the value is a string it will return the same string with the char ' at the end and the beggining.<br />
             */
            private function formatValue( $value )
            {
                // Check if it is a numberic value
                if( is_numeric( $value ) && !is_string( $value ) )
                {
                    return $value;
                }
                
                // Check if the value should be NULL
                if( $value === "NULL" || $value === NULL )
                {
                    return "NULL";
                }

                // Check if it is a date/time or datetime
                if( ( $dstr = strtotime( $value ) ) !== false )
                {
                    return date( "'Y-m-d h:m:s'", $dstr );
                }

                // Escape the string
                $value = $this->_link->escape( $value );
                
                return sprintf( "'%s'", $value );
            }

            /**
             * Convert an key_value array into an MySQL condition.
             *
             * @param array $key_values An array with the names of the primary key values
             * @retval string
             *  An condition string with the format `<schema_name`.`<table_name`=<formated_value> [AND ...]
             */
            private function keysToCondition( array $key_values )
            {
                $conditions = array();

                foreach( $key_values as $key_name => $key_value )
                {
                    $conditions[] = sprintf(
                        "`%s`.`%s`=%s",
                        static::$_name,
                        $key_name,
                        $this->formatValue( $key_value )
                    );
                }

                return implode( " AND \n", $conditions );
            }
            
            /**
             * Converts column names to absolute names and merge it to an string that is useable for an SELECT statement.<br />
             * \code{.php} 
             * print( $this->columnsToStatement( array( 'id', 'name' ) ) ); //Output is: `tbl`.`id`, `tbl`.`name` 
             * \endcode
             *
             * @param array $columns
             * @retval string
             *  If the number of elements in the array is 0, it will return a string with the content *. Otherwise it will return a string with the following format: <br />`<table_name>`.`<column_name>`[, ...]
             */
            private function columnsToStatement( array $columns )
            {
                $columns_str = "";

                if( count( $columns ) === 0 )
                {
                    $columns_str = "*";
                }
                else
                {
                    $first = true;

                    foreach( $columns as $column )
                    {
                        if( $first )
                        {
                            $first = false;
                        }
                        else
                        {
                            $columns_str .= ",";
                        }

                        $columns_str .= sprintf(
                            "`%s`.`%s`",
                            static::$_name,
                            $column
                        );
                    }
                }

                return $columns_str;
            }

            /**
             * Convert a column array to an MySQL ORDER BY statement
             *
             * @param array $order_by An array with columns name
             * @retval string
             *  If the number of elements in the array is 0, it will return a string with nothing. Otherwise it will return an ORDER BY statement with the format: ORDER BY `<table_name>`.`<column_name`>[, `<table_na...].
             */
            private function orderToStatement( array $order_by )
            {
                $statement = "";

                if( count( $order_by ) > 0 )
                {
                    $statements = array();
   
                    foreach( $order_by as $column => $type )
                    {
                        if( strtoupper( $type ) !== "ASC" )
                        {
                            $type = "DESC";
                        }

                        $statements[] = sprintf( "`%s`.`%s` %s", static::$_name, $column, $type ); 
                    }

                    $statement = sprintf( "ORDER BY %s", implode( ", ", $statements ) );
                }

                return $statement;
            }

            /**
             * Convert a integer value to an MySQL LIMIT statement
             *
             * @param integer $limit
             * @retval string 
             *  If the number of elements in the array is 0, it will return a string with nothing. Otherwise it will return a LIMIT Statement with the format: LIMIT <limit_value>.
             */
            private function limitToStatement( $limit )
            {
                if( $limit === 0 )
                {
                    return "";
                }
                else
                {
                    return sprintf( "LIMIT %d", $limit );
                }
            }
            
            /**
             * Converts an value array to a MySQL Statement that assigns values to column names.
             *
             * @param array $values The array key is the name of the columns and the value is the column value.
             * @retval string
             *  Return the statement with the format: `<column_name>`=<column_value>
             */
            private function valuesToStatement( array $values )
            {
                $column_values = array();

                foreach( $values as $column_name => $column_value )
                {
                    $column_values[] = sprintf(
                        "`%s`=%s",
                        $column_name,
                        $this->formatValue( $column_value )
                    );
                }

                return implode( ", ", $column_values );
            }
            
            /**
             * Return the absolute name of the table in MySQL format.
             * @code
             * print( $this->getAbsoluteName() ); // Output: `example_db`.`example_tbl`
             * @endcode
             *
             * @retval string
             *  Format is: `<schema_name>`.`<table_name>`
             *
             */
            public function getAbsoluteName()
            {
                return sprintf( 
                    "`%s`.`%s`",
                    static::$_schema,
                    static::$_name
                );
            }
        }

    }    
?>

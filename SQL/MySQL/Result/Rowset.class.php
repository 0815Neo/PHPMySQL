<?php
    /**
     * This file contains the SQL::MySQL::Result::Rowset class
     * 
     * @author Gerd Rönsch
     * @package MySQL
     */
    
    namespace SQL\MySQL\Result {
        
        class Rowset extends \SQL\Result\Rowset {
            
            /**
             * Get the row count of the selected rows.
             * 
             * @return integer
             */
            public function countRows()
            {
                return $this->_resource->num_rows;
            }

            /**
             * Get the column count of the selection.
             * 
             * @return type
             */
            public function countColumns()
            {
                return $this->_resource->field_count;
            }

            /**
             * Returns the definition of all columns of a result set as an array of objects.
             * 
             * @return array 
             *  The array key of the array is the name of the table. 
             *  The object has the following fields: name, orgname, table, orgtable, max_length, length, charsetnr, flags, type, decimals.
             * @throws SQL::MySQL::Exception If the table has a column with no name
             */
            public function getColumns()
            {
                $fields = $this->_resource->fetch_fields();
                $columns = array();

                foreach( $fields as $column )
                {
                    if( $column->name === false )
                    {
                        throw new Exception( "The table has a field with no name!" );
                    }

                    $columns[$column->name] = (array) $column;
                }

                return $columns;
            }

            /**
             * Fetch all rows of the result and return the rows as an array of values.
             * The format of the values is defined in the fetchmode.
             * 
             * @param integer $tmp_fetchmode The default Fetchmode is the fetchmode of the result
             * @return array
             * @throws SQL::MySQL::Exception
             */
            public function fetchAll( $tmp_fetchmode = -1 )
            {
                if( $tmp_fetchmode === -1 )
                {
                    $tmp_fetchmode = $this->_fetch_mode;
                }

                switch( $tmp_fetchmode )
                {
                    case \SQL\FETCHMODE_ARRAY_ASSOC:
                        $method_mode = MYSQLI_ASSOC;
                        break; 
                    case \SQL\FETCHMODE_ARRAY_NUM:
                        $method_mode = MYSQL_NUM;
                        break;
                    case \SQL\FETCHMODE_ARRAY_BOTH:
                        $method_mode = MYSQLI_BOTH;
                        break;
                    case \SQL\FETCHMODE_OBJECT:
                        $ret = array();

                        while( NULL !== ($obj = $this->_resource->fetch_object() ) )
                        {
                            $ret[] = $obj;
                        }

                        return $ret;
                        break;
                    default:
                        throw new Exception( "The fetchmode does not exists!" );
                        break;
                }

                $ret = array();

                while( NULL !== ($data = $this->_resource->fetch_array( $method_mode ) ) )
                {
                    $ret[] = $data;
                }

                return $ret;
            }

            /**
             * Fetch a row of the result.
             * 
             * @param integer $tmp_fetchmode
             * @return mixed Return a value with the format of the specified fetchmode or NULL if the last element is reached
             * @throws SQL::MySQL::Exception
             */
            public function fetch( $tmp_fetchmode = -1 )
            {
                if( $tmp_fetchmode === -1 )
                {
                    $tmp_fetchmode = $this->_fetch_mode;
                }

                switch( $tmp_fetchmode )
                {
                    case \SQL\FETCHMODE_ARRAY_ASSOC:
                        return $this->_resource->fetch_assoc( );
                        break; 
                    case \SQL\FETCHMODE_ARRAY_NUM:
                        return $this->_resource->fetch_array( MYSQL_NUM );
                        break;
                    case \SQL\FETCHMODE_ARRAY_BOTH:
                        return $this->_resource->fetch_array( MYSQLI_BOTH );
                        break;
                    case \SQL\FETCHMODE_OBJECT:
                        return $this->_resource->fetch_object();
                    default:
                        throw new Exception( "The fetchmode does not exists!" );
                        break;
                } 
            }

            /**
             * Free the result.
             */
            public function free()
            {
                $this->_resource->free();
            }

            /**
             * 
             */
            public function init() {} 

        } 

    }    
?>

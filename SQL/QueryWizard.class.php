<?php
    /**
     *
     *
     */
    
    namespace SQL {

        class QueryWizard {

            /**
             * Get all rows from a sql query as array.
             * 
             * @code
             * $rows = \SQL\QueryWizard( $link, "SELECT * FROM `test`.`test`", \SQL\FETCHMODE_ARRAY_ASSOC );
             * print_r( $rows ); # the values of the array are arrays with key=column_name and value=column_value
             * 
             * # Output: array(
             * #     [0] => array(
             * #         'id' => 12,
             * #         'name' => 'max'
             * #     )
             * #     [1] => ...
             * # )
             * 
             * @endcode
             * 
             * @param \SQL\ConnectionAdapter $link
             * @param type $query
             * @param type $fetch_mode
             * @return array An array with rows. If no row is selected an empty array is returned. The value format of the values is defined with $fetch_mode.
             * @throws Exception If the query wasn't successfully
             */
            public static function getRows( \SQL\ConnectionAdapter &$link, $query, $fetch_mode = \SQL\FETCHMODE_OBJECT )
            {
                $result = $link->executeQuery( $query );

                if( NULL === $result)
                {
                    throw new Exception( $link->getLastErrorMessage(), $link->getLastErrorCode() );
                }

                if( !($result instanceof \SQL\Result\Rowset) )
                {
                    return array();
                }

                $result->setFetchmode( $fetch_mode );

                return $result->fetchAll();
            }

            /**
             * Get a column from a select query that only return one row.
             * 
             * @code
             * $col = \SQL\QueryWizard::getRowColumn( $link, "SELECT * FROM `test`.`test` WHERE `id`=1", "test" );
             * printf( "The column test of the row with the id 1 has the following value: %s\n", $col ); # print the value of the column as string.
             * @endcode
             * 
             * @param \SQL\ConnectionAdapter $link
             * @param type $query
             * @param type $column
             * @return string
             * @throws Exception If the query wasn't succeed
             */
            public static function getRowColumn( \SQL\ConnectionAdapter &$link, $query, $column )
            {
                $result = self::getRows( $link, $query );

                if( count( $result) === 0 )
                {
                    return "";
                }

                if( count( $result ) > 1 )
                {
                    throw new Exception( "The result has more than one row" );
                }

                if( !isset( $result[0]->$column ) )
                {
                    throw new Exception( sprintf( "The result does not have the column '%s'!", $column ) );
                }

                return $result[0]->$column;
            }
        }
    }

?>

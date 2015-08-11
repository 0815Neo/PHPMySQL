<?php
    /**
     *
     *
     */
    

    namespace SQL {

        interface TableDataGateway {

            /*******************************************************************
             * Standard methods for the TableDataGateway                       *
             *******************************************************************/
            public function findRow( array $key_values, array $columns = array() );
            public function rowExists( array $key_values );
            public function deleteRow( array $key_values );
            public function updateRow( array $key_values, array $values );
            public function getRows( array $columns = array(), array $order_by = array(), $limit = 0 );
            public function insertRow( array $values );
            public function insertRows( array $rows, &$err_msg );
            
            
            
            /*******************************************************************
             * Buffer functionality                                            *
             *******************************************************************/
            public function deleteRowBuffered( array $key_values );
            public function insertRowBuffered( array $values );
            public function insertRowsBuffered( array $rows );
            
            public function setInsertBufferSize( $size = 1000 );
            public function setDeleteBufferSize( $size = 1000 );
            public function getInsertBufferSize();
            public function getDeleteBufferSize();
            
            public function flushInsertBuffer();
            public function flushDeleteBuffer();
            public function clearInsertBuffer();
            public function clearDeleteBuffer();

        }

    }
?>

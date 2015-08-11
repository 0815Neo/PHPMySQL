<?php

    /**
     * Display a result as table on command line (ASCI ART)
     *
     * @param \SQL\Result\Rowset $res
     */
    function display_result( \SQL\Result\Rowset $res )
    {
        $hr = str_repeat( "-", 23*$res->countColumns()+1 );
        $hr .= "\n";

        $columns = $res->getColumns();
        $rows = $res->fetchAll( \SQL\FETCHMODE_ARRAY_NUM );

        $table_header = "| ";

        foreach( $columns as $column_name => $column_props )
        {
            $table_header .= sprintf( "%20s | ", substr( $column_name, 0, 20 ) );
        }

        printf( $hr );
        printf( $table_header.PHP_EOL );
        printf( $hr );

        foreach( $rows as $row )
        {
            $row_str = "| ";

            foreach( $row as $column_value )
            {
                $row_str .= sprintf( "%20s | ", substr( $column_value, 0, 20 ) );
            }

            printf( "%s\n", $row_str );
            printf( $hr );
        }
    }
    
    class CLI {
        
        const HR = "*******************************************************************************";
        
        /**
         * Display an array as table on command line (ASCI ART)
         *
         * @param array $res 2 dimensonal array
         */
        public static function display_array( array $res )
        {
            reset( $res );
            if( !is_array( current( $res ) ) )
            {
                return false;
            }
            
            $ccolumns = count( current( $res ) );
            $columns = current( $res );

            $hr = str_repeat( "-", 23*$ccolumns+1 );
            $hr .= "\n";

            $rows = $res;

            $table_header = "| ";

            foreach( $columns as $column_name => $column_props )
            {
                $table_header .= sprintf( "%20s | ", substr( $column_name, 0, 20 ) );
            }

            printf( $hr );
            printf( $table_header.PHP_EOL );
            printf( $hr );

            foreach( $rows as $row )
            {
                $row_str = "| ";

                foreach( $row as $column_value )
                {
                    $row_str .= sprintf( "%20s | ", substr( $column_value, 0, 20 ) );
                }

                printf( "%s\n", $row_str );
                printf( $hr );
            }
        }
        
        
        public static function psep()
        {
            printf( "%s%s", self::HR, PHP_EOL );
        }
        
        public static function writeLine( $str )
        {
            printf( "%s\n", $str );
        }
        
        public static function write( $str )
        {
            printf( "%s", $str );
        }
        
        /**
         * Reads a line from the command line interface.
         * 
         * @param string $message
         * @param string $std_value
         * @return string
         */
        public static function readLine( $message, $std_value = NULL )
        {
            fwrite( STDOUT, sprintf( "%s", $message ) );
            
            $input_str = trim( fgets(STDIN) );

            if( "" === $input_str )
            {
                return $std_value;
            }

            return $input_str;
        }
    }
?>

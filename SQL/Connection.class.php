<?php
    /**
     *
     *
     */
    
    namespace SQL {
        
        class Connection {
    
            public static function factory( $adapter, $properties ) {
                $class_name = sprintf( '\SQL\%s\ConnectionAdapter', $adapter );

                if( !class_exists( $class_name ) )
                {
                    throw new Exception( sprintf( "The Adapter type '%s' is not supported!", $adapter ) );
                }

                return new $class_name( $properties );
            }

        }

    }
?>

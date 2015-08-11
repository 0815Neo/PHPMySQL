<?php
    namespace SQL {
        require( '../SQL/SQL.inc.php' );
    
        class TestAdapter extends ConnectionAdapter {
            public function connect( array $properties ) {}
            public function connect_persistent( array $properties ) {}
            public function disconnect() {}
            public function selectSchema( $schema ) {}
            public function executeQuery( $query ) {}
            public function getConnectionInfo() {}
            public function getConnectionState() {}
            public function getLastErrorCode() {}
            public function getLastErrorMessage() {}
            public function getErrorMessage( $code ) {}
            public function escape( $str ) {}
            public function getSelectedSchema() {}
            
            public function test_debug( $msg )
            {
                $this->debug( $msg );
            }
            
        }
        
        $all_flags = array( FLAG_DEBUG_MODE_CLI, FLAG_DEBUG_MODE_FILE, FLAG_DEBUG_MODE_HTML );
        
        
        
        $con = new TestAdapter( array() );  # init the test adapter
        $con->setFlag( FLAG_DEBUG );        # activate debugging
        
        
        
        # Debug to file
        $con->removeFlags( $all_flags );
        $con->setFlag( FLAG_DEBUG_MODE_FILE );
        $con->test_debug( "Test_File" );
        
        # Debug to command line interface
        $con->removeFlags( $all_flags );
        $con->setFlag( FLAG_DEBUG_MODE_CLI );
        $con->test_debug( "Debug CLI" );
        
        # Debug with html format
        $con->removeFlags( $all_flags );
        $con->setFlag( FLAG_DEBUG_MODE_HTML );
        $con->test_debug( "Debug HTML" );
        
        # Debug with all formats
        $con->setFlags( $all_flags );
        $con->test_debug( "All" );
    }
?>

<?php
    require( '../SQL/SQL.inc.php' );
    require( 'lib.inc.php' );

    $connection_properties = array(
        'host' => 'localhost',
        'user' => 'root',
        'user_password' => '1234',
        'schema' => 'mysql'
    );
    
    $adapter = "MySQL";

    CLI::writeLine( "SQL::Connection test." );


    printf( "Connect to the server..." );

    try {
        $link = \SQL\Connection::factory( $adapter, $connection_properties );
    }
    catch( \Exception $e )
    {
        printf( "failure!\nError raised: %s\n", $e->getMessage() ); exit;
    }

    CLI::writeLine( "success!" );
?>

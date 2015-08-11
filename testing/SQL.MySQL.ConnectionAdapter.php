<?php
    require( '../SQL/SQL.inc.php' );
    require( 'lib.inc.php' );

    const HR =          "----------------------------------------------------------------------------------------------------\n";

    $connection_properties = array(
        'host' => 'localhost',
        'user' => 'root',
        'user_password' => '1234',
        'schema' => 'mysql'
    );




    printf( "\n* Initalisation test\n%s", HR );

    
    
    /******************************************************************************************
     * Test initalisation (connect, state_check, flags)                                       *
     ******************************************************************************************/
    printf( "Connect to the server..." );

    try {
        $link = \SQL\Connection::factory( "MySQL", $connection_properties );
    }
    catch( \Exception $e )
    {
        printf( "failure!\nError raised: %s\n", $e->getMessage() ); exit;
    }

    print( "success!\n" );


    printf( "Send ping to the server (check connection state)..." );
    switch( $link->getConnectionState() )
    {
        case \SQL\ConnectionAdapter::STATE_NOT_CONNECTED:
            printf( "failure!\n" );
            exit;
            break;
        case \SQL\ConnectionAdapter::STATE_CONNECTED:
            printf( "success!\n" );
            break;
    }

    printf( "Test flag system..." );
    $link->setFlag( \SQL\FETCHMODE_ARRAY_ASSOC );
    $link->setFlag( \SQL\FETCHMODE_OBJECT );
    printf( "finished!\n" );
    printf( HR );




    /******************************************************************************************
     * Test query execution                                                                   *
     ******************************************************************************************/ 
    printf( "\n* Query test\n%s", HR );


    printf( "1. Valid query and fetchable\n" );

    $res = $link->executeQuery( "SHOW DATABASES;" );

    if( $res === NULL )
    {
        printf( "Error raised: %s\n", $link->getLastErrorMessage() );
    }

    display_result( $res );
    printf( PHP_EOL );


    printf( "2. Valid query and not fetchable\n" );

    $res = $link->executeQuery( "CREATE TEMPORARY TABLE tmp LIKE `mysql`.`user`" );

    if( $res === NULL )
    {
        printf( "Error raised: %s\n", $link->getLastErrorMessage() );
    }

    printf( "Affected rows: %d\n", $res->countAffectedRows() );
    printf( PHP_EOL );



    printf( "3. Syntax error in query\n" );

    $res = $link->executeQuery( "SELECT `;" );

    if( $res === NULL )
    {
        printf( "Error raised: %s\n", $link->getLastErrorMessage() );
    }

    printf( HR.PHP_EOL );
?>

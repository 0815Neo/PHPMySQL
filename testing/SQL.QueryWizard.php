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

    
    try {
        $link = \SQL\Connection::factory( $adapter, $connection_properties );
    }
    catch( \Exception $e )
    {
        printf( "failure!\nError raised: %s\n", $e->getMessage() ); exit;
    }
    
    
    
    # SQL::QueryWizard::getRows test
    CLI::writeLine( "Try to get Rows" );
    $q1 = "SELECT * FROM `test`.`test`";
    $rows = SQL\QueryWizard::getRows( $link, $q1, \SQL\FETCHMODE_ARRAY_ASSOC );
    
    CLI::display_array($rows);
    
    # SQL::QueryWizard::getRowColumn() test
    CLI::writeLine( "Trying to get the column test from the table test" );
    
    $q2 = "SELECT * FROM `test`.`test` WHERE `id`=77";
    $col_val = SQL\QueryWizard::getRowColumn($link, $q2, "test" );
    CLI::writeLine( sprintf( "Wert der Spalte test: %s", $col_val ) );
    
?>

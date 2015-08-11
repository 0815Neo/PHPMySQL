<?php
    /***************************************************************************
     * This is the testing file for the class SQL::MySQL::Table                *
     *                                                                         *
     * @author Gerd Rönsch                                                     *
     ***************************************************************************/
    require_once( '../SQL/SQL.inc.php' );
    require_once( 'lib.inc.php' );


    // Create example mysql table abstraction
    class user extends \SQL\MySQL\Table {
        static $_schema = "test";
        static $_name = "test"; 
    }

    /* SQL file to create the test table
    
    delimiter $$
    CREATE DATABASE IF NOT EXISTS test;
    USE `test`;
    CREATE TABLE `test` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `test` varchar(45) DEFAULT NULL,
      `date` date DEFAULT NULL,
      `time` time DEFAULT NULL,
      `datetime` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=latin1$$
     
    */
    
    
    /***************************************************************************
     * Main                                                                    *
     ***************************************************************************/
    // Try to connect to database
    try {
        $link = \SQL\Connection::factory( "MySQL", array( 
            'host' => 'localhost',
            'user' => 'root',
            'user_password' => '1234',
            'schema' => 'test'
        ) );
    }
    catch( Exception $e )
    {
        CLI::psep();
        printf( "%s\n", $e->getMessage() );
        CLI::psep();
        exit;
    }

    if( NULL === $link )
    {
        print( "Error!" );exit;
    }

    // Set debugging flags if needed
    //$link->setFlag( SQL\FLAG_DEBUG );
    //$link->setFlag( SQL\FLAG_DEBUG_MODE_CLI );


    $u = new user( $link );

    
    
    
    /***************************************************************************
     * Method Tests                                                            *
     ***************************************************************************/
    
    # SQL::MySQL::Table::findRow test
    CLI::psep();
    CLI::writeLine( "Trying to find the dataset with the id=1..." );
    
    $frres = $u->findRow( array( 'id' => 1 ) ); // Search for the row with the id 0

    
    if( count( $frres ) === 0 )
    {
        printf( "Row wasn't found!\n" );
    }
    else
    {
        CLI::display_array( array($frres) );
    }
    
    
    
    # SQL::MySQL::Table::rowExists test
    CLI::psep();
    CLI::writeLine( "Check if the dataset with id=1 exists..." );
    
    $reres = $u->rowExists( array( 'id' => 1 ) ); // Search for the row with the id 0

    
    if( !$reres )
    {
        printf( "Row does not exists!\n" );
    }
    else
    {
        printf( "Row exists!\n" );
    }


    
    # SQL::MySQL::Table::deleteRow test
    CLI::psep();
    CLI::writeLine( "Trying to delete row with id=10..." );
    
    $drres = $u->deleteRow( array( 'id' => 10 ) );

    if( $drres === false ) 
    {
        printf( "Deleting has been failed!\n" ); 
    }
    else 
    {
        printf( "Deleting has been succeed!\n" );
    }



    # SQL::MySQL::Table::insertRow test
    CLI::psep();
    CLI::writeLine( "Trying to insert new row..." );
    
    $irres = $u->insertRow( array( 
        'id' => NULL, 
        'test' => "te" 
    ) );

    if( $irres === false )
    {
        printf( "Insert has been failed. Reason: %s\n", $link->getLastErrorMessage() );
    }
    else
    {
        printf( "Insert has been succeed!\n" );
    }



    # SQL::MySQL::Table::insertRows test
    CLI::psep();
    CLI::writeLine( "Trying to insert new rows..." );
    
    $rows = array(
        array(
            'id' => NULL,
            'test' => 'test01',
            'date' => date( "c" )
        ),
        array(
            'test' => 't'
        )
    );

    $error = "";
    $irsres = $u->insertRows( $rows, $error );

    if( $irsres === false )
    {
        printf( "Insert has been failed. Reason: %s\n", $error );
    }
    else
    {
        printf( "Inserts has been succeed!\n" );
    }



    # SQL::MySQL::Table::getRows()
    CLI::psep();
    CLI::writeLine( "Trying to get all rows..." );
    
    $order_by = array(
        'test' => 'DESC'
    );

    $columns = array();

    try {
        $grsres = $u->getRows( $columns, $order_by );

        CLI::display_array( $grsres );
    }
    catch( Exception $e )
    {
        printf( "Get rows has been failed! Reason: %s\n", $e->getMessage() );
    }


    # SQL::MySQL::Table::updateRow()
    CLI::psep();
    CLI::writeLine( "Trying to update row..." );

    $keys = array(
        'id' => 77
    );

    $values = array(
        'test' => "updated"
    );

    $res = $u->updateRow( $keys, $values );

    if( false === $res )
    {
        printf( "Update row has been failed! Reason: %s\n", $link->getLastErrorMessage() );
    }
    else
    {
        printf( "Update row has been succeed!\n" );
    }
   
    
    
    # SQL::MySQL::Table::getColumns()
    CLI::psep();
    CLI::writeLine( "Trying to get columns..." );

    CLI::display_array( $u->getColumns() );
    
    
    
    # SQL::MySQL::Table::columnsExists()
    CLI::psep();
    CLI::writeLine( "Trying to check the following columns of existence: 'test', 'date', 'fail'.");
    
    $wrong_column = "";
    $ceres = $u->columnsExists(array( 'test', 'date', 'fail' ), $wrong_column);
    
    if( !$ceres )
    {
        CLI::writeLine( sprintf( "The column `%s` does not exists!", $wrong_column ) );
    }
    else
    {
        CLI::writeLine( "All columns exists!" );
    }
    
    
    
    # SQL::MySQL::Table::getAbsoluteName()
    CLI::psep();
    CLI::writeLine( "Trying to get absolute name of the table..." );
    
    CLI::writeLine( sprintf( "The name is: %s", $u->getAbsoluteName() ) );
?>

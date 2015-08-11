<?php

require '../../../SQL/SQL.inc.php';
require '../../conf.inc.php';

class ConnectionAdapterTest extends PHPUnit_Framework_TestCase {
    
    public function getValidConnectionConfigurationFixture()
    {
        $con = array(
            'host' => 'localhost',
            'user' => 'root',
            'user_password' => '1234',
            'schema' => 'test'
        );
         
        return $con;
    }
    
    public function getValidConnectionFixture()
    {
        
    }
    
    public function testInit()
    {
        $this->assertInstanceOf('\SQL\ConnectionAdapter', \SQL\Connection::factory( 'MySQL', $this->getValidConnectionConfigurationFixture() ) );
    }
    
}

?>

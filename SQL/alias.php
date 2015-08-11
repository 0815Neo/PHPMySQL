<?php
    function a($name) { 
        print( $name ); 
    }

    function a_alias($name)
    {
        return call_user_func_array("a", func_get_args() );
    }



    a("hallo\n");
    a_alias( "hallo\n" );
?>

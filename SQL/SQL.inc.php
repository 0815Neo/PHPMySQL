<?php
/**
 * This file register this library to the spl autoloader.
 * If the PHP version is lower than 5, this autoloader will not work!
 * You are able to change the pathname of this library but the per-
 * formance will be decreased.
 * A compatibility check of PHP is included.
 *
 * @author Gerd Rönsch <gerd.roensch@tu-dresden.de>
 * @package SQL
 */

namespace SQL {

    const LIB_PHP_MAJOR_VERSION = 5;
    const LIB_PHP_MINOR_VERSION = 3;

    const LIB_VERSION_MAJOR = 0;
    const LIB_VERSION_MINOR = 0;
    const LIB_VERSION_PATCH = 0;

    define( 'LIBRARY_PATH',     __DIR__. '/' );



    const FLAG_DEBUG =                  0x001;
    const FLAG_DEBUG_MODE_CLI =         0x002;
    const FLAG_DEBUG_MODE_FILE =        0x003;
    const FLAG_DEBUG_MODE_HTML =        0x004;
    const FLAG_DEBUG_QUERIES =          0x005;
    const FLAG_DEBUG_ERRORS =           0x006;
    const FLAG_DEBUG_CONNECT =          0x007;

    const FETCHMODE_ARRAY_NUM =         0x010;
    const FETCHMODE_ARRAY_ASSOC =       0x011;
    const FETCHMODE_ARRAY_BOTH =        0x012;
    const FETCHMODE_OBJECT =            0x013;

    const FLAG_FETCHMODE_ARRAY_NUM =    FETCHMODE_ARRAY_NUM;
    const FLAG_FETCHMODE_ARRAY_ASSOC =  FETCHMODE_ARRAY_ASSOC;
    const FLAG_FETCHMODE_ARRAY_BOTH =   FETCHMODE_ARRAY_BOTH;
    const FLAG_FETCHMODE_OBJECT =       FETCHMODE_OBJECT;
    
    const USE_DEFAULT_SCHEMA =              NULL;       # Can be used as param for $schema

    // Check for PHP Versions lower than 4.0
    if( !defined( "PHP_MAJOR_VERSION") || !defined( "PHP_MINOR_VERSION" ) )
    {
        trigger_error( "Getting PHP Version failed!" );
    }


    // Check if the php version is compatible with the library (apache versioning)
    if( PHP_MAJOR_VERSION !== LIB_PHP_MAJOR_VERSION || PHP_MINOR_VERSION < LIB_PHP_MINOR_VERSION )
    {
        $err_str = sprintf( "The current PHP version(%s) is not compatible with this library (%s.%s.x)!", 
                            PHP_VERSION, 
                            LIB_PHP_MAJOR_VERSION, 
                            LIB_PHP_MINOR_VERSION 
                          );

        trigger_error( $err_str );
    }

    spl_autoload_register( __NAMESPACE__. "\autoload" );


    /**
     * Include the specified class automaticly.
     * This function is only usable if __NAMESPACE__\LIBRARY_PATH is defined
     * and the folder structure match to: LIBRARY_PATH[\__NAMESPACE__[...]]\$class_name!
     *
     * @param string $class_name Absolute name of the class (with namespace)
     * @throws Exception If the class was not found
     */
    function autoload( $class_name )
    {
        // Replace \ with / in class names
        $class_name_patched = str_replace( '\\', '/', $class_name );

        // If the directory has the same name as the namespace the filename is: 
        // __DIR__/../__NAMESPACE__[/__NAMESPACE__[/..]]/<class_name>.class.php
        if( basename( __DIR__ ) === __NAMESPACE__ )
        {
            $file_name = sprintf( "%s/../%s.class.php", __DIR__, $class_name_patched );
        }
        // Otherwise, replace the namespace name with the name of the current directory
        else
        {
            $class_arr = explode( '/', $class_name_patched );
    
            if( $class_arr !== false && $class_arr[0] === __NAMESPACE__ )
            {
                unset( $class_arr[0] );
    
    
                $file_name = sprintf( "%s/", LIBRARY_PATH );
                
    
                foreach( $class_arr as $elem )
                {
                    $file_name .= sprintf( "/%s", $elem );
                }
    

                $file_name .= sprintf( ".class.php" );
            }
            
            if( !file_exists( $file_name ) )
            {
                $err_msg = sprintf( "The class '%s' was not found in '%s'!", $class_name, $file_name );
    
                throw new \Exception( $err_msg );
            }
        }

        if( file_exists( $file_name ) )
        {
            require_once( $file_name );
        }
    }

    /**
     * Get the version of the current library as string.
     * Format: <MAJOR>.<MINOR>.<PATCH>
     *
     * @return string version
     */
    function get_version()
    {
        return sprintf( "%s.%s.%s", LIB_VERSION_MAJOR, LIB_VERSION_MINOR, LIB_VERSION_PATCH );
    }

}
?>

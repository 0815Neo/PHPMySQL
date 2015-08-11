<?php
    if( false !== getenv( 'PHP_AUTOLOADER_ADD_INCLUDE_PATH' ) ) {
        set_include_path( get_include_path().getenv( 'PHP_AUTOLOADER_OPTIONAL_PATHS' ) );
    }


    class Autoload {

        private static $_registered   = false;      # Save if the autoload method is loaded
        private static $_cversions    = array();    # Save an array of classes with version
        private static $_nversions    = array();    # Save an array of namespace with version
        private static $_paths        = NULL;       # Save the include path as array


        /**
         * Initialize autoloader.
         * Called in this file.
         */
        public static function init() {
            if( self::$_registered ) {
                return;
            }
            self::$_paths = explode( PATH_SEPARATOR, get_include_path() );
            spl_autoload_register( 'self::load' );
            self::$_registered = true;
        }


        /**
         * Register a class to the autoloader.
         *
         * @param string $class_name The class that will be automaticly loaded if required
         * @param string $version The optional param that is added to the folder name of the namespace. (Format: Class-Version.class.php)
         * @param string $path If the class is not in the php include path you must add the parent folder of the namespace as param.
         */
        public static function importClass( $class_name, $version = NULL, $path = NULL ) {
            if( $version !== NULL ) {
                self::$_cversions[$class_name] = $version;
            }

            self::addPath( $path );
        }

        
        /**
         * Register a namespace to the autoloader.
         * 
         * @param string $namespace The name of the namespace that will automaticly loaded if a class is needed
         * @param string $version The optional param that is added to the folder name of the namespace. (Format: Namespace-Version)
         * @param string $path If the namespace is not in the php include path you must add the parent folder of the namespace
         */
        public static function importNamespace( $namespace, $version = NULL, $path = NULL ) {
            if( $version !== NULL ) {
                self::$_nversions[$namespace] = $version;
            }                

            self::addPath( $path );
        }


        private static function addPath( $path ) {
            if( $path !== NULL ) {
                set_include_path( sprintf( "%s%s%s", get_include_path(), PATH_SEPARATOR, $path ) );
                self::$_paths[] = $path;
            }
        }

        /**
         * Implement the autoload method for spl_autoload_register.
         *
         * @param string The full class name
         */
        private static function load( $class_name ) {

            if( self::$_paths === NULL ) {
                self::$_paths = explode( PATH_SEPARATOR, get_include_path() );
            }

            $file_name = sprintf( "%s.class.php", str_replace( '\\', '/', self::getFixedClassName( $class_name ) ) );

            foreach( self::$_paths as $path ) {
                $search_file_name = sprintf( "%s/%s", $path, $file_name );
                
                if( file_exists( $search_file_name ) ) {
                    require_once( $search_file_name );
                    return;
                }
            }

        }

        /**
         * Add the version to the class/namespace.
         * If no version was specified with import, the original class name is returned.
         * The format is: [Namespace[-Version]\]Class[-Version].
         *
         * @param string The complete class name
         */
        private static function getFixedClassName( $class_name ) {
            $class_array = explode( '\\', $class_name );
            $namespace = reset( $class_array );

            if( false === $namespace || count( $class_array ) < 2 ) {   # Class is not in a namespace
                if( array_key_exists( $class_name, self::$_cversions ) ) {
                    return sprintf( "%s%s%s", $class_name, PATH_SEPARATOR, self::$_cversions[$class_name] );
                } else {
                    return $class_name; # If no version was specified for the class, the original class name is returned
                }
            } else {                                                    # Class is in a namespace
                if( array_key_exists( $namespace, self::$_nversions ) ) {
                    return substr_replace( 
                        $class_name, 
                        sprintf( "%s%s%s", $namespace, PATH_SEPARATOR, self::$_nversions[$namespace] ), 
                        0, 
                        strlen( $namespace ) 
                    ); # Replace the namespace with namespace+version
                } else {
                    return $class_name; # If no version was specified for the namespace, the original class name is returned
                }
            }
        }

    }

    /**
     * Alias for Autoload::importClass
     */
    function autoload_import_class( $class_name, $version = NULL, $path ) {
        Autoload::importClass( $class_name, $version, $path );
    }


    /**
     * Alias for Autoload::importNamespace
     */
    function autoload_import_namespace( $namespace, $version, $path ) {
        Autoload::importNamespace( $namespace, $version, $path );
    }

    Autoload::init();
?>
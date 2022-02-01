<?php
class database
{
    // store the single instance of database
    private static $instance;
    
    // database connection
    protected static $connection;

    // private constructor to limit object instantiation to within the class
    private function __construct() 
    {
		require("config.php");
		
		self::$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name) or die(mysqli_connect_error());
        mysqli_query(self::$connection, "set names utf8");
    }
    
    // private destructor
    private function __destruct() 
    {
       mysqli_close(self::$connection);
	}

    // getter method for creating/returning the single instance of this class
    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new Database();
        }
        
        return self::$instance;
    }

	// query database
    public function query($query)
    {
    	$result = mysqli_query(self::$connection, $query) or die(mysqli_error(self::$connection));
		
		return $result;
    }
 }
 ?>
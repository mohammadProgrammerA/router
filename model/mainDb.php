<?php
   
    class mainDb implements mainFuncs{
        // protected static $connection;
        private static $serverName="localhost";
        private static  $userName="root";
        private static $password="";
        private static $dbName="ecommerce";
        
        public static function get($query){

            echo $query;
            
                $mysqli=new mysqli(self::$serverName,self::$userName,self::$password,self::$dbName);

                return $mysqli -> query($query);
            
        }
        
    }
?>
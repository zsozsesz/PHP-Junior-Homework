<?php
// database connection class
class Connection {
    // config variables for the connection
    protected $connect;
    private $db_user = 'username';
    private $db_password = 'password';
    private $db_host = 'host';
    private $db_name = 'databasename';

    /*
    connect to database
    @return if success :the connection else: false 
    */
    function connectToDataBase (){
       try{
        $this->connect = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name.';charset=utf8mb4', $this->db_user, $this->db_password);
        return $this->connect;
       }catch(PDOException $e){
            echo 'Database Connection Error';
            return false;
       }
    }   
}
?>
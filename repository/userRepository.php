<?php
class UserRepository{
        // config variables for the connection
        private $connect;
        private $db_user;
        private $db_password;
        private $db_host;
        private $db_name;
        private $ini;

    function __construct() {
        $this->ini = parse_ini_file('../config.ini');
        $this->db_user = $this->ini['db_user'];
        $this->db_password = $this->ini['db_password'];
        $this->db_host = $this->ini['db_host'];
        $this->db_name = $this->ini['db_name'];

        try{
            $this->connect = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name.';charset=utf8mb4', $this->db_user, $this->db_password); 
        }catch(PDOException $e){
            echo 'Internal Server Error';
       } 
           
   }

    /**
     * search for same email in the db
     * @param email adress
     * return true if email unique else false
     */
    public function checkIfEmailUnique($email){
        $sql = "SELECT * FROM users WHERE email = :email"; 
        $stmt = $this->connect->prepare($sql);
        $stmt->bindValue(':email',$email);
        if(!$stmt->execute()){
            echo "Database query error";
        }       
        if($stmt->rowCount() > 0){
            return false;
        }else{ 
            return true;
        }
    }
    /**
     * Create new User in database
     * @param input params
     */
    public function createNewUser($params,$hash){
        try{
            $sql = "INSERT INTO users (name,email,password) VALUES (:name,:email,:password)"; //add new record to the users table
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':name', $params['name']);
            $stmt->bindValue(':email', $params['email']);
            $password = password_hash($params['password1'], PASSWORD_DEFAULT); //hash the password 
            $stmt->bindValue(':password',$password );
            $stmt->execute();
            $lastInsertId = $this->connect->lastInsertId();

            $sql = "INSERT INTO verify (hash,userID) VALUES (:hash,:userID)"; //add new record to verify table
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':hash', $hash);
            $stmt->bindValue(':userID', $lastInsertId);
            $stmt->execute();

            return $hash;
        }catch(PDOException $e){
            echo 'Internal Server Error';
       } 
    }

        /**
     * search for same email in the db
     * @param email address and $password
     * return the result
     */
public function getUserFromDB($email){
        $sql = "SELECT password,name,valid FROM users WHERE email = :email";
        try{
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':email',$email);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch(PDOException $e){
            echo 'Internal Server Error';
        }  
    
    }
    /**
     * @param email, hash
     * @return the result
     * check the email and the hash in the db
     */
    public function checkEmailVerifiedStatus($email,$hash){
        $sql = "SELECT u.email AS email,u.id AS uid, v.hash AS hash FROM users u INNER JOIN verify v ON v.userID =u.id WHERE u.email=:email AND v.hash = :hash";
        try{
            $stmt = $this->connect->prepare($sql);  // search for the hash and email in the db
            $stmt->bindValue(':email',$email);
            $stmt->bindValue(':hash',$hash);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch(PDOException $e){
            echo 'Internal Server Error';
        }
    } 
    
    /**
     * @param user ID
     * @retrun true if success
     * Set the valid column 1 to a user
     */
    public function setValidationTrue($uid){
        try{
            $sql = "UPDATE users SET valid=1 WHERE id =:id"; //set valid to 1, user can log in
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':id',$uid);
            $stmt->execute();

            $sql = "DELETE FROM verify WHERE userID = :userID"; // delete the has record from verify table
            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(':userID',$uid);
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            echo 'Internal Server Error';
        }

    }
}
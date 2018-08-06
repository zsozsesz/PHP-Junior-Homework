<?php
require './vendor/autoload.php';
include_once('./repository/userRepository.php');
include_once('./models/user.php');
class UserService{
    private $userRepository;
    private $logger;
    private $domain;
    //private $ini;
    public function __construct() {
        session_start();
        //$this->ini = parse_ini_file('../config.ini');
        $this->userRepository = new UserRepository();
        $this->logger = new Katzgrau\KLogger\Logger('./logs');
        $this->domain = "yourdomain";
    }
    /**
     * validate the input fields
     * Add user to the database, and send email with hash link
     * @param input fields value
     */
    public function registration($params){
        if(empty($params["password1"])||empty($params["password2"])||empty($params["name"])||empty($params["email"])){ 
            echo 'Please fill all the input fields';
        }elseif($params['password1'] !== $params['password2'] ){
            echo 'The two password should match';
        }elseif(!$this->passwordConfirm($params['password1'])){      
            echo 'Too weak password';
        }elseif(!filter_var($params['email'], FILTER_VALIDATE_EMAIL)){
            echo 'Please add valid email address';
        }elseif(!$this->emailNameConfirm($params['email'],$params['name'])){
            echo 'Too long email or name'; 
        }else{
            if($this->userRepository->checkIfEmailUnique($params['email'])){
                $hash = md5(rand(0,1000)); //generate a 32 long string for email validation
                $this->userRepository->createNewUser($params,$hash);   
                $link = $this->domain.'/verify.php?email='.$params['email'].'&hash='.$hash; 
                mail($params['email'],'Email verification','Please verify your email account. Click on the link:  '.$link); //send email with the link
                header("Location: ./index.php");
            }else{
                echo 'Email already in use';
            }
        }
    }

    /**
     * @param input fields value
     * search for the user in the db 
     * log if the login was unsuccesfull
     * store the user in session variable
     * redirect to welcome page
     */
    public function login($params){
        $result = $this->userRepository->getUserFromDB($params['email']);
            if(count($result) > 0){
                if($result[0]['valid'] != 1){ // check if the email already validated
                    $this->logger->info('Unsuccessfully login (unverificated email)');
                    echo 'Please validate your email address';
                }else{
                    if(password_verify($params['password'],$result[0]['password'])){ // compare the passwords
                        $_SESSION["user"] = new User($result[0]["name"],$params["email"]);
                        header("Location: ./welcome.php");
                    }else{
                        $this->$logger->info('Unsuccessfully login (invalid password)');
                        echo 'Authentication failed';
                    }
                }
            }else{
                $this->logger->info('Unsuccessfully login (unknown email)');
                echo 'Authentication failed';
            }
    }
    /**
     * activate the user
     * @param email,hash
     */
    public function verify($email,$hash){
        $result = $this->userRepository->checkEmailVerifiedStatus($email,$hash);
        if(count($result) > 0){
            if($this->userRepository->setValidationTrue($result[0]['uid'])){
                echo 'You have successfully verified your email address <br> <a href="./index.php">Login</a>';
            }else{
                echo 'Failed to verify email.';
            }
        }else{
            echo 'Failed to verify email.';
        }
    }

    /**
     * logout, destroy the session
     */
    public function logout(){
        session_destroy();
        header("Location: ../index.php"); 
    }

    /**
     * Check if user is logged in
     * @return true if yes  
     */
    public function checkLogin(){
        if(isset($_SESSION["user"])){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @return the session variable with the user
     *      */
    public function getUser(){
        return $_SESSION["user"];
    }
        /**
         * check the length of the email and name variables
         * @param string $email,$name
         * @return Boolean 
        */
        private function emailNameConfirm($email,$name){
            $emailLength = strlen($email)>254 ? false  :true;
            $nameLength = strlen($name)>100 ? false  :true;
            return ($emailLength && $nameLength) == true ? true :false;
        }

        /**
         * check the difficulty of a password
         * @param string password
         * @return Boolean 
         */
        private function passwordConfirm($password) {
            $hasUpper = preg_match('/[A-Z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);
            $enoughLenght = strlen($password)>=8 ? true :false;
            return ($hasUpper&&$hasNumber&&$enoughLenght)==true ? true: false;  
        }
}
?>
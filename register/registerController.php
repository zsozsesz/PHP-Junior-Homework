<?php
    include '../dbconfig/db_config.php';

    $domain = "http://justforpractice.hu";

    // input validation
    if(empty($_POST["password1"])||empty($_POST["password2"])||empty($_POST["name"])||empty($_POST["email"])){ 
        header("Location: register.php?error=6");
    }elseif($_POST['password1'] !== $_POST['password2'] ){
        header("Location: register.php?error=1");
    }elseif(!passwordConfirm($_POST['password1'])){      
        header("Location: register.php?error=2");
    }elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        header("Location: register.php?error=3");
    }elseif(!emailNameConfirm($_POST['email'],$_POST['name'])){
        header("Location: register.php?error=5");
    }else{
        // db connection
        $db = new Connection();
        $con = $db->connectToDataBase();
        $sql = "SELECT * FROM users WHERE email = :email"; // search for same email in the db
        try{
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':email',$_POST['email']);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                $con = NULL;
                header("Location: register.php?error=4"); 
            }else{
                $sql = "INSERT INTO users (name,email,password) VALUES (:name,:email,:password)"; //add new record to the users table
                $stmt = $con->prepare($sql);
                $stmt->bindValue(':name', $_POST['name']);
                $stmt->bindValue(':email', $_POST['email']);
                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT); //hash the password 
                $stmt->bindValue(':password',$password );
                $stmt->execute();
                $lastInsertId = $con->lastInsertId();

                $hash = md5(rand(0,1000)); //generate a 32 long string for email validation
                $sql = "INSERT INTO verify (hash,userID) VALUES (:hash,:userID)"; //add new record to verify table
                $stmt = $con->prepare($sql);
                $stmt->bindValue(':hash', $hash);
                $stmt->bindValue(':userID', $lastInsertId);
                $stmt->execute();
                $con = NULL;
                $link = $domain.'/verify/verify.php?email='.$_POST['email'].'&hash='.$hash; 
                mail($_POST['email'],'Email verification','Please verify your email account. Click on the link:  '.$link); //send email with the link
                header("Location: ../index.php?success=1"); 
            }
        }catch(PDOException $e){
            echo 'Internal Server Error';
       }   
    }
        /**
         * check the length of the email and name variables
         * @param string $email,$name
         * @return Boolean 
        */
        function emailNameConfirm($email,$name){
            $emailLength = strlen($email)>254 ? false  :true;
            $nameLength = strlen($name)>100 ? false  :true;
            return ($emailLength && $nameLength) == true ? true :false;
        }
        /**
         * check the difficulty of a password
         * @param string password
         * @return Boolean 
         */
        function passwordConfirm($password) {
            $hasUpper = preg_match('/[A-Z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);
            $enoughLenght = strlen($password)>=8 ? true :false;
            return ($hasUpper&&$hasNumber&&$enoughLenght)==true ? true: false;  
        }
?>
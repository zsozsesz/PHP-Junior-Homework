<?php
    include '../dbconfig/db_config.php';
    require '../vendor/autoload.php';
    // setup logger
    $logger = new Katzgrau\KLogger\Logger('../logs');
    // setup db connection
    $db = new Connection();
    $con = $db->connectToDataBase();

    // Search for email in the db if its doesnt exist return with error message
    $sql = "SELECT password,name,valid FROM users WHERE email = :email";
    try{
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':email',$_POST['email']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            if($result[0]['valid'] != 1){ // check if the email already validated
                $logger->info('Unsuccessfully login (unverificated email)');
                $con = NULL;
                header("Location: ../index.php?error=2"); 
            }else{
                if(password_verify($_POST['password'],$result[0]['password'])){ // compare the passwords
                    session_start();
                    $_SESSION["name"] = $result[0]['name']; // set the name in sessionvariable
                    $con = NULL;
                    header("Location: ../welcome.php"); // redirect to welcome page
                }else{
                    $logger->info('Unsuccessfully login (invalid password)');
                    $con = NULL;
                    header("Location: ../index.php?error=1"); 
                }
            }
        }else{
            $logger->info('Unsuccessfully login (unknown email)');
            $con = NULL;
            header("Location: ../index.php?error=1"); 
        }
    }catch(PDOException $e){
        echo 'Internal Server Error';
    }   
?>

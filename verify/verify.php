<?php 
    include '../dbconfig/db_config.php';
    $db = new Connection();
    $con = $db->connectToDataBase(); //db connection

    $sql = "SELECT u.email AS email,u.id AS uid, v.hash AS hash FROM users u INNER JOIN verify v ON v.userID =u.id WHERE u.email=:email AND v.hash = :hash";
    try{
        $stmt = $con->prepare($sql);  // search for the hash and email in the db
        $stmt->bindValue(':email',$_GET['email']);
        $stmt->bindValue(':hash',$_GET['hash']);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            $sql = "UPDATE users SET valid=1 WHERE id =:id"; //set valid to 1, user can log in
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id',$result[0]['uid']);
            $stmt->execute();

            $sql = "DELETE FROM verify WHERE userID = :userID"; // delete the has record from verify table
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':userID',$result[0]['uid']);
            $stmt->execute();
            $con = NULL;
            header("Location: ../index.php?success=2"); 
        }else{
            $con = NULL;
            echo 'Failed to verify email';
        }
    }catch(PDOException $e){
        echo 'Internal Server Error';
    }

?>

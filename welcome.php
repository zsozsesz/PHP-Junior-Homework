<?php
    session_start();
    if(isset($_SESSION["name"])){
        echo '<h1>Welcome '.$_SESSION["name"].'</h1> <br> <a href="logout.php">Logout</a>';
    }else{
        header("Location: index.php?error=3"); //redirect to login page if session name is unset
    }
?>
<?php
       include_once('./services/userService.php');
    $userService = new UserService();
    if($userService->checkLogin()){
        $user = $userService->getUser();
        echo "Welcome ".$user->getName().' <br> </h1><a href="logout.php">Logout</a>';
    }else{
        header("Location: index.php");
    }
?>
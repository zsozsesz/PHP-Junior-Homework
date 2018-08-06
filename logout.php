<?php
    include_once('./services/userService.php');
    $userService = new UserService();
    $user = $userService->logout();
    header("Location: index.php");

?>
<?php
     include_once('./services/userService.php');
   $userService = new UserService();
   $userService->verify($_GET['email'],$_GET['hash']);
?>
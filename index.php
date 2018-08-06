<html>
<?php
    include_once('./services/userService.php');
    $userService = new UserService();
    if(isset($_POST['email'])){
        $userService->login($_POST);
    }
?>
<body>
    <!-- Login Form-->
    <form action="./index.php" method="post">
       Login:   <br>
            E-mail:<input type="text" name="email" required><br>
            Password:<input type="password" name="password" required><br>
            <input type="submit" value="submit">
    </form>
    <!-- Navigate to reg form-->
    <a href="./register.php">Registration</a>
</body>
</html>
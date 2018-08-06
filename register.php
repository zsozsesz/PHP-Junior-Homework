<html>
    <?php
        include_once('./services/userService.php');
    if(isset($_POST['email'])){
        $userService = new UserService();
        $userService->registration($_POST);
    }
    ?>
<body>
    <!-- Registration Form-->
    <form action="./register.php" method="post">
        Registration:   <br>
            Name:<input type="text" name="name" required> max length 100 char<br> 
            E-mail:<input type="text" name="email" required><br>
            Password:<input type="password" name="password1" required> require atleast one uppercase letter, one number and min length 8 char<br>
            Confirm Password: <input type="password" name="password2" required><br>
    <input type="submit" value="submit">
    </form>
    <a href="./index.php">Login</a>

</body>
</html>
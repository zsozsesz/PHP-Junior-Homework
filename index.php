<html>
<body>
    <!-- Login Form-->
    <form action="./login/loginController.php" method="post">
       Login:   <br>
            E-mail:<input type="text" name="email" required><br>
            Password:<input type="password" name="password" required><br>
            <input type="submit" value="submit">
    </form>
    <!-- Navigate to reg form-->
    <a href="./register/register.php">Registration</a>
    <?php
        //check the response from loginController
        if(isset($_GET['error']) && $_GET['error'] ==1){
            echo 'Authentication failed';
        }elseif(isset($_GET['error']) && $_GET['error'] ==2){
            echo 'Please validate your email address';
        }elseif(isset($_GET['error']) && $_GET['error'] ==3){
            echo 'You have to login first';
        }elseif(isset($_GET['success']) && $_GET['success'] ==1){ // check the response from registerController
            echo 'Successfull registration, Email has been sent';
        }elseif(isset($_GET['success']) && $_GET['success'] ==2){ // check the response from registerController
            echo 'You have successfully verified your email address';
        }  
    ?>
</body>
</html>
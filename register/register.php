<html>
<body>
    <!-- Registration Form-->
    <form action="registerController.php" method="post">
        Registration:   <br>
            Name:<input type="text" name="name" required> max length 100 char<br> 
            E-mail:<input type="text" name="email" required><br>
            Password:<input type="password" name="password1" required> require atleast one uppercase letter, one number and min length 8 char<br>
            Confirm Password: <input type="password" name="password2" required><br>
    <input type="submit" value="submit">
    </form>
    <a href="../index.php">Login</a>
    <?php
        //check the error response
        if(isset($_GET['error']) && $_GET['error'] ==1){
            echo 'The two password should match';
        }elseif(isset($_GET['error']) && $_GET['error'] ==2){
            echo 'Password do not match the requiments';
        }elseif(isset($_GET['error']) && $_GET['error'] ==3){
            echo 'Please add valid email address';
        }elseif(isset($_GET['error']) && $_GET['error'] ==4){
            echo 'Email already in use';
        }elseif(isset($_GET['error']) && $_GET['error'] ==5){
            echo 'Too long email or name'; 
        }elseif(isset($_GET['error']) && $_GET['error'] ==6){
            echo 'Please fill all the input fields';
        }  
    ?>
</body>
</html>
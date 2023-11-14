<?php 
    require '../header.php';
    require '../connect.php';

    if(isset($_SESSION['s_id']) || isset($_SESSION['user_id'])) {
        header('location: /bills.php');
        exit;
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = strip_tags($_POST['username']);
        $password = strip_tags($_POST['password']);

        
        // Get data to database

        $sql = $con->prepare("SELECT * FROM customer WHERE username = ? LIMIT 1");

        $sql->execute(array($username));

        $userInfo = $sql->fetchAll();


        $count = $sql->rowCount();

        if($count) {
            
            if(password_verify($password, $userInfo[0]['password'])) {
                $_SESSION['user_id'] = $userInfo[0]['c_id'];
                $_SESSION['username'] = $userInfo[0]['username'];
                $_SESSION['phone'] = $userInfo[0]['phone_number'];
                $successMsg = 'Logged Successfully';

                header('Refresh: 3; url=/bills.php');
            }else {
                $errormsg_password = 'Sorry, this password deson\'t exist';
            }

        }else {
            $errormsg_username = 'Sorry, this username deson\'t exist';
        }


    }
?>

<div class="s-login store">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="Post" class="col-lg-6 col-md-8 col-12 bg-light pb-4 px-4 rounded shadow-sm">
            <h2 class="fs-3 text-center mb-3 color-two">Customer Login</h2>

            <div class="col-12 mb-4">
                <label class="mb-2 fs-5 mt-4">Username</label>
                <div class="position-relative">
                    <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                    <span class="astrick">*</span>
                </div>
                <div class="msg"><p class="error-msg"><?php echo $errormsg_username ?? null ?></p></div>
            </div>

            <label class="mb-2 fs-5">Password</label>
            <div class="position-relative">
                <input type="password" name="password" class="form-control" placeholder="Enter a complex password" required>
                <span class="astrick">*</span>
            </div>
            <div class="msg"><p class="error-msg"><?php echo $errormsg_password ?? null ?></p></div>
            <div class="msg"><p class="success-msg"><?php echo $successMsg ?? null ?></p></div>

            <p class="mb-0 mt-2">You haven't account yet? <a href="c_signup.php" class="color-two fw-bold text-decoration-underline">Signup</a></p>
            <input type="submit" value="Login" class="btn back-one col-4  mt-4 fw-bold">
        </form>
    </div>
</div>
    
<?php require '../footer.php';?>
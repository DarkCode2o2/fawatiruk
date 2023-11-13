<?php 
    require '../header.php';
    require '../connect.php';

    if(isset($_SESSION['store_name'])) {
        header('location: /bills.php');
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cr = $_POST['cr_number'];
        $password = strip_tags($_POST['password']);

        
        // Get data to database

        $sql = $con->prepare("SELECT * FROM store WHERE CR = ? LIMIT 1");

        $sql->execute(array($cr));

        $storeInfo = $sql->fetchAll();


        $count = $sql->rowCount();

        if($count) {

            if(password_verify($password, $storeInfo[0]['password'])) {
                $_SESSION['s_id'] = $storeInfo[0]['s_id'];
                $_SESSION['store_name'] = $storeInfo[0]['s_name'];
                $_SESSION['is_sub'] = $storeInfo[0]['is_sub'];
                $successMsg = 'Loggedin Successfully';
                header('Refresh: 3; url=/bills.php');
            }else {
                $errormsg_password = 'Sorry, this password deson\'t exist';
            }

        }else {
            $errormsg_cr = 'Sorry, this CR number deson\'t exist';
        }


    }
?>
    
<div class="s-login store">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="col-lg-6 col-md-8 col-12 bg-light pb-4 px-4 rounded shadow-sm">
            <h2 class="fs-1 text-center mb-3 color-two">Login</h2>

            <div class="col-12 mb-4">
                <label class="mb-2 fs-5 mt-4">CR number</label>
                <div class="position-relative">
                    <input type="number" name="cr_number" id="" class="form-control" required placeholder="Enter commercial registration">
                    <span class="astrick">*</span>
                </div>
                <div class="msg"><p class="error-msg"><?php echo $errormsg_cr ?? null ?></p></div>
            </div>
            
            <label class="mb-2 fs-5">Password</label>
            <div class="position-relative">
                <input type="password" name="password" class="form-control" required placeholder="Enter a complex password">
                <span class="astrick">*</span>
            </div>
            <div class="msg"><p class="error-msg"><?php echo $errormsg_password ?? null ?></p></div>

            <div class="msg"><p class="success-msg"><?php echo $successMsg ?? null ?></p></div>

            <p class="mb-0 mt-2">You haven't account yet? <a href="s_signup.php" class="color-two fw-bold text-decoration-underline">Signup</a></p>
            <input type="submit" value="Login" class="btn back-one col-4  mt-4 fw-bold">
        </form>
    </div>
</div>

    
<?php require '../footer.php';?>
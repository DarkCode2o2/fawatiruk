<?php 
    require '../header.php';
    include '../connect.php';

    if(isset($_SESSION['s_id']) || isset($_SESSION['user_id'])) {
        header('location: /bills.php');
        exit;
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $name = strip_tags($_POST['name']); 
        $password = strip_tags($_POST['password']);
        $cr = $_POST['cr_number'];
        $formError = array();
        
        // Validate Inputs 

        if(empty($cr)) {
            $formError[] = $errormsg_cr = 'Sorry, CR number field can\'t be empty!';
        }else {
            if(strlen($cr) < 8 ) {
                $formError[] = $errormsg_cr = 'Sorry, CR number can\'t be less then 8 chars!';
            }
        }

        if(empty($name)) {
            $formError[] = $errormsg_name = 'Sorry, name field can\'t be empty!';
        }else {
            if(strlen($name) < 2 ) {
                $formError[] = $errormsg_name = 'Sorry, name can\'t be less then 2 chars!';
            }
        }

        if(empty($password)) {
            $formError[] = $errormsg_password = 'Sorry, password field can\'t be empty!';
        }else {
            if(strlen($password) < 6 ) {
                $formError[] = $errormsg_password = 'Sorry, password can\'t be less then 6 chars!';
            }
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);


        // check is user has been taken 
        $is_taken = checker('s_name', 'store', $name);
        $is_taken2 = checker('CR', 'store', $cr);

        if(!$is_taken) {
            if(!$is_taken2) {

                if(empty($formError)) {

                    // Insert data to database
        
                    $sql = $con->prepare("INSERT INTO store (s_name, password, CR, date) VALUES (?,?,?, now())");
        
                    $sql->execute(array($name, $hashed, $cr));
        
                    $successMsg = "Signed up successfully";
                    
                    if($sql) {
                        header("Refresh: 3; url=s_login.php");
                    }
                }

            }else {
    
                $formError[] = $errormsg_taken2 = 'Sorry, this CR number has been taken!';
    
            }
        }else {

            $formError[] = $errormsg_taken = 'Sorry, this name has been taken!';

        }

    }



?>
    
<div class="s-signup store">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="Post" class="col-lg-6 col-md-8 col-12 bg-light pb-4 px-4 rounded shadow-sm">
            <h2 class="fs-1 text-center mb-3 color-two">Sign up</h2>
            <div class="col-12 pb-3">
                <label class="mb-2 fs-5">Store name</label>
                <div class="position-relative">
                    <input type="text" name="name" class="form-control"  placeholder="Enter store name">
                    <span class="astrick">*</span>
                </div>
                <div class="msg"><p class="error-msg"><?php echo $errormsg_name ?? null?></p></div>
                <div class="msg"><p class="error-msg"><?php echo $errormsg_taken  ?? null?></p></div>

            </div>
            <div class="col-12 pb-3">
                <label class="mb-2 fs-5">CR number</label>
                <div class="position-relative">
                    <input type="number" name="cr_number" id="" class="form-control"  placeholder="Enter commercial registration">
                    <span class="astrick">*</span>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_taken2  ?? null?></p></div>
                </div>

                <div class="msg"><p class="error-msg"><?php echo $errormsg_cr ?? null?></p></div>
            </div>

            <label class="mb-2 fs-5">Password</label>
            <div class="position-relative">
                <input type="password" name="password" class="form-control"  placeholder="Enter a complex password">
                <span class="astrick">*</span>
            </div>
            <div class="msg"><p class="error-msg"><?php echo $errormsg_password ?? null ?></p></div>
            <div class="msg"><p class="success-msg"><?php echo $successMsg ?? null?></p></div>
                
            <p class="mb-0 mt-2">You already have account? <a href="s_login.php" class="color-two fw-bold text-decoration-underline">Login</a></p>
            <input type="submit" value="Signup" class="btn back-one col-4  mt-4 fw-bold">
        </form>
    </div>
</div>

    
<?php require '../footer.php';?>
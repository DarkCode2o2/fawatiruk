<?php 
    require '../header.php';
    include '../connect.php';

    if(isset($_SESSION['s_id']) || isset($_SESSION['user_id'])) {
        header('location: /bills.php');
        exit;
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $username = strip_tags($_POST['username']); 
        $password = strip_tags($_POST['password']);
        $phone = strip_tags($_POST['phoneNumber']);

        $formError = array();

        // Validate Inputs 

        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];
        }else {
            $formError[] = $errormsg_email = 'Please enter a vaild email';
        }

        if(empty($username)) {
            $formError[] = $errormsg_username = 'Sorry, username field can\'t be empty!';
        }else {
            if(strlen($username) < 4 ) {
                $formError[] = $errormsg_username = 'Sorry, username can\'t be less then 4 chars!';
            }
        }

        if(!empty($phone)) {
            if (!preg_match('/^05\d{8}$/', $phone)) {
                $formError[] = $errormsg_phone = "Please enter a valid Saudi phone number starting with '05'.";
            } 
        }else {
            $formError[] = $errormsg_phone = "Sorry, this field can't be empty!";
        }

        if(empty($password)) {
            $formError[] = $errormsg_password = 'Sorry, password field can\'t be empty!';
        }else {
            if(strlen($password) < 6 ) {
                $formError[] = $errormsg_password = 'Sorry, password can\'t be less then 6 chars!';
            }
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);


        // check if the Number and username have been taken 

        $is_taken = checker('phone_number', 'customer', $phone);
        $is_taken2 = checker('username', 'customer', $username);

        if(!$is_taken) {
            if(!$is_taken2) {
                if(empty($formError)) {

                    // Insert data to database
    
                    $sql = $con->prepare("INSERT INTO customer (username, password, phone_number, date) VALUES (?,?,?, now())");
        
                    $sql->execute(array($username, $hashed, $phone));
        
                    $successMsg = "Signed up successfully";
    
                    if($sql) {
                        header("Refresh: 3; url=c_login.php");
                    }
                }
            }else {

                $formError[] = $errormsg_taken2 = 'Sorry, this username has been taken!';
            }
        }else {

            $formError[] = $errormsg_taken = 'Sorry, this number has been taken!';

        }

    }



?>
    
<div class="s-signup store">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="Post" class="col-lg-6 col-md-8 col-12 bg-light pb-4 px-4 rounded shadow-sm">
            <h2 class="fs-3 text-center mb-4 color-two">Customer signup</h2>
            <div class="d-md-flex">
                <div class="col-md-6 col-12 me-2">
                    <label class="mb-2 fs-5">Username</label>
                    <div class="position-relative">
                        <input type="text" name="username" class="form-control" placeholder="Enter username name"  value="<?php echo isset($_POST['username']) && !empty($_POST['username']) ? $_POST['username'] : ''?>">
                        <span class="astrick">*</span>
                    </div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_username ?? null?></p></div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_taken2 ?? null?></p></div>
                </div>
                <div class="col-md-6 col-12">
                    <label class="mb-2 fs-5 mt-md-0 mt-4">Email</label>
                    <div class="position-relative">
                        <input type="email" name="email" id="" class="form-control" placeholder="Enter email"  value="<?php echo isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : ''?>">
                        <span class="astrick">*</span>
                    </div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_email?? null?></p></div>
                </div>
            </div>
            <div class="d-md-flex">
                <div class="col-md-6 col-12 me-2">
                    <label class="mb-2 fs-5 mt-4">Phone number</label>
                    <div class="position-relative">
                        <input type="number" name="phoneNumber" class="form-control" placeholder="Example: 0539999375"  value="<?php echo isset($_POST['phoneNumber']) && !empty($_POST['phoneNumber']) ? $_POST['phoneNumber'] : ''?>">
                        <span class="astrick">*</span>
                    </div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_phone ?? null?></p></div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_taken ?? null?></p></div>
                </div>
                <div class="col-md-6 col-12">
                    <label class="mb-2 fs-5 mt-4">Password</label>
                    <div class="position-relative">
                        <input type="password" name="password" class="form-control"  placeholder="Enter a complex password">
                        <span class="astrick">*</span>
                    </div>

                    <div class="msg"><p class="error-msg"><?php echo $errormsg_password ?? null?></p></div>
                    
                </div>
            </div>
            
            <div class="msg"><p class="success-msg"><?php echo $successMsg ?? null?></p></div>
            
          
            <p class="mb-0 mt-2">You already have account? <a href="c_login.php" class="color-two fw-bold text-decoration-underline">Login</a></p>
            <input type="submit" value="Signup" class="btn back-one col-4  mt-4 fw-bold">
        </form>
    </div>
</div>

    
<?php require '../footer.php';?>
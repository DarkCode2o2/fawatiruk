<?php require 'header.php';?>
<div class="hero mt-md-4 mt-0">
    <div class="container d-flex flex-lg-row flex-column-reverse p-4">
        <div class="content">
            <h2>Easy and Secure Invoice Registration and Storage</h2>
            <p>
                Welcome to our comprehensive platform for easy and secure registration and storage of bills. We are here to provide you with the optimal solution for managing your bills in a convenient and efficient manner. With our innovative platform, you can now record all your bills and store them in a secure and organized place.
            </p>
            <a href="<?php echo isset($s_id) ? "create_bill.php" : '/auth/s_login.php'?>" class="btn back-one fw-bold shadow">Start now!</a>
        </div>
        <img src="images/hero.svg" alt="" class="img-fluid">
    </div>
</div>
<?php require 'footer.php'?>

<?php 

    include 'header.php';
    include 'connect.php';
    
    if($_SESSION['is_sub'] == 1) {
        header("Location: bills.php");
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $successMsg = 'Payment Successfully';
        $class = 'alert alert-success mt-2';

        $sql = $con->prepare("UPDATE store SET is_sub = ? WHERE s_id = ?");
        $sql->execute(array(1, $_SESSION['s_id']));

        if($sql) {
            $_SESSION['is_sub'] = 1;
            header("Refresh: 2; url=bills.php");
        }
    }
?>

    <div class="container d-flex justify-content-center align-items-center mt-4">
        <div class="payment p-4 mt-4 shadow rounded back-one w-50">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <h1>Confirm Your Payment</h1>
                <div class="first-row my-4">
                    <div class="mb-2">
                        <h3 class="h4">Name</h3>
                        <div class="input-field">
                            <input type="text" class="form-control" required placeholder="Name">
                        </div>
                    </div>
                    <div class="card-number mt-4">
                        <h3 class="h4">Card Number</h3>
                        <div class="input-field">
                            <input type="number" class="form-control" required placeholder="0000 0000 0000 0000">
                        </div>
                    </div>
                </div>
                <div class="second-row d-flex justify-content-between">
                    <div class="cvv col-6 me-2">
                        <h3 class="h4">CVC</h3>
                        <div class="">
                            <input type="text" class="form-control" required placeholder="XXX">
                        </div>
                    </div>
                    <div class="selection col-6 ">
                        <h3 class="h4">Expiration date</h3>
                        <div class="date d-flex justify-content-center">
                        <input type="date" class="form-control" required placeholder="00/00">
                    </div>
                </div>
                </div>
                <div class="pay-button">
                    <div class="cards text-end">
                        <img src="images/mc.png" alt="" class="img-fluid mt-2" style="width: 60px;">
                        <img src="images/vi.png" alt="" class="img-fluid mt-2" style="width: 60px;">
                        <img src="images/pp.png" alt="" class="img-fluid mt-2" style="width: 60px;">
                    </div> 
                </div>
                <p class="<?php echo $class ?? null ?>"><?php echo $successMsg ?? null ?></p>
                <input type="submit" value="Pay 159 SAR" class="btn back-two text-light fw-bold shadow-sm">
            </form>
        </div>
    </div>
<?php include 'footer.php'?>
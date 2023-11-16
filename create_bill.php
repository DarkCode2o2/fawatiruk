<?php 
    require 'header.php';
    require 'connect.php';

    if(!isset($_SESSION['s_id']) || $_SESSION['is_sub'] != 1) {
        header('location: /index.php');
        exit;
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $phone              = $_POST['phone_number'];
        $description        = strip_tags($_POST['description']);
        $date               = $_POST['date'];
        $warranty_period    = $_POST['warranty_period'];
        $sotre_id           = $_POST['store_id'];

        // Validate inputs
        $formError = array();

        $prodcuts = $_POST['products'];

        foreach($prodcuts as $prodcut) {
            if(empty($prodcut['pro_name']) || empty($prodcut['pro_price']) || empty($prodcut['quantity'])) {
                $formError [] = $errormsg_prodcuts = "Sorry, you need to add at least one item!";
            }
        }

        if(!empty($phone)) {
            if (!preg_match('/^05\d{8}$/', $phone)) {
                $formError[] = $errormsg_phone = "Plase enter a valid number start with '05'.";
            } 
        }else {
            $formError[] = $errormsg_phone = "Sorry, this field can't be empty!";
        }

        $splitDate = explode('-', $date)[0];

        $currentDate = date("Y-m-d");

        if(!empty($date)) {
            if(strlen($splitDate) > 4 || strlen($splitDate) < 4) {
                $formError[] =  $errormsg_date = "Please, enter a valid date!";
            }elseif($date > $currentDate) {
                $formError[] = $errormsg_date = "Enter a date earlier than the current date";
            }
        }else {
            $formError[] =  $errormsg_date = "Sorry, this field can't be empty!";
        }

        if(!empty($warranty_period)) {
            if(strlen($splitDate) > 4 || strlen($splitDate) < 4) {
                $formError[] =  $errormsg_warranty_period = "Please, enter a valid date!";
            }elseif($warranty_period < $currentDate) {
                $formError[] = $errormsg_warranty_period = "Enter a date later than the current date";
            }
        }else {
            $warranty_period = null;
        }
        
        $count = checker('phone_number', 'bill', intval($phone));

        if($count == 0) {
            if(empty($formError)) {
                $insert = $con->prepare("INSERT INTO bill (description, phone_number, warranty_period, date, store_id ) 
                                        VALUES (?,?,?,?,?)");
                $insert->execute(array($description, $phone, $warranty_period, $date, intval($sotre_id)));
                 

                if($insert) {
                    $invoiceId = $con->lastInsertId();

                    foreach($prodcuts as $prodcut) {
                        $proName = strip_tags($prodcut['pro_name']);
                        $proPrice = $prodcut['pro_price'];
                        $proQuantity = $prodcut['quantity'];
            
                        $stmt = $con->prepare("INSERT INTO products (name, price, quantity, date, bill_id ) 
                                        VALUES (?, ?, ?, now(), ?)");
                        $stmt->execute(array($proName, $proPrice, $proQuantity, intval($invoiceId)));
                 
                    }

                    $successMsg = "Added Successfully!";
                    
                    $_POST['phone_number'] = '';
                    $_POST['description'] = '';
                }

            }
        }else {
            $formError[] = $errormsg_token = "Sorry, this number has been taken! try another one";
        }


    }
?>
    
<div class="create-bill">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="col-lg-8 col-md-10 col-12 bg-light pb-4 px-md-4 px-2 rounded shadow-sm position-relative">
            <h2 class="fs-1 text-center mb-3 mt-4 color-two">Bill Info</h2>
            <input type="hidden" name="store_id" value="<?php echo $_SESSION['s_id'] ?>">

            <div class="d-flex flex-lg-row flex-column justify-content-center mb-2">
                <div class="col-lg-6 col-12 me-2">
                    <label class="mb-2 fs-5">Phone number</label>
                    <div class="position-relative">
                        <input type="number" name="phone_number"  class="form-control" placeholder="Example: 0539999376" value="<?php echo isset($_POST['phone_number']) && !empty($_POST['phone_number']) ? $_POST['phone_number'] : ''?>">
                        <span class="astrick">*</span>
                    </div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_phone ?? null ?></p></div>
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_token ?? null ?></p></div>
                </div>
                <div class="col-lg-6 col-12 me-2">
                    <label class="mb-2 fs-5 mt-md-0 mt-3">Date</label>
                        <input type="date" name="date" class="form-control" placeholder="Enter bill date">
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_date ?? null ?></p></div>
                </div>
            </div>

            <div class="d-flex flex-lg-row flex-column justify-content-center mb-2 mt-4 gap-2">
                <div class="col-lg-8 col-12">
                    <label class="mb-2 fs-5 mt-md-0 mt-2">Description</label>
                    <textarea name="description" rows="5" placeholder="Enter description....." class="form-control" value="<?php echo isset($_POST['description']) && !empty($_POST['description']) ? $_POST['description'] : ''?>"></textarea>
                </div>
                <div class="col-lg-4 col-12 me-2">
                    <label class="mb-2 fs-5 mt-md-0 mt-3">Warranty expiration date</label>
                        <input type="date" name="warranty_period" class="form-control" placeholder="Enter bill date">
                    <div class="msg"><p class="error-msg"><?php echo $errormsg_warranty_period ?? null ?></p></div>
                </div>
            </div>

            <hr class="color-two my-4">
            <div class="pro-area">
                <div class="add-pro d-flex position-relative justify-content-between align-items-center mt-4 gap-2 back-one p-4 rounded mb-4">
                    <div class="col-6 ">
                        <label class="mb-2 mt-md-0 mt-3">Name</label>
                        <div class="position-relative">
                            <input type="text" name="products[0][pro_name]" class="form-control" placeholder="Product name">
                            <span class="astrick">*</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="mb-2 mt-md-0 mt-3">Price</label>
                        <div class="position-relative">
                            <input type="number" name="products[0][pro_price]" class="form-control" placeholder="Product price" >
                            <span class="astrick">*</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="mb-2 mt-md-0 mt-3">Quantity</label>
                        <div class="position-relative">
                            <input type="number" name="products[0][quantity]" min="1" max="1000" class="form-control" placeholder="Quantity" value="1">
                            <span class="astrick">*</span>
                        </div>
                    </div>
                </div>
                <div class="msg"><p class="error-msg"><?php echo $errormsg_prodcuts ?? null ?></p></div>
            </div>

            <div class="msg"><p class="success-msg"><?php echo $successMsg ?? null ?></p></div>

            <input type="submit" value="Create" class="btn back-one hover mt-4 w-25 fw-bold">
            <p class="btn back-one hover fw-bold position-absolute end-0 mt-4 me-2" onclick="addPro()">Add product <i class="fa-solid fa-circle-plus"></i></p>
        </form>
    </div>
</div>

    
<?php require 'footer.php';?>
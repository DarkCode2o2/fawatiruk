<?php 
    require 'connect.php';
    require 'header.php';

    if(!isset($_SESSION['s_id'])) {
        
        if(!isset($_SESSION['username']) || isMatch($_SESSION['user_id']) == 0) {
            header("location: index.php");
            exit;
        }
    }

    $bill = isset($_GET['bill']) && is_numeric($_GET['bill']) ? intval($_GET['bill']) : 0;

    if($bill != 0) {
        $get = $con->prepare("SELECT bill.*, products.*, store.s_name FROM bill LEFT JOIN products ON bill.b_id = products.bill_id
            LEFT JOIN store ON store.s_id = bill.store_id WHERE bill.b_id = ?");

        $get->execute(array($bill));
        $count = $get->rowCount();

        if(!$count) {
            header("location: /index.php");
            exit;
        }

        $billInfo = $get->fetchAll();
    }else {
        header("location: /index.php");
        exit;
    }

    $expiryDate = $billInfo[0]['warranty_period'];



    if(!empty($expiryDate)) {
        $currentDate = date("Y-m-d");
        $diffInDays = 0;

        $expiryTimestamp = strtotime($expiryDate);
        $currentTimestamp = strtotime($currentDate);
    
      
        if($expiryTimestamp <= $currentTimestamp) {
            $expiryTimestamp = 0;
        }else {
            $diffInSeconds = $expiryTimestamp - $currentTimestamp;
            $diffInDays = floor(abs($diffInSeconds) / (60 * 60 * 24));
        }
    }else {
        $diffInDays = null;
    }


    $sum = 0;

?>

<div class="bill-info">
    <div class="container bg-light shadow-sm rounded p-2 col-lg-6 col-md-8 col-12">
        <div class="info-box mt-4">
            <div class="logo p-4 d-flex justify-content-between align-items-center mb-4">
                <div class="right text-center">
                    <img src="images/logo.png" alt="" class="img-fluid rounded-circle shadow-sm mb-2">
                    <p class="color-two fw-bold">Fawatiruk</p>
                </div>
                <div class="left fs-2 color-two fw-bold">
                    <?php echo $billInfo[0]['b_id'] ?>#
                </div>
            </div>
            <div class="content p-4 rounded">
                <ul class="p-0">
                    <li>From store: <span><?php echo $billInfo[0]['s_name']?></span></li>
                    <?php if(!empty($billInfo[0]['warranty_period'])):
                            if($diffInDays <= 7 && $diffInDays >= 1) { ?>
                                <li>Warranty: <span class="bg-light p-2 rounded d-md-inline d-block warranty" style="color:#E91E63;"> <?php echo $diffInDays?> days left until the warranty expires</span></li>
                           <?php }elseif($diffInDays  == 0) { ?>
                                
                            <?php }else { ?>
                                <li>Warranty: <span> <?php echo $diffInDays?> days left until the warranty expires </span></li>
                            <?php }?>
                        
                    <?php endif;?>
                    <li>Description: <span><?php echo $billInfo[0]['description'] ? $billInfo[0]['description'] : "There is no description"?></span></li>
                    <li>Customer number: <span><?php echo $billInfo[0]['phone_number'] ?></span></li>
                    <li>Date: <span><?php echo $billInfo[0][4] ?></span></li>
                </ul>
            </div>
        </div>
        <?php if(!empty($billInfo[0]['price'])):?>
            <div class="pro-info">
                <table class="table shadow-sm text-center">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($billInfo as $key => $bill):?> 
                            <tr>
                                <th scope="row"><?php echo ++$key ?></th>
                                <td><?php echo $bill['name']?></td>
                                <td><?php echo $bill['price']?></td>
                                <td><?php echo $bill['quantity']?></td>
                            </tr> 
                            <?php $sum += $bill['price'] * $bill['quantity']?>
                        <?php endforeach;?>                 
                    </tbody>
                </table>
                <div class="d-flex align-items-center justify-content-between download-btn">
                    <p class="p-2 color-two h4 total mt-4">Total: <span class="fw-bold"><?php echo $sum?> SAR</span></p>
                </div>
            </div>
        <?php endif;?>
        <a href="preview.php?id=<?php echo $billInfo[0]['b_id']?>" class="print-button btn back-one mb-2 mx-auto w-50 col-6 fw-bold">Download as PDF</a>
    </div>
</div>


<?php require 'footer.php'?>

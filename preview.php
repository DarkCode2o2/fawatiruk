<?php 
    require 'connect.php';
    session_start();

    if(!isset($_SESSION['s_id'])) {
        
        if(!isset($_SESSION['username']) || isMatch($_SESSION['user_id']) == 0) {
            header("location: index.php");
            exit;
        }
    }

    $bill = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

    if($bill != 0) {
        $get = $con->prepare("SELECT bill.*, products.* FROM bill LEFT JOIN products ON bill.b_id = products.bill_id WHERE bill.b_id = ?");

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

        if($expiryTimestamp < $currentTimestamp) {
            $expiryTimestamp = 0;
        }else {
        
            $diffInSeconds = $expiryTimestamp - $currentTimestamp;
            $diffInDays = floor(abs($diffInSeconds) / (60 * 60 * 24));
        }
    }else {
        $diffInDays = 'null';
    }


    $sum = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <title>Fawatiruk</title>
</head>
<body>
    <div class="bill-info">
        <div class="container bg-light shadow-sm rounded p-2 col-10 ">
            <h1 class="color-two text-center">Products invoice</h1>
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
                        <?php if(!empty($billInfo[0]['warranty_period'])):?>
                            <li>Warranty: <span> <?php echo $diffInDays?> days left until the warranty expires </span></li>
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
                    <p class="p-2 color-two h4 total mt-4">Total: <span class="fw-bold"><?php echo $sum?> SAR</span></p>
                </div>
                <?php endif;?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="/js/script.js"></script>
</body>
</html>

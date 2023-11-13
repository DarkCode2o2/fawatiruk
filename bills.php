<?php 
    require 'connect.php';
    require 'header.php';


    if(isset($_SESSION['s_id']) || isset($_SESSION['username'])) {


        if(isset($_SESSION['is_sub']) && intval($_SESSION['is_sub']) != 0) {

            $get = $con->prepare("SELECT * FROM bill WHERE store_id = ?");
            $get->execute(array($_SESSION['s_id']));
            $billsInfo = $get->fetchAll();
    
        }
        elseif(isset($_SESSION['username'])) {

            $sql = $con->prepare("SELECT * FROM store");

            $sql->execute(array());

            $storesName = $sql->fetchAll();
        }

        if(!empty($billsInfo)) {

            $expiryDate = $billsInfo[0]['warranty_period'];
        
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
                $diffInDays = null;
            }
        }
    
        $sort = 'ASC';
        $sort_array = array('ASC', 'DESC');
    
        if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {
            $sort = $_GET['sort'];
        }
    
        if(isset($_GET['search'])) {
            $search = $_GET['search'];
            $stmt = $con->prepare("SELECT * FROM bill WHERE phone_number LIKE '$search%' AND store_id = ? ORDER BY b_id $sort");
            $stmt->execute(array($_SESSION['s_id']));
    
            $allBills = $stmt->fetchAll();
        }
    
    }else {
        header("Location: index.php");
    }      
?>

<div class="bills">
    <div class="container">
        <?php if(isset($_SESSION['s_id'])) {?>
            <?php if($_SESSION['is_sub'] != 0) { ?>
            
                <?php if(!empty($billsInfo)) {?>
                    <form method="GET" class="d-flex my-4 mx-auto justify-content-center align-items-center" role="search">
                        <input class="form-control me-2 p-2 bg-light w-50" type="search" name="search" placeholder="Search by customer number: 0569999334" aria-label="Search">
                        <button class="btn back-one hover" type="submit">Search</button>
                        <a href="create_bill.php" class="btn btn-sm back-one fw-bold ms-2">Create bill</a>
                    </form>

                        <?php if(!empty($allBills)) { ?>
                            <div class="sorting mt-4 mb-2">
                                <i class="fa fa-sort"></i> <span class="">Sorting:</span>
                                <a href="?search=<?php echo $search?>&sort=ASC" class="<?php echo $sort == 'ASC' ? 'active' : ''?>">ASC</a> |
                                <a href="?search=<?php echo $search?>&sort=DESC" class="<?php echo $sort == 'DESC' ? 'active' : ''?>">DESC</a>
                            </div>
                            <div class="show_bills mx-auto back-one p-4 mb-4 rounded shadow-sm">
                                    <?php foreach($allBills as $billInfo):?>
                                        <a href="show_bill.php?bill=<?php echo $billInfo['b_id'] ?>" class="info-box mt-4 p-4 bg-white shadow-sm rounded">
                                            <div class="logo d-flex justify-content-between align-items-center mb-4">
                                                <div class="right text-center">
                                                    <img src="images/logo.png" alt="" class="img-fluid rounded-circle shadow-sm mb-2">
                                                    <p class="color-two fw-bold">Fawatiruk</p>
                                                </div>
                                                <div class="left fs-4 color-two fw-bold">
                                                    <?php echo $billInfo['b_id'] ?>#
                                                </div>
                                            </div>
                                            <div class="content p-4 rounded">
                                                <ul class="p-0">
                                                    <li>Phone number: <span> <?php echo $billInfo['phone_number'] ?></span></li>
                                                    <li>Description: <span><?php echo $billInfo['description'] ? $billInfo['description'] : "There is no description"?></span></li>
                                                    <li>Date: <span><?php echo $billInfo['date'] ?></span></li>
                                                </ul>
                                            </div>
                                            <span class="show-more">Show more <i class="fa-solid fa-expand fs-1"></i></span>
                                        </a>
                                    <?php endforeach;?>
                                </div>
                            <?php }else { ?>
                                <?php 
                                    if(isset($allBills)) { ?>
                                    
                                    <div class="not-found mt-4">
                                            <p class='h1 text-center'> Can't find (<?php echo $_GET['search'] ?> )</p>
                                            <img class="img-fluid " src='images/not_found.avif'>
                                    </div>

                                    <?php }?>
                            <?php };?>
                <?php }else { ?>
                    <p class="alert alert-info">There are not bills to show <a href="create_bill.php" class="color-two text-decoration-underline fw-bold">Create bill</a></p>
                <?php }?>
            <?php }else { ?>
                    <div class="sub-card back-one p-4 rounded shadow-sm w-75 mx-auto">
                        <h5 class="color-two mb-4 fw-bold">
                            Join us now and enjoy creating your invoices with ease and effectiveness for only 159 riyals per month. You will benefit from a set of features that include:
                        </h5>
                        <ul class="" style="list-style: auto;">
                            <li class="mb-2 color-two fw-bold">
                                Data Protection: We put in place strong protection for your and your customers' data, ensuring the confidentiality and security of information.
                            </li>
                            <li class="mb-2 color-two fw-bold">
                                Customer Search: The powerful search system allows you to easily find customer data and previous billing details, saving time and effort in customer management.
                            </li>
                            <li class="mb-2 color-two fw-bold">
                                Add products and prices: You can manage your product database, register new products and update prices easily, making the process of creating invoices easier and easier.
                            </li>
                        </ul>
                        <a href="/payment.php" class="btn back-two text-light shadow-sm fw-bold">Subsicribe now!</a>
                    </div>
            <?php } ?>
        <?php }else { ?>
            <div class="user-billinfo">
                <div class="container">
                    <p class="main-title">All Bills</p>
                    <?php foreach($storesName as $store):?>
                        <div class="info-boxes bg-light p-4 rounded shadow-sm bg-light  rounded">  
                            <h2 class="title mb-4 color-two" id="<?php echo $store['s_id']?>">
                                <?php echo $store['s_name']?><i class="arrow-title fa-solid fa-chevron-down ms-2 fs-3"></i>
                            </h2>
                            <div class="info-box">

                            </div>
                        </div>                        
                    <?php endforeach;?>
     
                </div>
            </div>
        <?php }?>
    </div>
</div>

<?php require 'footer.php'?>

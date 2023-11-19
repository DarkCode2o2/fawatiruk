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
          
            $uniqueNames = [];

            $sql = $con->prepare("SELECT store.s_name, bill.* FROM store INNER JOIN bill ON bill.store_id = store.s_id WHERE bill.phone_number = ? ");

            $sql->execute(array($_SESSION['phone']));

            $storesName = $sql->fetchAll();

            foreach($storesName as $name) {
                if(!in_array($name['s_name'], $uniqueNames)) {
                    $uniqueNames[$name['store_id']] = $name['s_name'];
                }
            }

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
    
        $sort = 'DESC';
        $sort_array = array('ASC', 'DESC');
    
        if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {
            $sort = $_GET['sort'];
        }
        
        // Store Search 
        if(isset($_GET['search']) && isset($_SESSION['s_id'])) {
            $search = $_GET['search'];
            $stmt = $con->prepare("SELECT * FROM bill WHERE phone_number LIKE '$search%' AND store_id = ? ORDER BY b_id $sort");
            $stmt->execute(array($_SESSION['s_id']));
    
            $allBills = $stmt->fetchAll();
        }

        // User Search 

        if(isset($_SESSION['username']) && isset($_GET['search'])) {

            if(isset($_GET['type']) && $_GET['type'] == 'store') {

                $name = $_GET['search'];
                $stmt = $con->prepare("SELECT store.*, bill.* FROM store INNER JOIN bill ON store.s_id = bill.store_id WHERE s_name LIKE '$name%' AND bill.phone_number = ? ORDER BY b_id $sort");
                $stmt->execute(array($_SESSION['phone']));
        
                $userSearchData = $stmt->fetchAll();


            }elseif(isset($_GET['type']) && $_GET['type'] == 'date') {

                $date = $_GET['search'];

                $stmt = $con->prepare("SELECT store.*, bill.* FROM store INNER JOIN bill ON store.s_id = bill.store_id WHERE bill.date LIKE '$date%' AND bill.phone_number = ? ORDER BY b_id $sort");
                $stmt->execute(array($_SESSION['phone']));
        
                $userSearchData = $stmt->fetchAll();

            }
        }


        if(!isset($allBills) && isset($_SESSION['s_id'])) {
            $stmt = $con->prepare("SELECT * FROM bill WHERE store_id = ? ORDER BY b_id $sort LIMIT 5");
            $stmt->execute(array($_SESSION['s_id']));
            $latestBills = $stmt->fetchAll();
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
                        <a href="create_bill.php" class="btn back-one ms-2">Create bill</a>
                    </form>

                    <!-- Latest Bills  -->
                    <?php if(!isset($allBills)) { ?>
                        <div class="sorting mt-4 mb-2">
                            <i class="fa fa-sort"></i> <span class="">Sorting:</span>
                            <a href="?sort=ASC" class="<?php echo $sort == 'ASC' ? 'active' : ''?>">ASC</a> |
                            <a href="?sort=DESC" class="<?php echo $sort == 'DESC' ? 'active' : ''?>">DESC</a>
                        </div>
                        <div class="latest-bills back-one p-4  rounded shadow-sm">
                            <div class="d-flex justify-content-between">
                                <p class="color-two h3">Welcome <?php echo $_SESSION['store_name'] ?></p>
                                <p class="color-two h3">Latest Bills</p>
                            </div>
                            <div class="show_bills mx-auto py-0">
                                    <?php foreach($latestBills as $billInfo):?>
                                        <a href="show_bill.php?bill=<?php echo $billInfo['b_id'] ?>" class="info-box mt-4 p-2 bg-white shadow-sm rounded d-flex justify-content-between flex-column">
                                            <div class="logo d-flex justify-content-between align-items-center mb-4 px-2">
                                                <div class="right text-center ">
                                                    <img src="images/logo.png" class="img-fluid rounded-circle shadow-sm mb-2">
                                                    <p class="color-two fw-bold">Fawatiruk</p>
                                                </div>
                                                <div class="left fs-4 color-two fw-bold">
                                                    <?php echo $billInfo['b_id'] ?>#
                                                </div>
                                            </div>
                                            <div class="content p-2 rounded">
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
                        </div>
                    <?php } ?>
                        
                        <?php if(!empty($allBills)) { ?>
                            <div class="sorting mt-4 mb-2">
                                <i class="fa fa-sort"></i> <span class="">Sorting:</span>
                                <a href="?search=<?php echo $search?>&sort=ASC" class="<?php echo $sort == 'ASC' ? 'active' : ''?>">ASC</a> |
                                <a href="?search=<?php echo $search?>&sort=DESC" class="<?php echo $sort == 'DESC' ? 'active' : ''?>">DESC</a>
                            </div>
                                <div class="show_bills mx-auto back-one p-4 mb-4 rounded shadow-sm">
                                        <?php foreach($allBills as $billInfo):?>
                                            <a href="show_bill.php?bill=<?php echo $billInfo['b_id'] ?>" class="info-box mt-4 p-2 bg-white shadow-sm rounded d-flex justify-content-between flex-column">
                                                <div class="logo d-flex justify-content-between align-items-center mb-4 px-2">
                                                    <div class="right text-center">
                                                        <img src="images/logo.png" class="img-fluid rounded-circle shadow-sm mb-2">
                                                        <p class="color-two fw-bold">Fawatiruk</p>
                                                    </div>
                                                    <div class="left fs-4 color-two fw-bold">
                                                        <?php echo $billInfo['b_id'] ?>#
                                                    </div>
                                                </div>
                                                <div class="content p-2 rounded">
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
                    <form method="GET" class="d-flex mx-auto col-lg-8 col-md-10 col-12 justify-content-center align-items-center  mb-5" role="search">

                        <input class="form-control p-2 bg-light rounded-end-0 user-search" type="search" name="search" placeholder="Search by: Store name | Date" aria-label="Search">

                        <select name="type" onchange="checkSearchType()" class="search-type btn btn-primary btn-lg px-0 fs-6 rounded-start-0">
                            <option value="store" class="bg-light text-black" selected>Name</option>
                            <option value="date" class="date bg-light text-black">Date</option>
                        </select>

                        <button class="btn btn-primary ms-2" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

                    </form>

                    <?php if(isset($_GET['type']) &&  empty($userSearchData)) { ?>

                        <div class="not-found mt-4">
                            <p class='h1 text-center'> Can't find (<?php echo $_GET['search'] ?> )</p>
                            <img class="img-fluid " src='images/not_found.avif'>
                        </div>
                    <?php }?>

                    <!-- Get search data  -->
                    <?php if(isset($_GET['type']) && !empty($userSearchData)): ?>
                            <div class="sorting mt-4 mb-2">
                                <i class="fa fa-sort me-1"></i><span>Sorting:</span>
                                <a href="?search=<?php echo $_GET['search']?>&type=<?php echo $_GET['type'] ?>&sort=ASC" class="<?php echo $sort == 'ASC' ? 'active' : ''?>">ASC</a> |
                                <a href="?search=<?php echo $_GET['search']?>&type=<?php echo $_GET['type'] ?>&sort=DESC" class="<?php echo $sort == 'DESC' ? 'active' : ''?>">DESC</a>
                            </div>
                            <div class="back-one p-4 rounded shadow-sm">
                                <div class="show_bills mx-auto py-0">
                                        <?php foreach($userSearchData as $data):?>
                                            <a href="show_bill.php?bill=<?php echo $data['b_id'] ?>" class="info-box mt-4 p-2 bg-white shadow-sm rounded d-flex justify-content-between flex-column  position-relative">
                                                <div class="logo d-flex justify-content-between align-items-center mb-4 px-2">
                                                    <div class="right text-center">
                                                        <img src="images/logo.png" class="img-fluid rounded-circle shadow-sm mb-2">
                                                        <p class="color-two fw-bold">Fawatiruk</p>
                                                    </div>
                                                    <div class="left fs-4 color-two fw-bold">
                                                        <?php echo $data['b_id'] ?>#
                                                    </div>
                                                </div>
                                                <div class="content p-2 rounded">
                                                    <ul class="p-0">
                                                        <li>From store: <span> <?php echo $data['s_name'] ?></span></li>
                                                        <li>Phone number: <span> <?php echo $data['phone_number'] ?></span></li>
                                                        <li>Description: <span><?php echo $data['description'] ? $data['description'] : "There is no description"?></span></li>
                                                        <li>Date: <span><?php echo $data['date'] ?></span></li>
                                                    </ul>
                                                </div>
                                                <span class="show-more">Show more <i class="fa-solid fa-expand fs-1"></i></span>
                                            </a>
                                        <?php endforeach;?>
                                </div>
                            </div>

                    <?php endif;?>

                    <!-- Show all my bills  -->
                    <?php if(!isset($_GET['type'])):?>
                        <div class="sorting mt-4 mb-2">
                            <i class="fa fa-sort"></i> <span>Sorting:</span>
                            <span class="sorting-btn active">ASC</span> |
                            <span class="sorting-btn">DESC</span>
                        </div>
                        <?php foreach($uniqueNames as $key => $name):
                            ?>
                                <div class="info-boxes bg-light p-4 shadow-sm bg-light">  
                                    <h2 class="title mb-4 color-two" id="<?php echo $key?>">
                                        <?php echo $name?><i class="arrow-title fa-solid fa-chevron-down ms-2 fs-3"></i>
                                    </h2>
                                    <div class="info-box">

                                    </div>
                                </div>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            </div>
        <?php }?>
    </div>
</div>

<?php require 'footer.php'?>

<?php

session_start();
require 'connect.php';

if(isset($_SESSION['s_id']) || isset($_SESSION['username'])) {
    if(isset($_SESSION['is_sub']) && intval($_SESSION['is_sub']) != 0) {

        $get = $con->prepare("SELECT * FROM bill WHERE store_id = ?");
        $get->execute(array($_SESSION['s_id']));
        $billsInfo = $get->fetchAll();

    }
    elseif(isset($_SESSION['username'])) {

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $storeid = $_POST['storeid'];
            
            if(isset($_POST['sort'])) {
                $sort = $_POST['sort'];
            }else {
                $sort = 'DESC';
            }


            $get = $con->prepare("SELECT bill.*, products.* FROM store LEFT JOIN bill ON store.s_id = bill.store_id 
                            LEFT JOIN products ON products.bill_id = bill.b_id WHERE bill.phone_number = ? AND store.s_id = ? ORDER BY bill.b_id $sort");
            
            $get->execute(array($_SESSION['phone'], $storeid));
            
            $billsInfo = $get->fetchAll();
            

            header('Content-Type: application/json;charset=utf-8');

            echo json_encode($billsInfo);

            return;
        }

    }
}

<?php 
    $dsn = 'mysql:hot=localhost;dbname=fawatiruk';
    $user = 'root';
    $password = '';

    try {
        $con = new PDO($dsn, $user, $password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
    }catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
        

    // Check if user has been taken 

    function checker($row, $table, $value) {
        global $con;

        $sql = $con->prepare("SELECT $row FROM $table WHERE $row = ?"); 

        $sql->execute(array($value));

        $count = $sql->rowCount();
        
        return $count;
    }

    // Check if phone number is match

    function isMatch($id) {
        global $con;

        $sql = $con->prepare("SELECT customer.phone_number,
                            customer.c_id,
                            bill.phone_number
                     FROM customer
                     INNER JOIN bill 
                     ON customer.phone_number = bill.phone_number
                     WHERE customer.c_id = ?");

        $sql->execute(array($id));

        $count = $sql->rowCount();
        
        return $count;
    }
?>

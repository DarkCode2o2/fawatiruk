<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/all.min.css?v6">
    <link rel="stylesheet" href="/css/style.css?v6">
    <title>Fawatiruk</title>
</head>
<body>
    <header class="bg-light shadow-sm py-2">
        <nav class="navbar navbar-expand-lg bg-body-tertiary container">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Fawatiruk</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 justify-content-end gap-2 gap-lg-0   align-items-start align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link main-link" aria-current="page" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link main-link" href="/about.php">About us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link main-link " href="/contact.php">Contact us</a>
                    </li>
                    <?php if(isset($_SESSION['store_name']) || isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <a class="nav-link main-link " href="/bills.php">Bills</a>
                    </li>
                    <?php endif;?>
                    <?php if(isset($_SESSION['store_name']) || isset($_SESSION['username'])) {?>
                        <li class="nav-item dropdown back-one hover rounded shadow-sm fw-bold">
                            <a class="dropdown-item m-2 fw-bold" href="/auth/logout.php">Logout</a>
                        </li>
                    <?php }else { ?>
                        <li class="nav-item dropdown back-one hover rounded shadow-sm fw-bold">
                            <a class="nav-link p-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Login
                            </a>
                            <ul class="dropdown-menu m-2">
                                <li><a class="dropdown-item" href="/auth/s_login.php">Login as Store</a></li>
                                <li><a class="dropdown-item" href="/auth/c_login.php">Login as User</a></li>
                            </ul>
                        </li>
                    <?php }; ?>
                </ul>
                </div>
            </div>
        </nav>
    </header>


<?php require 'header.php'?>
    <div class="contact">
        <div class="container">
            <form action="" method="Post" class="col-lg-6 col-12 bg-light mx-auto pb-4 px-4 rounded shadow-sm">
                <div class="title color-two">Contact us</div>
                <div class="d-flex flex-lg-row flex-column">
                    <div class="col-lg-6 col-12 me-2">
                        <label class="mb-2 mt-lg-0 mt-2 color-two">Full name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your Full name">
                    </div>
                    <div class="col-lg-6 col-12 mb-4">
                        <label class="mb-2 mt-lg-0 mt-2 color-two">Email</label>
                        <input type="email" name="email" id="" class="form-control" placeholder="Enter your email">
                    </div>
                </div>

                <label class="mb-2 color-two">Message</label>
                <textarea name="message" class="form-control"></textarea>
                <input type="submit" value="Send" class="btn back-one hover mt-4 w-25 fw-bold">
            </form>
        </div>
    </div>
<?php require 'footer.php'?>

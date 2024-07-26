<?php 
    require_once "inc/header.php";
?>
<!-- =============================================================================================== -->

<main>
<style>
    .large-text {
        font-size: 65px;
        font-weight: 900;
    }

    /* Media Query cho màn hình nhỏ hơn 768px */
    @media (max-width: 768px) {
        .large-text {
            font-size: 60px;
        }
    }

    /* Media Query cho màn hình nhỏ hơn 576px */
    @media (max-width: 576px) {
        .large-text {
            font-size: 45px;
        }
    }
</style>
<section class="contact-section" style="background-color: #F8F8FF">
    <div class="container">

        <div class="row">
            <div class="col-12 text-center">
                <div class="large-text">PHIẾU GỬI</div>
            </div>
            <div class="col-lg-12">
                <form class="form-contact contact_form" action="contact_process.php" method="post" id="contactForm" novalidate="novalidate">
                    <div class="row">
                        <div class="col-md-6">
                            
                            <div class="card form-group box-sender">
                                <div class="card-header"></div>
                                <div class="card-body">
                                    <label class="form-label my-3"></label>
                                    <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Subject'" placeholder="Enter Subject">
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            
                            <div class="card form-group box-sender">
                                <div class="card-header"></div>
                                <div class="card-body">
                                    <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Subject'" placeholder="Enter Subject">
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control valid" name="name" id="name" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your name'" placeholder="Enter your name">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control valid" name="email" id="email" type="email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email address'" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Subject'" placeholder="Enter Subject">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="button button-contactForm boxed-btn">Send</button>
                    </div>
                </form>
            </div>
        
        </div>
    </div>

    </div>
</section>

</main>

<!-- =============================================================================================== -->
<?php 
    require_once "inc/footer.php";
?>
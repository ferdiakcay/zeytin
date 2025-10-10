<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>
<?php
session_destroy();
?>

<head>

    <title><?php echo $language["Logout"]; ?> | Urca Tur</title>

    <?php include 'layouts/head.php'; ?>

    <?php include 'layouts/head-style.php'; ?>

</head>

<?php include 'layouts/body.php'; ?>
<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-xxl-3 col-lg-4 col-md-5">
                <div class="auth-full-page-content d-flex p-sm-5 p-4">
                    <div class="w-100">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4 mb-md-5 text-center">
                                <a href="index.php" class="d-block auth-logo">
                                    <img src="assets/images/logo.png" alt="" height="50"> <span class="logo-txt"></span>
                                </a>
                            </div>
                            <div class="auth-content my-auto">
                                <div class="text-center">
                                    <div class="avatar-xl mx-auto">
                                        <div class="avatar-title bg-light text-primary h1 rounded-circle">
                                            <i class="bx bxs-user"></i>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-2">
                                        <h5>Çıkış Yaptınız!</h5>
                                        <p class="text-muted font-size-15">Bizi Kullandığınız için Teşekkürler </p>
                                        <div class="mt-4">
                                            <a href="login.php" class="btn btn-primary w-100 waves-effect waves-light">Giriş Yap</a>
                                        </div>
                                    </div>
                                </div>

                             
                            </div>
                            <div class="mt-4 mt-md-5 text-center">
                                <p class="mb-0">© <script>
                                        document.write(new Date().getFullYear())
                                    </script> Kodlasoft <i class="mdi mdi-heart text-danger"></i> by Design</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col-xxl-9 col-lg-8 col-md-7">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
                  
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div>
<?php include 'layouts/vendor-scripts.php'; ?>
<script src="assets/js/pages/pass-addon.init.js"></script>

<script src="assets/js/pages/feather-icon.init.js"></script>

</body>

</html>
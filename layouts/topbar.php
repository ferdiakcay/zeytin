
 <header id="page-topbar">
     <div class="navbar-header">
         <div class="d-flex">
             <!-- LOGO -->
             <div class="navbar-brand-box">
                 <a href="index.php" class="logo logo-dark">
                     <span class="logo-sm">
                         <img src="assets/images/logo.png" alt="" height="50">
                     </span>
                     <span class="logo-lg">
                        <img src="assets/images/logo.png" alt="" height="50"> <span class="logo-txt"></span>
                     </span>
                 </a>

                 <a href="/" class="logo logo-light">
                     <span class="logo-sm">
                         <img src="assets/images/logo.png" alt="" height="50">
                     </span>
                     <span class="logo-lg">
                         <img src="assets/images/logo.png" alt="" height="34"> <span class="logo-txt">Zeytin Alım</span>
                     </span>
                 </a>
             </div>

             <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                 <i class="fa fa-fw fa-bars"></i>
             </button>

             <!-- App Search-->
             <!-- <form class="app-search d-none d-lg-block">
                 <div class="position-relative">
                     <input type="text" class="form-control" placeholder="Ara...">
                     <button class="btn btn-primary" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                 </div>
             </form> -->
         </div>

         <div class="d-flex">

             <div class="dropdown d-inline-block d-lg-none ms-2">
                 <button type="button" class="btn header-item" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i data-feather="search" class="icon-lg"></i>
                 </button>
                 <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">

                     <form class="p-3">
                         <div class="form-group m-0">
                             <div class="input-group">
                                 <input type="text" class="form-control" placeholder="Ara ..." aria-label="Search Result">

                                 <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>

           

            
                <form action="islem.php?tema=degis" >
                 <button  type="submit" name="tema" class="btn header-item" >
                    <?php 
                    
                    if($sorguCompany['tema']==0){ 
                        ?>
                     <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                     <?php } else {
                      
                        ?>
                     <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                     <?php }?>
                 </button>
                 </form>
           

           

           
             <div class="dropdown d-inline-block">
                 <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    
                     <span class="d-none d-xl-inline-block ms-1 fw-medium setting_user_name" id="setting_user_name">Admin</span>
                     <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                 </button>
                 <div class="dropdown-menu dropdown-menu-end">
                     <!-- item-->
                     
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item" href="logout.php"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Çıkış</a>
                 </div>
             </div>

         </div>
     </div>
 </header>
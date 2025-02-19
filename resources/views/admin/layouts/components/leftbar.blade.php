<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a class='logo logo-light' href='index.html'>
                    <span class="logo-sm">
                        <img src="{{ asset('admins/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admins/images/logo-light.png')}}" alt="" height="24">
                    </span>
                </a>
                <a class='logo logo-dark' href='index.html'>
                    <span class="logo-sm">
                        <img src="{{ asset('admins/images/logo-sm.png')}}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admins/images/logo-dark.png')}}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="#sidebarDashboards" data-bs-toggle="collapse">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarDashboards">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='index.html'>Analytical</a>
                            </li>
                            <li>
                                <a class='tp-link' href='ecommerce.html'>E-commerce</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- <li>
                        <a href="landing.html" target="_blank">
                            <i data-feather="globe"></i>
                            <span> Landing </span>
                        </a>
                    </li> -->

                <li class="menu-title">Pages</li>

                <li>
                    <a href="#sidebarAuth" data-bs-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> Authentication </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='auth-login.html'>Log In</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-register.html'>Register</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-recoverpw.html'>Recover Password</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-lock-screen.html'>Lock Screen</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-confirm-mail.html'>Confirm Mail</a>
                            </li>
                            <li>
                                <a class='tp-link' href='email-verification.html'>Email Verification</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-logout.html'>Logout</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarError" data-bs-toggle="collapse">
                        <i data-feather="alert-octagon"></i>
                        <span> Product </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarError">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='{{route('products.index')}}'>List Product</a>
                            </li>
                            <li>
                                <a class='tp-link' href='{{route('product_variants.index')}}'>Product variants</a>
                            </li>
                            <li>
                                <a class='tp-link' href='error-503.html'>Error 503</a>
                            </li>
                            <li>
                                <a class='tp-link' href='error-429.html'>Error 429</a>
                            </li>
                            <li>
                                <a class='tp-link' href='offline-page.html'>Offline Page</a>
                            </li>
                        </ul>
                    </div>
                </li>

               
                <li>
                    <a class='tp-link' href='{{route('categories.index')}}'>
                        <i data-feather="package"></i>
                        <span> Category </span>
                    </a>
                </li>

                                
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
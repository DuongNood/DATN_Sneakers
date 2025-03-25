<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a class='logo logo-light' href='index.html'>
                    <span class="logo-sm">
                        <img src="{{ asset('admins/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admins/images/logo-light.png') }}" alt="" height="24">
                    </span>
                </a>
                <a class='logo logo-dark' href='index.html'>
                    <span class="logo-sm">
                        <img src="{{ asset('admins/images/logo-sm.png') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('admins/images/logo-dark.png') }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="#sidebarDashboards" data-bs-toggle="collapse" href='#'>
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title">Pages</li>

                <li>
                    <a href="#sidebarError" data-bs-toggle="collapse">
                        <i data-feather="list"></i>
                        <span> Quản lý sản phẩm </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarError">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='{{ route('admin.products.index') }}'>Danh sách sản phẩm</a>
                            </li>
                            <li>
                                <a class='tp-link' href='{{ route('admin.product_variants.index') }}'>Biến thể sản phẩm</a>
                            </li>
                            <li>
                                <a class='tp-link' href='{{ route('admin.products.productDiscontinued') }}'>Sản phẩm ngừng
                                    kinh doanh</a>
                            </li>
                            <li>
                                <a class='tp-link' href='{{ route('admin.product_variants.variantDiscontinued') }}'>Biến thể
                                    đã ngừng kinh doanh</a>
                            </li>
                            <li>
                                <a class='tp-link' href='{{ route('admin.sizes.index') }}'>Size</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.categories.index') }}'>
                        <i data-feather="clipboard"></i>
                        <span> Quản lý danh mục </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.users.index') }}'>
                        <i data-feather="users"></i>
                        <span> Quản lý người dùng </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.orders.index') }}'>
                        <i data-feather="package"></i>
                        <span> Quản lý đơn hàng </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.banners.index') }}'>
                        <i data-feather="image"></i>
                        <span> Quản lý banner </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.statistics.index') }}'>
                        <i data-feather="bar-chart-2"></i>
                        <span> Quản lý thống kê </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.comments.index') }}'>
                        <i data-feather="message-square"></i>
                        <span> Quản lý bình luận </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.news.index') }}'>
                        <i data-feather="align-justify"></i>
                        <span> Quản lý tin tức </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='{{ route('admin.settings.edit') }}'>
                        <i data-feather="settings"></i>
                        <span> Quản lý cài đặt </span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>

<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <div id="sidebar-menu">

            <div class="logo-box d-flex align-items-center justify-content-start">
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

            <ul class="nav flex-column" id="side-menu">
                <li class="nav-item mt-2">
                    <a class="nav-link tp-link" href='{{ route('admin.index') }}'>
                        <i data-feather="home" class="icon-sidebar"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <span class="nav-link menu-title">Chức năng</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebarProduct" role="button"
                        aria-expanded="false" aria-controls="sidebarProduct">
                        <i data-feather="package" class="icon-sidebar"></i>
                        <span> Quản lý sản phẩm </span>
                        <i class="ri-arrow-down-s-line menu-arrow"></i>
                    </a>
                    <div class="collapse" id="sidebarProduct">
                        <ul class="nav flex-column nav-second-level">
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.products.index') }}'>
                                    <i data-feather="list" class="icon-sidebar-nested"></i>
                                    Danh sách sản phẩm
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.products.productDiscontinued') }}'>
                                    <i data-feather="slash" class="icon-sidebar-nested"></i>
                                    Sản phẩm ngừng kinh doanh
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.sizes.index') }}'>
                                    <i data-feather="layout" class="icon-sidebar-nested"></i>
                                    Size
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebarOrder" role="button"
                        aria-expanded="false" aria-controls="sidebarOrder">
                        <i data-feather="shopping-cart" class="icon-sidebar"></i>
                        <span> Quản lý đơn hàng </span>
                        <i class="ri-arrow-down-s-line menu-arrow"></i>
                    </a>
                    <div class="collapse" id="sidebarOrder">
                        <ul class="nav flex-column nav-second-level">
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.orders.index') }}'>
                                    <i data-feather="shopping-bag" class="icon-sidebar-nested"></i>
                                    Danh sách đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.orders.pending_cancellation') }}'>
                                    <i data-feather="x-octagon" class="icon-sidebar-nested"></i>
                                    Đơn hàng chờ hủy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tp-link" href='{{ route('admin.orders.order_cancellation') }}'>
                                    <i data-feather="trash-2" class="icon-sidebar-nested"></i>
                                    Đơn hàng đã hủy
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.users.index') }}'>
                        <i data-feather="users" class="icon-sidebar"></i>
                        <span> Quản lý người dùng </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.categories.index') }}'>
                        <i data-feather="folder" class="icon-sidebar"></i>
                        <span> Quản lý danh mục </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.brands.index') }}'>
                        <i data-feather="award" class="icon-sidebar"></i>
                        <span> Quản lý thương hiệu </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.review.index') }}'>
                        <i data-feather="star" class="icon-sidebar"></i>
                        <span> Quản lý đánh giá </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.comments.index') }}'>
                        <i data-feather="message-square" class="icon-sidebar"></i>
                        <span> Quản lý bình luận </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.banners.index') }}'>
                        <i data-feather="image" class="icon-sidebar"></i>
                        <span> Quản lý banner </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.news.index') }}'>
                        <i data-feather="file-text" class="icon-sidebar"></i>
                        <span> Quản lý tin tức </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link tp-link" href='{{ route('admin.promotions.index') }}'>
                        <i data-feather="tag" class="icon-sidebar"></i>
                        <span> Quản lý mã giảm giá </span>
                    </a>
                </li>

                @if (Auth::user()->hasPermission('manage_statistics'))
                    <li class="nav-item">
                        <a class="nav-link tp-link" href='{{ route('admin.statistics.index') }}'>
                            <i data-feather="bar-chart-2" class="icon-sidebar"></i>
                            <span> Quản lý thống kê </span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasPermission('manage_settings'))
                    <li class="nav-item">
                        <a class="nav-link tp-link" href='{{ route('admin.settings.edit') }}'>
                            <i data-feather="settings" class="icon-sidebar"></i>
                            <span> Quản lý cài đặt </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="clearfix"></div>

    </div>
</div>

<style>
    .icon-sidebar {
        width: 24px;
        height: 24px;
        margin-right: 10px;
        vertical-align: middle;
    }

    .icon-sidebar-nested {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        vertical-align: middle;
    }

    .menu-arrow {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .menu-title {
        color: #adb5bd;
        font-size: 0.8rem;
        font-weight: bold;
        padding: 0.5rem 1.5rem 0.2rem;
        text-transform: uppercase;
    }
</style>

<script>
    feather.replace();
</script>

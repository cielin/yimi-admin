<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/layout.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/fonts.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/blue.css') }}">
    @yield('css')
</head>
<body>
<div id="page-wrapper" class="active">
	<!-- Siderbar -->
	<div id="sidebar-wrapper">
        <ul class="sidebar">
            <li class="sidebar-main">
                <a href="#">
                    {{ strtoupper(config('app.name')) }}
                </a>
            </li>
            <li class="sidebar-title"><span>MAIN</span></li>
            <li class="sidebar-list">
                <a href="{{ URL::route('banners.index') }}">首页运营 <span class="menu-icon icon-house"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('banners.index') }}">Banner列表</a></li>
                    <li><a href="{{ URL::route('banners.create') }}">添加Banner</a></li>
                    <li><a href="{{ URL::route('spotlights.index') }}">发现好物列表</a></li>
                    <li><a href="{{ URL::route('spotlights.create') }}">添加好物</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('products.index') }}">商品管理<span class="menu-icon icon-database"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('products.index') }}">商品列表</a></li>
                    <li><a href="{{ URL::route('products.create') }}">添加商品</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('brands.index') }}">品牌管理 <span class="menu-icon icon-docs"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('brands.index') }}">品牌列表</a></li>
                    <li><a href="{{ URL::route('brands.create') }}">添加品牌</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('productcategories.index') }}">商品类别管理 <span class="menu-icon icon-drawer4"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('productcategories.index') }}">商品类别列表</a></li>
                    <li><a href="{{ URL::route('productcategories.create') }}">添加商品类别</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('orders.index') }}">订单管理 <span class="menu-icon icon-newspaper2"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('orders.index') }}">订单列表</a></li>
                    <li><a href="{{ URL::route('orders.create') }}">添加新订单</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('articles.index') }}">文章管理 <span class="menu-icon icon-newspaper2"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('articles.index') }}">文章列表</a></li>
                    <li><a href="{{ URL::route('articles.create') }}">添加新文章</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="">客户管理 <span class="menu-icon icon-users3"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('customers.index') }}">客户列表</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="">设计师管理 <span class="menu-icon icon-user5"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('designers.index') }}">设计师列表</a></li>
                    <li><a href="{{ URL::route('designers.create') }}">添加设计师</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="">设计师作品管理 <span class="menu-icon icon-user5"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('portfolios.index') }}">设计师作品列表</a></li>
                    <li><a href="{{ URL::route('portfolios.create') }}">添加设计师作品</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="">评论<span class="menu-icon icon-chat"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('reviews.index') }}">评论列表</a></li>
                </ul>
            </li>
            <li class="sidebar-list">
                <a href="{{ URL::route('notifys.index') }}">消息管理 <span class="menu-icon icon-newspaper2"></span></a>
                <ul class="sub-menu">
                    <li><a href="{{ URL::route('notifys.index') }}">消息列表</a></li>
                    <li><a href="{{ URL::route('notifys.create') }}">添加新消息</a></li>
                </ul>
            </li>
        </ul>
        <div class="sidebar-footer container">
            <div class="row">
                <div class="col">
                    <a href="{{ env('SITE_BASE_URL') }}" target="_blank">
                    主页
                    </a>
                </div>
                <div class="col">
                    {{ Html::link('auth/logout', '退出') }}
                </div>
            </div>
        </div>
    </div>
    <!-- End Siderbar -->
    <div id="content-wrapper">
    	<div class="page-content container">
            @notification()
    		@yield('page-content')
    	</div>
    </div>
</div>

<!-- js -->
<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/layout.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
@yield('js')
</body>
</html>
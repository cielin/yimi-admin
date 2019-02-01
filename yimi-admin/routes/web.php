<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/', 'AuthController@index');

// 登录验证
Route::post('auth/login', array('as' => 'auth.login', 'uses' => 'AuthController@doLogin'));
Route::get('auth/logout', array('as' => 'auth.logout', 'uses' => 'AuthController@doLogout'));
Route::get('login', 'AuthController@index')->name('login');
Route::resource('auth', 'AuthController');

Route::get('api/customers/getdatafromterm', array('as' => 'admin.customers.getcustomerbysearch', 'uses' => 'CustomersController@getCustomerBySearch'));
Route::get('api/customers/getaddresses', array('as' => 'admin.customers.getcustomeraddresses', 'uses' => 'CustomersController@getCustomerAddresses'));
Route::get('api/products/getdatafromterm', array('as' => 'admin.products.getproductbysearch', 'uses' => 'ProductsController@getProductBySearch'));
Route::get('api/orders/getsiblingorders', array('as' => 'admin.orders.getsiblingorders', 'uses' => 'OrdersController@getSiblingOrders'));

// 订单添加商品
Route::post('order/add_product', array('as' => 'orders.addproduct', 'uses' => 'OrdersController@addProduct'));

// 产品页过滤
Route::get('products/unproved', array('as' => 'products.getunprovedproducts', 'uses' => 'ProductsController@getUnprovedProducts'));
Route::get('products/published', array('as' => 'products.getpublishedproducts', 'uses' => 'ProductsController@getPublishedProducts'));

Route::middleware(['auth'])->group(function()
{
	Route::resource('products', 'ProductsController');
	Route::resource('brands', 'BrandsController');
	Route::resource('productcategories', 'ProductCategoriesController');
	Route::resource('orders', 'OrdersController');
	Route::resource('notifys', 'NotifyController');
	Route::resource('customers', 'CustomersController');
	Route::resource('designers', 'DesignersController');
	Route::resource('articles', 'ArticlesController');
	Route::resource('banners', 'BannersController');
	Route::resource('spotlights', 'SpotlightsController');
	Route::resource('portfolios', 'PortfoliosController');
	Route::resource('reviews', 'CustomerReviewsController');
});
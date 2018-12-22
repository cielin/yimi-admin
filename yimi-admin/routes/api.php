<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('article/upload', array('as' => 'admin.articles.upload', 'uses' => 'ArticlesController@uploadFile'));
Route::post('product/upload', array('as' => 'admin.products.upload', 'uses' => 'ProductsController@uploadFile'));
Route::post('product/doreview', array('as' => 'admin.reviews.do', 'uses' => 'CustomerReviewsController@doReview'));

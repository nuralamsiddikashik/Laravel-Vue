<?php

use Illuminate\Support\Facades\Route;

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

Route::get( '/', function () {
    return view( 'welcome' );
} );

Auth::routes();

Route::group( ['middleware' => ['auth']], function () {
    Route::get( '/home', 'HomeController@index' )->name( 'home' );
    Route::get( '/categories', 'CategoryController@index' )->name( 'categories' );
    Route::post( '/categories', 'CategoryController@addCategory' )->name( 'categories.store' );
    Route::put( '/categories/{id}', 'CategoryController@updateCategory' )->name( 'categories.update' );
    Route::delete( '/categories/{id}', 'CategoryController@deleteCategory' )->name( 'categories.delete' );

    // Product Route

    Route::get( '/products', 'ProductController@index' )->name( 'product.index' );
    Route::post( '/products', 'ProductController@store' )->name( 'product.store' );
} );

Route::group( ['middleware' => ['auth'], 'prefix' => 'api'], function () {
    Route::get( '/categories', 'CategoryController@getCategoryList' )->name( 'api.category.list' );
    Route::get( '/products', 'ProductController@getProductList' )->name( 'api.product.list' );
} );

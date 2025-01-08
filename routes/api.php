<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\api\RestaurantController;
use  App\Http\Controllers\api\UserController;
use  App\Http\Controllers\api\OrderController;
use  App\Http\Controllers\api\RatingController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\MenuController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout']);

Route::post('/forget', [UserController::class, 'forget']);
Route::post('/reset', [UserController::class, 'reset']);

Route::prefix('/restaurants')->group(function () {
Route::get('/showall', [RestaurantController::class,'index']);
Route::get('/show/{uuid}', [RestaurantController::class,'show']);
Route::get('/search', [RestaurantController::class,'search']);
Route::post('/add',[RestaurantController::class,'store']);
Route::post('/edit/{uuid}',[RestaurantController::class,'update']);
Route::get('/delete/{uuid}',[RestaurantController::class,'destroy']);
Route::get('/restore/{uuid}',[RestaurantController::class,'restore']);
});
Route::prefix('/order')->group(function () {
    Route::get('/ShowallOrderbyUser', [OrderController::class,'ShowallOrderbyUser']);
    Route::post('/addOrder', [OrderController::class,'addOrder']);
    Route::post('/update/{uuid}', [OrderController::class,'update']);
    Route::get('/deliverOrder/{uuid}', [OrderController::class,'deliverOrder']);
    Route::get('/cancelOrder/{uuid}', [OrderController::class,'cancelOrder']);
});
Route::prefix('/categories')->group(function () {
    Route::get('/showall', [CategoryController::class,'index']);
    Route::get('/show/{uuid}', [CategoryController::class,'show']);
    Route::get('/search', [CategoryController::class,'search']);
    Route::post('/add',[CategoryController::class,'store']);
    Route::post('/edit/{uuid}',[CategoryController::class,'update']);
    Route::get('/delete/{uuid}',[CategoryController::class,'destroy']);
    Route::get('/restore/{uuid}',[CategoryController::class,'restore']);
    });
Route::prefix('/products')->group(function () {
    Route::get('/show/{uuid}', [ProductController::class,'show']);
    Route::post('/add',[ProductController::class,'store']);
    Route::post('/edit/{uuid}',[ProductController::class,'update']);
    Route::get('/delete/{uuid}',[ProductController::class,'destroy']);
    Route::get('/restore/{uuid}',[ProductController::class,'restore']);
    });    
Route::prefix('/menus')->group(function () {
    Route::post('/add',[MenuController::class,'store']);
    Route::post('/edit/{uuid}',[MenuController::class,'update']);
    });
Route::prefix('/rating')->group(function () {
    Route::post('/add', [RatingController::class,'store']);
});

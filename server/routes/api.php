<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::resource('categories', CategoryController::class);

    Route::post('posts/edit/{post}', [PostController::class, 'update']);
    Route::resource('posts', PostController::class);

    Route::resource('comments', CommentController::class);

    Route::post('/categories/export', [CategoryController::class, 'export']);
    Route::post('/categories/import', [CategoryController::class, 'import']);

    Route::post('/posts/export', [PostController::class, 'export']);
    Route::post('/posts/import', [PostController::class, 'import']);
});

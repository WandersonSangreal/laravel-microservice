<?php

use App\Http\Controllers\API\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CastMemberController;

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

Route::group([], function () {

    $exception = ['except' => ['create', 'edit']];

    Route::resource('categories', CategoryController::class, $exception);
    Route::resource('genres', GenreController::class, $exception);
    Route::resource('cast_members', CastMemberController::class, $exception);
    Route::resource('videos', VideoController::class, $exception);

});

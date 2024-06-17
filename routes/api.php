<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PostController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//register
Route::post("register",[ApiController::class,"register"]);

//login
Route::post("login",[ApiController::class,"login"]);

Route::group(["middleware"=>["auth:sanctum"]
],function(){
    //profile
    Route::get("profile",[ApiController::class,"profile"]);

    //logout
    Route::get("logout",[ApiController::class,"logout"]);

     // CRUD routes for Post
     Route::get("posts", [PostController::class, "index"]);
     Route::post("posts", [PostController::class, "store"]);
     Route::get("posts/{post}", [PostController::class, "show"]);
     Route::put("posts/{post}", [PostController::class, "update"]);
     Route::delete("posts/{post}", [PostController::class, "destroy"]);


});
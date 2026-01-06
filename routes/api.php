<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailsController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyDocsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum', 'can:isApplicant');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working'], 200);
});


Route::middleware('auth:sanctum')->post('/v1/logout', [UserController::class, 'logout']);


//only admin user can access
Route::middleware(['auth:sanctum', 'can:isAdmin'])->group(function () {
        Route::prefix('/v1/')->group(function (){
        Route::post('vacancy', [VacancyController::class, 'store']);
        Route::put('vacancy/{vacancy}', [VacancyController::class, 'update']);
        Route::delete('vacancy/{vacancy}', [VacancyController::class, 'destroy']);    

        Route::post('post', [PostController::class, 'store']);
        Route::put('post/{post}', [PostController::class, 'update']);
        Route::delete('post/{post}', [PostController::class, 'destroy']);    
        
        Route::post('vacancy_docs', [VacancyDocsController::class, 'store']);
        Route::put('vacancy_docs/{vacancy_docs}', [VacancyDocsController::class, 'update']);
        Route::delete('vacancy_docs/{vacancy_docs}', [VacancyDocsController::class, 'destroy']);    

        
        Route::get('users', [UserController::class, 'index']);
        Route::put('user/{user}', [UserController::class, 'update']);
        Route::delete('user/{user}', [UserController::class, 'destroy']);    
        Route::get('user/{user}', [UserController::class, 'show']);    

        //////////////////////
        Route::get('vacancies_data', [VacancyController::class, 'vacancies_data']);
        Route::post('vacancy_data/{vacancy_id}', [VacancyController::class, 'vacancy_data_update']);
        Route::delete('vacancy_data/{vacancy_id}', [VacancyController::class, 'vacancy_data_destroy']);
        /////////////////////////
        Route::post('update_user_payment_status', [UserController::class, 'updateUserPaymentStatus']);


        


    });
});

Route::prefix('/v1/')->group(function (){

    Route::get('vacancies', [VacancyController::class, 'index']);
    Route::get('vacancy/{vacancy}', [VacancyController::class, 'show']);
    

    
    //crud post individually
    Route::get('posts', [PostController::class, 'index']);
    Route::get('post/{post}', [PostController::class, 'show']);

    //crud vacancy docs individually
    Route::get('vacancies_docs', [VacancyDocsController::class, 'index']);
    Route::get('vacancy_docs/{vacancy_docs}', [VacancyDocsController::class, 'show']);

    // Route::get('user/{user}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::controller(UserController::class)->group(function(){
        Route::post('login', 'login');//url, function_name
        Route::post('admin_login', 'loginAdmin');
        Route::post('register', 'store');
        Route::post('forgot-password', 'forgotPasswordSendResetLinkEmail');
        Route::post('password-reset', 'passwordReset');
    }); 

    //only login users
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(UserDetailsController::class)->group(function(){
            Route::post('user_details', 'update');//url, function_name
            Route::get('user_details/{step}', 'show');//url, function_name
        });
        Route::controller(UserController::class)->group(function(){
            Route::get('user/{user}', 'show');
        });

    });

});

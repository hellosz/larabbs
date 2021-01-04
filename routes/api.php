<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function() {
    // 用户登录、注册相关接口
    Route::middleware('throttle:' . config('api.rate_limits.sign'))->group(function() {
        // 返回图片验证码
        Route::post('captchas', 'CaptchasController@store')->name('captchas.store');
        // 发送短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');

        // 注册用户
        Route::post('users', 'UsersController@store')->name('users.store');

        // authorization
        Route::post('authorizations', 'AuthorizationsController@store')->name('authorizations.store');
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->where('social_type', 'wechat')
            ->name('socials.authorizations.store');

        // manage token
        Route::put('authorizations/current', 'AuthorizationsController@update')->name('authorizations.update');
        Route::delete('authorizations/current', 'AuthorizationsController@destroy')->name('authorizations.destroy');
    });

    Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function() {
        // 游客可以访问的接口
        Route::get('users/{user}', 'UsersController@show')->name('users.show');

        // 当前登录用户信息
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'UsersController@me')->name('user.me');
            // 更新信息
            Route::patch('users/update', 'UsersController@update')->name('users.update');

            // 图片上传
            Route::post('images', 'ImagesController@store')->name('images.store');

        });
    });
});

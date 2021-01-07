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

        // 分类列表
        Route::get('categories', 'CategoriesController@index')->name('categories.index');

        // 话题列表/详情
        Route::resource('topics', 'TopicsController')->only(['index', 'show']);

        // user publish topic list
        Route::get('users/{user}/topics', 'TopicsController@userIndex')->name('users.topics.index');

        // 回复列表
        Route::get('topics/{topic}/replies', 'RepliesController@index')->name('topics.replies.index');

        // 某个用户的回复列表
        Route::get('users/{user}/replies', 'RepliesController@userIndex')->name('users.replies.index');

        // 当前登录用户信息
        Route::middleware('auth:api')->group(function() {
            Route::get('user', 'UsersController@me')->name('user.me');
            // 更新信息
            Route::patch('users/update', 'UsersController@update')->name('users.update');

            // 图片上传
            Route::post('images', 'ImagesController@store')->name('images.store');

            // 话题创建/更新和删除
            Route::resource('topics', 'TopicsController')->only(['update', 'store', 'destroy']);

            // reply topic
            Route::post('topics/{topic}/replies', 'RepliesController@store')->name('topics.replies.store');

            // delete reply
            Route::delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')->name('topics.replies.destroy');

            // 读取通知列表
            Route::get('notifications', 'NotificationsController@index')->name('notifications.index');

            // 通知数量
            Route::get('notifications/stat', 'NotificationsController@stat')->name('notifications.stat');
        });
    });
});

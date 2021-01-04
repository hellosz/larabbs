<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    //
    public function store(UserRequest $request)
    {
        // 验证验证码是否失效
        $verification = $request->verification_key;
        $storedVerification = Cache::get($verification);
        if (!$storedVerification) {
            abort(403, '短信验证码失效');
        }

        // 验证验证码
        if (!hash_equals((string)$storedVerification['code'], (string)$request->verification_code)) {
            // 401
            throw new AuthenticationException('短信验证码错误');
        }

        //  创建用户
        $user = User::create([
          'name' => $request->name,
          'password' => $request->password,
          'phone' => $storedVerification['phone']
        ]);

        // 忘记验证码
        Cache::forget($verification);

        // 返回结果
        return (new UserResource($user))->showSensitiveFields();
    }

    /**
     * 显示指定用户信息
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * 返回当前登录用户的信息
     *
     * @return UserResource
     */
    public function me()
    {
        return (new UserResource(auth()->user()))->showSensitiveFields();
    }
}

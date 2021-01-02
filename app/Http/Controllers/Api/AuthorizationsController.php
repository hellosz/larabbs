<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationsRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Overtrue\Socialite\AccessToken;

class AuthorizationsController extends Controller
{
    //
    public function store()
    {
        return response()->json(['msg' => 'success']);
    }

    public function socialStore($type, AuthorizationsRequest $request)
    {
        $driver = \Socialite::driver($type);

        try {
            // 获取access_token
            if ($request->code) {
                $accessToken = $driver->getAccesToken($request->code);
            } else {
                $accessTokenData['access_token'] = $request->access_token;

                if ($type === 'wechat') {
                    $accessTokenData['openid'] = $request->openid;
                }
                $accessToken = new AccessToken($accessTokenData);
            }

            // 获取用户信息
            $authUser = $driver->user($accessToken);
        } catch (\Exception $e) {
            new AuthorizationException('参数草屋，用户登录失败');
        }

        switch ($type) {
            case 'wechat':
                $unionId = $authUser->getOriginal()['unionid'] ?? null;
                if ($unionId) {
                    $user = User::where('wechat_unionid', $authUser->unionId)->first();
                } else {
                    $user = User::where('wechat_openid', $authUser->id)->first();
                }

                // 新用户创建
                if (!$user) {
                    $user = User::create([
                        'name' => $authUser->getNickName(),
                        'avatar' => $authUser->getAvatar(),
                        'wechat_unionid' => $unionId,
                        'wechat_openid' => $authUser->getId(),
                    ]);
                }
                
                break;
        }



        // 返回结果
        return response()->json(['token' => $user->id]);
    }
}

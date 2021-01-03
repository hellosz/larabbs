<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationsRequest;
use App\Http\Requests\Api\SocialAuthorizationsRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Overtrue\Socialite\AccessToken;

class AuthorizationsController extends Controller
{
    /**
     * api login
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AuthorizationsRequest $request)
    {
        // 设置登录账号和密码
        filter_var($request->username, FILTER_VALIDATE_EMAIL) ?
            $cridential['email'] = $request->username :
            $cridential['phone'] = $request->username;

        $cridential['password'] = $request->password;

        //  check cridential
        if (!$token = Auth::guard('api')->attempt($cridential)) {
            throw new AuthenticationException('账号或者密码错误！');
        }

        return $this->responseWithToken($token)->setStatusCode(201);
    }

    public function socialStore($type, SocialAuthorizationsRequest $request)
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

        // login, get token
        $token = auth('api')->login($user);

        // 返回结果
        return $this->responseWithToken($token);
    }

    /**
     * update token
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function update()
    {
        $token = auth('api')->refresh();
        return $this->responseWithToken($token)->setStatusCode(201);

    }

    /**
     * destory token
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy()
    {
        auth('api')->logout();
        return response(null, 204);

    }
    /**
     * 返回结果
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseWithToken($token)
    {
        // return response
        $response  = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];

        return response()->json($response);
    }
}

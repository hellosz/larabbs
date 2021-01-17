<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationsRequest;
use App\Models\User;
use App\Traits\PassportToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Overtrue\Socialite\AccessToken;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response as Psr7Response;


class AuthorizationsController extends Controller
{
    use PassportToken;

    /**
     * 登录授权
     *
     * @param AuthorizationsRequest $request
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return \Psr\Http\Message\ResponseInterface
     */
//    public function store(AuthorizationsRequest $request, AuthorizationServer $server, ServerRequestInterface $serverRequest)
//    {
//        try {
//            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response())->withStatus(201);
//        } catch (OAuthServerException $e) {
//            new AuthorizationException($e->getMessage());
//        }
//    }

    /**
     * 整合第三方登录
     *
     * @param AuthorizationsRequest $originRequest
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return \Psr\Http\Message\ResponseInterface
     * @throws AuthorizationException
     */
    public function store(AuthorizationsRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch(OAuthServerException $e) {
            throw new AuthorizationException($e->getMessage());
        }
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
//        $token = auth('api')->login($user);
        $token = $this->createPassportTokenByUser($user, '1', false);

        // 返回结果
        return $this->responseWithToken($token);
    }

    /**
     * update token
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
//    public function update()
//    {
//        $token = auth('api')->refresh();
//        return $this->responseWithToken($token)->setStatusCode(201);
//
//    }

    /**
     * 整合第三方登录
     *
     * @param AuthorizationServer $server
     * @param ServerRequestInterface $serverRequest
     * @return \Psr\Http\Message\ResponseInterface
     * @throws AuthorizationException
     */
    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response);
        } catch(OAuthServerException $e) {
            throw new AuthorizationException($e->getMessage());
        }
    }

    /**
     * destory token
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
//    public function destroy()
//    {
//        auth('api')->logout();
//        return response(null, 204);
//
//    }


    /**
     * destroy token
     *
     * @throws AuthorizationException
     */
    public function destroy()
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
        } else {
            throw new AuthorizationException('Access token is invalid');
        }
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

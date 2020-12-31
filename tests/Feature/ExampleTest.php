<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Overtrue\LaravelSocialite\Socialite;
use Overtrue\Socialite\AccessToken;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testAccessToken()
    {
        // 创建token
        $accessToken = new AccessToken([
            'access_token' => '40_ELlLm6ckE1qoELaFErcchJswsKCOKLPYlAGSWqbjp98RFQn2uHOJAxI6OvsUEsytIjjTEQEcf-3uYHJQU_f4HQ',
            'openid' => 'ousZA6CHP5h4IEfFFAMIujyL52_U',
        ]);

        // 获取用户信息
        $driver = Socialite::driver('wechat');
        $user = $driver->user($accessToken);
        dd($user);
    }

    public function testCode()
    {
        // 根据code获取access_token
        $code = 'CODE';
        $driver = Socialite::driver('wechat');
        $accessToken = $driver->getAccessToken($code);
        $user = $driver->user($accessToken);
        dd($user);
    }
}

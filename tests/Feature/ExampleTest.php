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
            'access_token' => '40_nO9hh5B0nBvp6HJePpXSC3O1ZZN3S7dBR8l9rx8peeqDLj0tDS1OIC2WmINVc9vWgY_jjSvjpK4_wdu-4qK2jA',
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

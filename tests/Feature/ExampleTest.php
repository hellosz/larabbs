<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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

    public function testAuthLogin()
    {
        $arr = [];
        $this->assertNotEmpty($arr);
    }

    public function testStoreTopic()
    {


    }
}

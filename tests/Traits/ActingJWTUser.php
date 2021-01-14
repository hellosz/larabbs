<?php

namespace Tests\Traits;

use App\Models\User;

trait ActingJWTUser
{
    /**
     * 使用指定账号请求接
     *
     * @param User $user
     * @return $this
     */
    public function JWTActingAs(User $user)
    {
        $token = auth('api')->fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }
}

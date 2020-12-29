<?php

/**
 * 图片验证码
 *
 * Class CaptchasController
 * @package App\Http\Controllers\Api
 */
namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchasRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{
    /**
     * 生成图片验证码
     *
     * @param CaptchasRequest $request
     * @param CaptchaBuilder $captchaBuilder
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(CaptchasRequest $request, CaptchaBuilder $captchaBuilder)
    {
        // 生成图片验证码
        $phone = $request->phone;
        $captchaKey = 'captcha_' . Str::random(15);

        // 保存验证码
        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(2);
        Cache::put($captchaKey, ['phone' => $phone, 'code' => $captcha->getPhrase()]);

        // 返回结果
        $response = [
            'captcha_key' => $captchaKey,
            'captcha_image_content' => $captcha->inline(),
            'expired_at' => $expiredAt->toDateTimeString()
        ];

        return response()->json($response)->setStatusCode(201);
    }


}

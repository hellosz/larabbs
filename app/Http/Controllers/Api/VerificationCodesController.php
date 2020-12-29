<?php

/**
 * 发送验证码
 *
 * Class VerificationCodesController
 * @package App\Http\Controllers\Api
 */
namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        // 验证图片验证码
        $captchaKey = $request->captcha_key;
        $storedCaptcha = Cache::get($captchaKey);
        if (!$storedCaptcha) {
            abort(403, '图片验证码失效');
        }

        // 验证图片验证码是否一致
        if ($storedCaptcha['code'] != $request->captcha_code) {
           Cache::forget($captchaKey); // 清除旧的验证码
           throw new AuthenticationException('图片验证码错误');
        }

        // 发送验证码
        $phone = $storedCaptcha['phone']; // 电话号码

        if (ENV('APP_DEBUG') == true) {
            // 测试华景验证码为1235
            $code = 1235;
        } else {
            try {
                // 生成随机的验证码
                $code = Str::padLeft(random_int(1, 9999), 4, 0);
                $resutl = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (InvalidArgumentException $e) {
                abort(500, $e->getMessage());
            } catch (NoGatewayAvailableException $e) {
                $message = $e->getException('aliyun')->getMessage() ?? '短信发送异常';
                abort(500, $message);
            }
        }

        // 保存验证码信息
        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinutes(5); // 5分钟过期
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        // 返回结果
        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);
    }
}

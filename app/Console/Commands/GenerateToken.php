<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larabbs:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate token for assigned user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 获取用户
        $userId = $this->ask('请输入用户id?');

        // 判断用户是否存在
        $user = User::find($userId);
        if (!$userId) {
            $this->error("{$userId} user does not exists");
        }

        // 登录，放回家过
        $ttl = 365 * 24 * 60;
        $token = auth('api')->setTTL($ttl)->login($user);
        return $this->info("TOKEN: {$token}");
    }
}

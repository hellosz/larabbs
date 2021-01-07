<?php
/**
 * @desc   PhpStorm
 * @author Chris
 * @time   2021/1/6 上午10:45
 */


namespace App\Http\Queries;


use App\Models\Reply;
use Spatie\QueryBuilder\QueryBuilder;

class ReplyQuery extends QueryBuilder
{
    public function __construct()
    {
        // 初始化
        parent::__construct(Reply::query());

        // 过滤条件
        $this->allowedIncludes('user', 'topic', 'topic.user');
    }
}

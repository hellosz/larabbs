<?php
/**
 * @desc   PhpStorm
 * @author Chris
 * @time   2021/1/6 上午10:45
 */


namespace App\Http\Queries;


use App\Models\Topic;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicQuery extends QueryBuilder
{
    public function __construct()
    {
        // 初始化
        parent::__construct(Topic::query());

        // 过滤条件
        $this->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ]);
    }
}

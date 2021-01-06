<?php

namespace App\Models\Traits;

use Spatie\QueryBuilder\QueryBuilder;

trait QueryBuilderBindable
{

    /**
     * 重写路由绑定
     *
     * @param mixed $value
     * @param null $field
     * @return \Illuminate\Database\Eloquent\Model|object|QueryBuilder|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // 确定query
        $queryClass = property_exists($this, 'queryClass') ?
            $this->queryClass : 'App\\Http\\Queries\\' . class_basename(self::class) . 'Query';

        // 判断是否存在
        if (!class_exists($queryClass)) {
            return parent::resolveRouteBinding($value, $field);
        }

        // 返回结果
        return (new $queryClass($this))
            ->where($this->getRouteKeyName(), $value)
            ->first();
    }
}

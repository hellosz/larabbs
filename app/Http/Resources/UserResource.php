<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * 显示敏感字段
     *
     * @var bool
     */
    protected $showSensitiveFields = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // 动态隐藏敏感字段
        if (!$this->showSensitiveFields) {
            $this->resource->makeHidden(['phone', 'email']);
        }

        // 新增自定义字段
        $data = parent::toArray($request);
        $data['bound_phone'] = $this->resource->phone ? true : false;
        $data['bound_wechat'] = ($this->resource->wechat_unionid || $this->resource->wechat_openid) ? true : false;

        // 用户角色
        $data['roles'] = RoleResource::collection($this->whenLoaded('roles'));

        return $data;
    }

    /**
     * 显示敏感字段
     *
     * @return $this
     */
    public function showSensitiveFields()
    {
        $this->showSensitiveFields = true;
        return $this;
    }
}

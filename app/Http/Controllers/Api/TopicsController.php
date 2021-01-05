<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    /**
     * 发布话题
     *
     * @param TopicRequest $request
     * @param Topic $topic
     * @return TopicResource
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        // 填充数据
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        // 返回结果
        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        // 鉴权
        $this->authorize('update', $topic);

        // 更新数据
        $topic->update($request->all());
        return new TopicResource($topic);
    }
}

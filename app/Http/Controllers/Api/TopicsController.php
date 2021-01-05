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

    /**
     * 更新主题
     *
     * @param TopicRequest $request
     * @param Topic $topic
     * @return TopicResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        // 鉴权
        $this->authorize('update', $topic);

        // 更新数据
        $topic->update($request->all());
        return new TopicResource($topic);
    }

    /**
     * 删除话题
     *
     * @param Topic $topic
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        // 鉴权
        $this->authorize('destroy', $topic);

        // 删除数据
        $topic->delete();
        return response(null, 204);
    }


}

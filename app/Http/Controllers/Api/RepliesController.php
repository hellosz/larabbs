<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\ReplyQuery;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * 帖子回复列表
     *
     * @param Request $request
     * @param Topic $topic
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(ReplyQuery $replyQuery, Topic $topic)
    {
        $replies = $replyQuery->where('topic_id', $topic->id)->paginate();

        return ReplyResource::collection($replies);
    }

    /**
     * 用户的回复列表
     *
     * @param ReplyQuery $replyQuery
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userIndex(ReplyQuery $replyQuery, User $user)
    {
        $replies = $replyQuery->where('user_id', $user->id)->paginate();

        return ReplyResource::collection($replies);
    }
    //
    /**
     * 回复主题
     *
     * @param ReplyRequest $request
     * @param Topic $topic
     * @param Reply $reply
     * @return ReplyResource
     */
    public function store(ReplyRequest  $request, Topic $topic, Reply $reply)
    {
        $reply->content = request()->get('content');
        $reply->user()->associate($request->user());
        $reply->topic()->associate($topic);
        $reply->save();

        return new ReplyResource($reply);
    }

    /**
     * delete reply
     *
     * @param Topic $topic
     * @param Reply $reply
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($topic->id != $reply->topic_id) {
           abort(404);
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return response(null, 204);
    }
}

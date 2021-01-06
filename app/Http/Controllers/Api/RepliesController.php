<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
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
        $reply->content = $request->content;
        $reply->user()->associate($request->user());
        $reply->topic()->associate($topic);
        $reply->save();

        return new ReplyResource($reply);
    }
}

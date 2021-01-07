<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * 通知列表
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate();

        return NotificationResource::collection($notifications);
    }

    /**
     * 未读通知数
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stat(Request $request)
    {
        $unreadCount = (int)$request->user()->notification_count;
        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * 设置消息已读
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request)
    {
        $request->user()->markAsRead();

        return response()->json(['message' => '消息已读']);
    }
}

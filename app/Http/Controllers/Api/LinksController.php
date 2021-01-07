<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    //
    /**
     * 推荐列表
     *
     * @param Request $request
     * @param Link $link
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Link $link)
    {
        LinkResource::wrap('data');
        return LinkResource::collection($link->getAllCached());
    }
}

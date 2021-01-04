<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImagesController extends Controller
{
    /**
     * 图片上传
     *
     * @param ImageRequest $request
     * @param ImageUploadHandler $uploadHandler
     * @param Image $image
     * @return ImageResource
     */
    public function store(ImageRequest $request, ImageUploadHandler $uploadHandler, Image $image)
    {
        $type = $request->type;
        $size = $type == 'avatar' ? 416 : 1024;
        $user = $request->user();

        // 图片上传
        $uplaoded = $uploadHandler->save($request->image, Str::plural($type), $user->id, $size);

        // 保存数据
        $image->user_id = $user->id;
        $image->type = $request->type;
        $image->path = $uplaoded['path'];
        $image->save();

        return new ImageResource($image);
    }
}

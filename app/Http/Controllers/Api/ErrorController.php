<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    //
    public function index()
    {
        return $this->errorResponse(404, '报错提示', 1001);
    }

    public function locale()
    {
        return $this->errorResponse(404, trans('auth.failed'), 1001);
    }
}

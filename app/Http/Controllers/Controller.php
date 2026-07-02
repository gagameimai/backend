<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * api log
     *
     * @param string $title,
     * @param \Throwable $th
     * @return void
     */
    public function apiLog($title, $th)
    {
        \Log::channel('api')->error($title, [
            'message' => $th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile()
        ]);
    }
}

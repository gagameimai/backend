<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerModel;

class BannerController extends Controller
{
    /**
     * Banner
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => BannerModel::selectRaw('name, img, img_mobile, url')
                    ->where('status', 1)
                    ->orderBy('sort', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('BannerController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

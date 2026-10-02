<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingModel;

class WebsiteController extends Controller
{
    /**
     * website info
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            $result = SettingModel::where('type', 'website')->first();

            return response()->json([
                'result' => \App\Http\Controllers\Admin\Setting\WebsiteInfoController::decodeContent($result->content)
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('WebsiteController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

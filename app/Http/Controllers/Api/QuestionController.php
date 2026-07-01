<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingModel;

class QuestionController extends Controller
{
    /**
     * 常見問題
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => [
                    'content' => SettingModel::where('type', 'qa')->value('content')
                ]
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('QuestionController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

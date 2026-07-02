<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarMediaModel;

class MultiMediaController extends Controller
{
    /**
     * 多媒體機
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => CarMediaModel::selectRaw('id, name, img, memo, size, hard_drive, price, ram, resolution')
                    ->where('status', 1)
                    ->orderByDesc('is_top')
                    ->orderBy('name', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('MultiMediaController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 多媒體機 - 詳情
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail($id)
    {
        try {
            $result = CarMediaModel::selectRaw('name, img, memo_in, content')->find($id);
            if (empty($result)) {
                return response()->json([
                    'message' => '查無資料'
                ], 404);
            } else {
                return response()->json([
                    'result' => $result
                ]);
            }
        } catch (\Throwable $th) {
            $this->apiLog('MultiMediaController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

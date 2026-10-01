<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarFittingModel;

class FittingController extends Controller
{
    /**
     * 車用配件
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => CarFittingModel::selectRaw('id, name, material, power, img')
                    ->where('status', 1)
                    ->orderByDesc('is_top')   /* 2026-09-30：置頂在前（原本 ASC 是反的） */
                    ->orderBy('sort', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('FittingController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 車用配件 - 詳情
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail($id)
    {
        try {
            $result = CarFittingModel::selectRaw('name, img, memo_in, content')->where('status', 1)->find($id)   /* 2026-09-30：停用的商品詳情頁不再打得開 */;
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
            $this->apiLog('FittingController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

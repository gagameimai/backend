<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarHeadrestModel;

class CarHeadrestController extends Controller
{
    /**
     * 頭枕螢幕 - 列表（沒帶 id，給 headrest 用）
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $result = CarHeadrestModel::selectRaw('id, name, img')
                ->where('status', 1)
                ->orderBy('is_top', 'ASC')
                ->orderBy('sort', 'ASC')
                ->get();

            return response()->json([
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarHeadrestController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 頭枕螢幕 - 詳情（有帶 id，給 headrestDetail 用）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail(Request $request, $id)
    {
        try {
            $result = CarHeadrestModel::selectRaw('name, img, memo_in, content')->find($id);
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
            $this->apiLog('CarHeadrestController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

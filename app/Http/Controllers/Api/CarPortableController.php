<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarPortableModel;

class CarPortableController extends Controller
{
    /**
     * 可攜式 - 列表（沒帶 id，給 portable 用）
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $result = CarPortableModel::selectRaw('id, name, img, price')
                ->where('status', 1)
                ->orderBy('is_top', 'ASC')
                ->orderBy('sort', 'ASC')
                ->get();

            return response()->json([
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarPortableController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 可攜式 - 詳情（有帶 id，給 portableDetail 用）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail(Request $request, $id)
    {
        try {
            $result = CarPortableModel::selectRaw('name, img, memo_in, content, price')->find($id);
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
            $this->apiLog('CarPortableController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

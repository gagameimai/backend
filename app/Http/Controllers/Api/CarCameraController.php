<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarCameraModel;

class CarCameraController extends Controller
{
    /**
     * 鏡頭 - 列表（沒帶 id，給 CameraList.vue 用）
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = CarCameraModel::selectRaw('id, name, img, price')
                ->where('status', 1);

            // 依前端傳來的 brand 過濾（0=MM、1=Clarion）；沒帶則回全部
            if ($request->filled('brand')) {
                $query->where('brand', $request->input('brand'));
            }

            return response()->json([
                'result' => $query->orderBy('is_top', 'ASC')
                    ->orderBy('sort', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarCameraController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 鏡頭 - 詳情（有帶 id，給 CameraDetail.vue 用）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail(Request $request, $id)
    {
        try {
            $query = CarCameraModel::selectRaw('name, img, memo_in, content, price');

            // 有帶 brand 就一併比對，確保 mm 網址不會取到 clarion 的資料
            if ($request->filled('brand')) {
                $query->where('brand', $request->input('brand'));
            }

            $result = $query->find($id);
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
            $this->apiLog('CarCameraController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

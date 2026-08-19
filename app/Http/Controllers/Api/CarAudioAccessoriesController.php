<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarAudioAccessoriesModel;

class CarAudioAccessoriesController extends Controller
{
    /**
     * 汽車音響 - 列表（沒帶 id，給 audioAccessories 用）
     * 回傳含 type（0=一般喇叭 1=高音喇叭 2=重低音 3=擴大機），前台依 type 分區顯示
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = CarAudioAccessoriesModel::selectRaw('id, name, img, type')
                ->where('status', 1);

            // 前端若有帶 type 就過濾，沒帶則回全部（由前台分區）
            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            return response()->json([
                'result' => $query->orderBy('is_top', 'ASC')
                    ->orderBy('sort', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarAudioAccessoriesController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 汽車音響 - 詳情（有帶 id，給 audioAccessoriesDetail 用）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail(Request $request, $id)
    {
        try {
            $result = CarAudioAccessoriesModel::selectRaw('name, img, memo_in, content')->find($id);
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
            $this->apiLog('CarAudioAccessoriesController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

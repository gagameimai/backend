<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarHeadUnitModel;

class CarHeadUnitController extends Controller
{
    /**
     * 車用主機 1/2DIN - 列表（沒帶 id，給 headUnit 用）
     * 回傳含 type（0=1DIN 1=2DIN），前台依 type 分區顯示
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = CarHeadUnitModel::selectRaw('id, name, img, type, size, hard_drive, ram, resolution, price')
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
            $this->apiLog('CarHeadUnitController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 車用主機 1/2DIN - 詳情（有帶 id，給 headUnitDetail 用）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail(Request $request, $id)
    {
        try {
            $result = CarHeadUnitModel::selectRaw('name, img, memo_in, content, size, hard_drive, ram, resolution, price')->find($id);
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
            $this->apiLog('CarHeadUnitController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

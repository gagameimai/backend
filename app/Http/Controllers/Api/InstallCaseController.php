<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstallCaseModel;
use Illuminate\Http\Request;
use DB;

class InstallCaseController extends Controller
{
    /**
     * 導入事例列表
     *
     * GET /api/install_cases          全部（狀態啟用）
     * GET /api/install_cases?home=1   只取首頁要顯示的，依 home_sort 由小到大，最多 3 筆
     *
     * 排序：置頂在前，其次依安裝日期由新到舊（沒填安裝日期則用建立時間遞補）。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = InstallCaseModel::selectRaw(
                'id, category, name, img, installed_at, is_pinned, car_brand_id, car_id, product, dealer'
            )->with(['brand', 'car'])->where('status', 1);

            if ($request->input('home') == 1) {
                $result = $query->where('is_home', 1)
                    ->orderBy('home_sort')
                    ->orderByDesc(DB::raw('COALESCE(installed_at, created_at)'))
                    ->limit(3)
                    ->get();
            } else {
                $result = $query->orderByDesc('is_pinned')
                    ->orderByDesc(DB::raw('COALESCE(installed_at, created_at)'))
                    ->orderByDesc('sort')
                    ->get();
            }

            return response()->json([
                'result' => self::withCarModel($result)
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('InstallCaseController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 把品牌與車款組成前台顯示用的車型字串（例：Toyota RAV4）。
     * 前台只讀 car_model 這個欄位，目前資料結構用關聯
     * 一樣在這裡組好再回傳，前台不需要跟著改。
     *
     * @param  \App\Models\InstallCaseModel  $item
     * @return string
     */
    protected static function carModel($item): string
    {
        return trim(($item->brand->name ?? '') . ' ' . ($item->car->name ?? ''));
    }

    /**
     * 批次補上 car_model。
     *
     * @param  \Illuminate\Support\Collection  $rows
     * @return \Illuminate\Support\Collection
     */
    protected static function withCarModel($rows)
    {
        return $rows->each(function ($item) {
            $item->car_model = self::carModel($item);
        });
    }

    /**
     * 導入事例單筆（獨立內頁用）
     *
     * GET /api/install_cases/{id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail($id)
    {
        try {
            $result = InstallCaseModel::selectRaw(
                'id, category, name, img, installed_at, is_pinned, car_brand_id, car_id, product, dealer, need, work'
            )->with(['brand', 'car'])->where('status', 1)->find($id);

            if (empty($result)) {
                return response()->json([
                    'message' => '查無資料'
                ], 404);
            }

            $result->car_model = self::carModel($result);

            return response()->json([
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('InstallCaseController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarBlindSpotModel;
use App\Models\CarBlindSpotFormatModel;
use App\Models\CarBrandModel;

class BlindSpotController extends Controller
{
    /**
     * 盲點偵測
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => CarBlindSpotModel::selectRaw('id, name, img')
                    ->where('status', 1)
                    ->orderBy('is_top', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('BlindSpotController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 盲點偵測 - 詳情
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail($id)
    {
        try {
            $result = CarBlindSpotModel::selectRaw('name, img, memo_in, content')->find($id);
            if (empty($result)) {
                return response()->json([
                    'message' => '查無資料'
                ], 404);
            } else {
                $result->depend = CarBlindSpotFormatModel::selectRaw('car_brand_id, style, year, spc')
                    ->where([
                        'car_blind_spot_id' => $id,
                        'status' => 1
                    ])
                    ->orderBy('sort', 'ASC')
                    ->get();

                $result->car_brand = CarBrandModel::selectRaw('id, name')
                    ->whereIn('id', data_get($result->depend, '*.car_brand_id'))
                    ->where('status', 1)
                    ->orderBy('sort', 'ASC')
                    ->get();

                return response()->json([
                    'result' => $result
                ]);
            }
        } catch (\Throwable $th) {
            $this->apiLog('BlindSpotController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

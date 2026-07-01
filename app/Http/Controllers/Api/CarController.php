<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarBrandModel;
use App\Models\CarModel;

class CarController extends Controller
{
    /**
     * 汽車品牌 + 種類
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'car_brand' => CarBrandModel::selectRaw('id, name')
                    ->where('status', 1)
                    ->orderBy('sort', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->get(),
                'car' => CarModel::selectRaw('id, car_brand_id, name, year_start, year_end')
                    ->where('status', 1)
                    ->orderBy('sort', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

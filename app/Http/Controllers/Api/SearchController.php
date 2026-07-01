<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarFittingModel;
use App\Models\CarMediaModel;
use App\Models\CarBlindSpotModel;
use App\Models\CarFrameModel;

class SearchController extends Controller
{
    /**
     * search
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = CarFrameModel::with(['brand', 'car'])->selectRaw('id, car_brand_id, car_id, name, year_start, year_end, size, img')
                ->where('status', 1)
                ->orderBy('name', 'ASC');

            if ($request->filled('car_brand_id')) {
                $query = $query->where('car_brand_id', $request->get('car_brand_id'));
            }

            if ($request->filled('car_id')) {
                $query = $query->where('car_id', $request->get('car_id'));
            }

            if ($request->filled('year')) {
                $query = $query->where('year_start', '<=', $request->get('year'))
                    ->where('year_end', '>=', $request->get('year'));
            }

            $result = $query->get();
            foreach ($result as $r) {
                $r->brand_name = '';
                if (!empty($r->brand)) {
                    $r->brand_name = $r->brand->name;
                }

                $r->car_name = '';
                if (!empty($r->car)) {
                    $r->car_name = $r->car->name;
                }

                unset($r->car_brand_id, $r->car_id, $r->car, $r->brand);
            }

            return response()->json([
                'result' => [
                    'car_frame' => $result,
                    'car_media' => CarMediaModel::selectRaw('id, name, img')
                        ->where('status', 1)
                        ->orderBy('is_top', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get(),
                    'car_blind_spot' => CarBlindSpotModel::selectRaw('id, name, img')
                        ->where('status', 1)
                        ->orderBy('is_top', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get(),
                    'car_fitting' => CarFittingModel::selectRaw('id, name, img')
                        ->where('status', 1)
                        ->orderBy('is_top', 'ASC')
                        ->orderBy('sort', 'ASC')
                        ->get(),
                ]
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('SearchController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

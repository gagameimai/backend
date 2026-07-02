<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarFrameModel;

class CarFrameController extends Controller
{
    /**
     * 安卓車框
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $query = CarFrameModel::with(['brand', 'car'])
                ->selectRaw('id, car_brand_id, car_id, name, year_start, year_end, size, img, img1, img2, img3')
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

                $r->img = json_decode($r->img, true)[0] ?? '';
                $r->img1 = json_decode($r->img1, true)[0] ?? '';
                $r->img2 = json_decode($r->img2, true)[0] ?? '';
                $r->img3 = json_decode($r->img3, true)[0] ?? '';
            }

            return response()->json([
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('CarFrameController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }

    /**
     * 安卓車框 - 詳情
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function detail($id)
    {
        try {
            $result = CarFrameModel::with(['brand', 'car'])
                ->selectRaw('car_brand_id, car_id, name, img, img1, img2, img3, content, year_start, year_end, size')
                ->find($id);
            if (empty($result)) {
                return response()->json([
                    'message' => '查無資料'
                ], 404);
            } else {
                $result->brand_name = '';
                if (!empty($result->brand)) {
                    $result->brand_name = $result->brand->name;
                }

                $result->car_name = '';
                if (!empty($result->car)) {
                    $result->car_name = $result->car->name;
                }

                unset($result->car_brand_id, $result->car_id, $result->car, $result->brand);

                $result->img = json_decode($result->img, true)[0] ?? '';
                $result->img1 = json_decode($result->img1, true);
                $result->img2 = json_decode($result->img2, true);
                $result->img3 = json_decode($result->img3, true)[0] ?? '';

                return response()->json([
                    'result' => $result
                ]);
            }
        } catch (\Throwable $th) {
            $this->apiLog('CarFrameController->detail()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

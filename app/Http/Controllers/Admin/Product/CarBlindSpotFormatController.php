<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\CarBlindSpotFormatResquest;
use App\Models\CarBlindSpotFormatModel;
use App\Models\CarBlindSpotModel;
use App\Models\CarBrandModel;
use DB;

class CarBlindSpotFormatController extends Controller
{
    /**
     * 畫面
     *
     * @param int $id
     * @param string $format
     */
    public function checkDependId($id, $format = '')
    {
        $check = CarBlindSpotModel::find($id);
        return !empty($check);
    }

    /**
     * 畫面
     *
     * @param int $dependId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index($dependId = 0)
    {
        if (!$this->checkDependId($dependId)) {
            return redirect()->route('admin.car_blind_spot')->with('error_message', '查無資料');
        } else {
            return view('admin.product.car_blind_spot_format', [
                'depend' => CarBlindSpotModel::find($dependId),
                'id' => $dependId
            ]);
        }
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @param int $dependId
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request, $dependId = 0)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            $query = CarBlindSpotFormatModel::with('brand')
                ->where('car_blind_spot_id', $dependId)
                ->orderByDesc('status')
                ->orderBy('sort', 'ASC')
                ->orderByDesc('created_at');

            if ($request->filled('car_brand_id')) {
                $query = $query->where('car_brand_id', $request->input('car_brand_id'));
                $isSearch = true;
            }

            return response()->json([
                'items' => $query->paginate(50),
                'brands' => CarBrandModel::orderByDesc('status')
                    ->orderBy('sort', 'ASC')
                    ->orderByDesc('created_at')
                    ->get(),
                'is_search' => $isSearch ?? false
            ]);
        }
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\Product\CarBlindSpotFormatResquest $request
     * @param int $dependId
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(CarBlindSpotFormatResquest $request, $dependId = 0)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            CarBlindSpotFormatModel::create([
                'car_blind_spot_id' => $dependId,
                'car_brand_id' => $request->input('car_brand_id'),
                'style' => $request->input('style'),
                'year' => $request->input('year'),
                'spc' => $request->input('spc'),
                'status' => $request->input('status'),
            ]);

            return response()->json([
                'message' => '新增成功'
            ]);
        }
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\Product\CarBlindSpotFormatResquest $request
     * @param int $dependId
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(CarBlindSpotFormatResquest $request, $dependId, $id)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            $item = CarBlindSpotFormatModel::where('car_blind_spot_id', $dependId)->find($id);
            if (empty($item)) {
                return response([
                    'message' => '查無資料'
                ], 400);
            } else {
                $item->car_brand_id = $request->input('car_brand_id');
                $item->style = $request->input('style');
                $item->year = $request->input('year');
                $item->spc = $request->input('spc');
                $item->status = $request->input('status');
                $item->save();

                return response()->json([
                    'message' => '更新成功'
                ]);
            }
        }
    }

    /**
     * 刪除
     *
     * @param int $dependId
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete($dependId, $id)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            $item = CarBlindSpotFormatModel::where('car_blind_spot_id', $dependId)->find($id);
            if (empty($item)) {
                return response([
                    'message' => '查無資料'
                ], 400);
            } else {
                $item->delete();

                return response()->json([
                    'message' => '刪除成功'
                ]);
            }
        }
    }

    /**
     * 取得單一
     *
     * @param int $dependId
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function find($dependId, $id)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            $item = CarBlindSpotFormatModel::where('car_blind_spot_id', $dependId)->find($id);
            if (empty($item)) {
                return response()->json([
                    'message' => '查無資料'
                ], 400);
            } else {
                return response()->json([
                    'item' => $item
                ]);
            }
        }
    }

    /**
     * 排序
     *
     * @param \Illuminate\Http\Request $request
     * @param int $dependId
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory Response
     */
    public function sort(Request $request, $dependId)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            if (!$request->has(['items'])) {
                return response([
                    'message' => '排序更新失敗。'
                ], 400);
            } else {
                DB::update(update_when_case_string('car_blind_spot_format', 'sort', $request->items));

                return response([
                    'message' => '排序更新成功。'
                ]);
            }
        }
    }

    /**
     * 狀態
     *
     * @param in $dependId
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function status($dependId, $id)
    {
        if (!$this->checkDependId($dependId)) {
            return response()->json([
                'message' => '系統異常,請正常操作'
            ], 500);
        } else {
            $item = CarBlindSpotFormatModel::where('car_blind_spot_id', $dependId)->find($id);
            if (empty($item)) {
                return response()->json([
                    'message' => '查無資料'
                ], 400);
            } else {
                $item->status = $item->status == 1 ? 0 : 1;
                $item->save();

                return response()->json([
                    'message' => '狀態更新成功'
                ]);
            }
        }
    }
}

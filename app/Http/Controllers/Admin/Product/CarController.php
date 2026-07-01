<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\CarResquest;
use App\Models\CarBrandModel;
use App\Models\CarModel;
use DB;

class CarController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.product.car');
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        // $query = CarModel::with('brand')
        //     ->orderByDesc('status')
        //     ->orderBy('sort', 'ASC')
        //     ->orderBy('name', 'ASC')
        //     ->orderByDesc('created_at');
            
        $query = CarModel::selectRaw('car.*, car_brand.name as brand_name')
            ->join('car_brand', 'car_brand.id', '=', 'car.car_brand_id')
            ->orderByDesc('car.status')
            ->orderBy('car_brand.name', 'ASC')
            ->orderBy('car.name', 'ASC')
            ->orderBy('car.year_start', 'ASC');
            
        if ($request->filled('car_brand_id')) {
            $query = $query->where('car_brand_id', $request->input('car_brand_id'));
            $isSearch = true;
        }

        if ($request->filled('name')) {
            $query = $query->where('name', 'LIKE', "%{$request->input('name')}%");
            $isSearch = true;
        }

        return response()->json([
            'items' => $query->paginate(30),
            'brands' => CarBrandModel::orderByDesc('status')
                ->orderBy('name', 'ASC')
                ->get(),
            'is_search' => $isSearch ?? false
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\Product\CarResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(CarResquest $request)
    {
        CarModel::create([
            'car_brand_id' => $request->input('car_brand_id'),
            'name' => $request->input('name'),
            'year_start' => $request->input('year_start'),
            'year_end' => $request->input('year_end'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\Product\CarResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(CarResquest $request, $id)
    {
        $item = CarModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->car_brand_id = $request->input('car_brand_id');
            $item->name = $request->input('name');
            $item->year_start = $request->input('year_start');
            $item->year_end = $request->input('year_end');
            $item->status = $request->input('status');
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }

    /**
     * 刪除
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete($id)
    {
        $item = CarModel::find($id);
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

    /**
     * 取得單一
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function find($id)
    {
        $item = CarModel::find($id);
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

    /**
     * 排序
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory Response
     */
    public function sort(Request $request)
    {
        if (!$request->has(['items'])) {
            return response([
                'message' => '排序更新失敗。'
            ], 400);
        } else {
            DB::update(update_when_case_string('car', 'sort', $request->items));

            return response([
                'message' => '排序更新成功。'
            ]);
        }
    }

    /**
     * 狀態
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function status($id)
    {
        $item = CarModel::find($id);
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

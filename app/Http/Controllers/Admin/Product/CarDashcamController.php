<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\CarDashcamResquest;
use App\Models\CarDashcamModel;
use DB;

class CarDashcamController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.product.car_dashcam');
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        $query = CarDashcamModel::orderByDesc('created_at');

        if ($request->filled('name')) {
            $query = $query->where('name', 'LIKE', "%{$request->input('name')}%");
            $isSearch = true;
        }

        return response()->json([
            'items' => $query->paginate(15),
            'is_search' => $isSearch ?? false
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\Product\CarDashcamResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(CarDashcamResquest $request)
    {
        CarDashcamModel::create([
            'brand' => $request->input('brand'),
            'name' => $request->input('name'),
            'img' => $request->input('img'),
            'memo_in' => $request->input('memo_in'),
            'content' => $request->input('content'),
            'is_top' => $request->input('is_top'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\Product\CarDashcamResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(CarDashcamResquest $request, $id)
    {
        $item = CarDashcamModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->brand = $request->input('brand');
            $item->name = $request->input('name');
            $item->img = $request->input('img');
            $item->memo_in = $request->input('memo_in');
            $item->content = $request->input('content');
            $item->is_top = $request->input('is_top');
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
        $item = CarDashcamModel::find($id);
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
        $item = CarDashcamModel::find($id);
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
            DB::update(update_when_case_string('car_dashcam', 'sort', $request->items));

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
        $item = CarDashcamModel::find($id);
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

    /**
     * 置頂
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function top($id)
    {
        $item = CarDashcamModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->is_top = $item->is_top == 1 ? 0 : 1;
            $item->save();

            return response()->json([
                'message' => '置頂更新成功'
            ]);
        }
    }
}

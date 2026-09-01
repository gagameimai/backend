<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RecommendProductResquest;
use App\Models\RecommendProductModel;
use App\Services\RecommendProductResolver;
use DB;

class RecommendProductController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.recommend_product', [
            'types' => RecommendProductResolver::types(),
        ]);
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        $query = RecommendProductModel::orderByDesc('sort')->orderByDesc('created_at');

        if ($request->filled('product_type')) {
            $query = $query->where('product_type', $request->input('product_type'));
            $isSearch = true;
        }

        $items = $query->paginate(15);

        // 補上每筆的商品名稱／圖片／連結，方便後台列表顯示
        $resolved = RecommendProductResolver::resolveMany($items->getCollection());
        $resolvedById = collect($resolved)->keyBy('id');

        $items->getCollection()->transform(function ($item) use ($resolvedById) {
            $extra = $resolvedById->get($item->id);
            $item->product_name = $extra['name'] ?? '(商品已不存在)';
            $item->product_img = $extra['img'] ?? '';
            return $item;
        });

        return response()->json([
            'items' => $items,
            'is_search' => $isSearch ?? false
        ]);
    }

    /**
     * 依分類取得該分類底下的商品選項（下拉選單第二層，AJAX 用）
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function options(Request $request)
    {
        $type = $request->input('product_type');
        if (!$type) {
            return response()->json(['options' => []]);
        }

        return response()->json([
            'options' => RecommendProductResolver::options($type)
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\RecommendProductResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(RecommendProductResquest $request)
    {
        RecommendProductModel::create([
            'product_type' => $request->input('product_type'),
            'product_id' => $request->input('product_id'),
            'sort' => $request->input('sort') ?? 0,
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\RecommendProductResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(RecommendProductResquest $request, $id)
    {
        $item = RecommendProductModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->product_type = $request->input('product_type');
            $item->product_id = $request->input('product_id');
            $item->sort = $request->input('sort') ?? 0;
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
        $item = RecommendProductModel::find($id);
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
        $item = RecommendProductModel::find($id);
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
            DB::update(update_when_case_string('recommend_products', 'sort', $request->items));

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
        $item = RecommendProductModel::find($id);
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

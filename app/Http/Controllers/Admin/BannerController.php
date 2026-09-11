<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\BannerResquest;
use App\Models\BannerModel;
use DB;

class BannerController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.banner');
    }

    /**
     * 取得全部
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        return response()->json([
            'items' => BannerModel::orderByDesc('status')
                ->orderBy('sort', 'ASC')
                ->orderByDesc('created_at')
                ->paginate(15)
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\BannerResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(BannerResquest $request)
    {
        BannerModel::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'img' => $request->input('img'),
            'img_mobile' => $request->input('img_mobile'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\BannerResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(BannerResquest $request, $id)
    {
        $item = BannerModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->name = $request->input('name');
            $item->url = $request->input('url');
            $item->img = $request->input('img');
            $item->img_mobile = $request->input('img_mobile');
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
        $item = BannerModel::find($id);
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
        $item = BannerModel::find($id);
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
            DB::update(update_when_case_string('banner', 'sort', $request->items));

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
        $item = BannerModel::find($id);
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

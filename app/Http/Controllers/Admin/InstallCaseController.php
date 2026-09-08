<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\InstallCaseResquest;
use App\Models\InstallCaseModel;
use App\Models\CarBrandModel;
use App\Models\CarModel;
use DB;

class InstallCaseController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.install_case');
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        $query = InstallCaseModel::with(['brand', 'car'])
            ->orderByDesc('sort')->orderByDesc('created_at');

        if ($request->filled('name')) {
            $query = $query->where('name', 'LIKE', "%{$request->input('name')}%");
            $isSearch = true;
        }

        return response()->json([
            'items' => $query->paginate(15),
            // 車型下拉用：品牌與車款清單（與安卓車框後台同一份資料）
            'brands' => CarBrandModel::orderByDesc('status')->orderBy('name', 'ASC')->get(),
            'cars' => CarModel::orderByDesc('status')->orderBy('name', 'ASC')->get(),
            'is_search' => $isSearch ?? false
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\InstallCaseResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(InstallCaseResquest $request)
    {
        InstallCaseModel::create([
            'category' => $request->input('category'),
            'name' => $request->input('name'),
            'img' => $request->input('img'),
            'sort' => $request->input('sort') ?? 0,
            'status' => $request->input('status'),
            'installed_at' => $request->input('installed_at') ?: null,
            'is_pinned' => $request->input('is_pinned') ?? 0,
            'is_home' => $request->input('is_home') ?? 0,
            'home_sort' => $request->input('home_sort') ?? 0,
            'car_brand_id' => $request->input('car_brand_id'),
            'car_id' => $request->input('car_id'),
            'product' => $request->input('product'),
            'dealer' => $request->input('dealer'),
            'need' => $request->input('need'),
            'work' => $request->input('work'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\InstallCaseResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(InstallCaseResquest $request, $id)
    {
        $item = InstallCaseModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->category = $request->input('category');
            $item->name = $request->input('name');
            $item->img = $request->input('img');
            $item->sort = $request->input('sort') ?? 0;
            $item->status = $request->input('status');
            $item->installed_at = $request->input('installed_at') ?: null;
            $item->is_pinned = $request->input('is_pinned') ?? 0;
            $item->is_home = $request->input('is_home') ?? 0;
            $item->home_sort = $request->input('home_sort') ?? 0;
            $item->car_brand_id = $request->input('car_brand_id');
            $item->car_id = $request->input('car_id');
            $item->product = $request->input('product');
            $item->dealer = $request->input('dealer');
            $item->need = $request->input('need');
            $item->work = $request->input('work');
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
        $item = InstallCaseModel::find($id);
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
        $item = InstallCaseModel::find($id);
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
            DB::update(update_when_case_string('install_cases', 'sort', $request->items));

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
        $item = InstallCaseModel::find($id);
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
     * 切換置頂
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function pinned($id)
    {
        $item = InstallCaseModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        }

        $item->is_pinned = $item->is_pinned == 1 ? 0 : 1;
        $item->save();

        return response()->json([
            'message' => '置頂設定已更新'
        ]);
    }

    /**
     * 切換是否顯示於首頁（首頁最多 3 筆）
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function home($id)
    {
        $item = InstallCaseModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        }

        // 要開啟時先檢查首頁是不是已經有 3 筆
        if ($item->is_home != 1) {
            $count = InstallCaseModel::where('is_home', 1)->where('id', '<>', $item->id)->count();
            if ($count >= 3) {
                return response()->json([
                    'message' => '首頁最多只能放 3 筆，請先把其中一筆關閉'
                ], 400);
            }
        }

        $item->is_home = $item->is_home == 1 ? 0 : 1;
        $item->save();

        return response()->json([
            'message' => '首頁設定已更新'
        ]);
    }
}

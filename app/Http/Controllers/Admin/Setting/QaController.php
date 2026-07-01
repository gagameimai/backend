<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingModel;

class QaController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.setting.qa');
    }

    /**
     * 取得全部
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        return response()->json([
            'item' => SettingModel::where('type', 'qa')->first()
        ]);
    }

    /**
     * 更新
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(Request $request)
    {
        $item = SettingModel::where('type', 'qa')->first();
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->content = $request->input('content');
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }
}

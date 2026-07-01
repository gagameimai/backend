<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingModel;

class WebsiteInfoController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.setting.website');
    }

    /**
     * 取得全部
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        $item = SettingModel::where('type', 'website')->first();
        $item->content = json_decode($item->content, true);

        return response()->json([
            'item' => $item
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
        $item = SettingModel::where('type', 'website')->first();
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->content = json_encode($request->input('content'));
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }
}

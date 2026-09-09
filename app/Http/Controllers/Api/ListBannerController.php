<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListBannerModel;
use Illuminate\Http\Request;

class ListBannerController extends Controller
{
    /**
     * 產品列表／總覽頁面的 Banner 背景圖
     *
     * GET /api/list_banner?page=multimedia&type=2
     * page 對應頁面代碼，詳見 config/list_banner.php；type 該頁面只有單一 banner 時可省略（預設 'default'）
     *
     * 查無設定或沒有上傳圖片時，img 回傳 null，前台改用預設漸層背景。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $pageKey = $request->input('page');
            if (empty($pageKey)) {
                return response()->json([
                    'message' => '缺少 page 參數'
                ], 400);
            }

            $typeKey = $request->input('type');
            $typeKey = $typeKey === null || $typeKey === '' ? 'default' : (string) $typeKey;

            $item = ListBannerModel::selectRaw('img')
                ->where('page_key', $pageKey)
                ->where('type_key', $typeKey)
                ->first();

            return response()->json([
                'result' => [
                    'img' => $item->img ?? null,
                ]
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('ListBannerController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeSectionModel;

class HomeSectionController extends Controller
{
    /**
     * 首頁「滿版情境區塊」內容與背景圖
     *
     * GET /api/home_section
     *
     * 固定回傳 config/home_section.php 定義的 3 個區塊（zone1/zone2/zone3）。
     * 後台還沒編輯過的區塊，content/img/img_mobile 一律回傳 null，
     * 前台收到 null 就維持目前寫死的預設文字與預設背景圖，不會空白。
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            $existing = HomeSectionModel::all()->keyBy('section_key');

            $result = [];
            foreach (config('home_section.sections') as $sectionKey => $section) {
                $item = $existing->get($sectionKey);
                $result[$sectionKey] = [
                    'content' => $item->content ?? null,
                    'img' => $item->img ?? null,
                    'img_mobile' => $item->img_mobile ?? null,
                ];
            }

            return response()->json([
                'result' => $result
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('HomeSectionController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

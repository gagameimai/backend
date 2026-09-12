<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionResquest;
use App\Models\HomeSectionModel;

class HomeSectionController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.home_section', [
            'sections' => config('home_section.sections'),
        ]);
    }

    /**
     * 取得全部：依 config/home_section.php 列出固定 3 個區塊，
     * 有編輯過的帶出現有內容／圖片，沒編輯過的欄位就是 null（前台會維持預設文字/背景）。
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        $existing = HomeSectionModel::all()->keyBy('section_key');

        $rows = [];
        foreach (config('home_section.sections') as $sectionKey => $section) {
            $found = $existing->get($sectionKey);
            $rows[] = [
                'section_key' => $sectionKey,
                'section_name' => $section['name'],
                'content' => $found->content ?? null,
                'img' => $found->img ?? null,
                'img_mobile' => $found->img_mobile ?? null,
            ];
        }

        return response()->json([
            'items' => $rows,
        ]);
    }

    /**
     * 新增／更新（同一個區塊只會有一筆，用 updateOrCreate 直接寫入）
     *
     * @param \App\Http\Requests\Admin\HomeSectionResquest $request
     * @param string $section_key
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(HomeSectionResquest $request, $section_key)
    {
        $sections = config('home_section.sections');
        if (!isset($sections[$section_key])) {
            return response()->json([
                'message' => '查無此區塊設定'
            ], 400);
        }

        HomeSectionModel::updateOrCreate(
            ['section_key' => $section_key],
            [
                'content' => $request->input('content') ?: null,
                'img' => $request->input('img') ?: null,
                'img_mobile' => $request->input('img_mobile') ?: null,
            ]
        );

        return response()->json([
            'message' => '更新成功'
        ]);
    }
}

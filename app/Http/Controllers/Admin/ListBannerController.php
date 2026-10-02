<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListBannerResquest;
use App\Models\ListBannerModel;

class ListBannerController extends Controller
{
    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.list_banner', [
            'pages' => config('list_banner.pages'),
        ]);
    }

    /**
     * 取得全部：依 config/list_banner.php 列出每個頁面╱分類組合，
     * 有設定過 Banner 的帶出現有圖片網址，沒設定過的 img 就是 null（前台會用預設漸層背景）。
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        $existing = ListBannerModel::all()->keyBy(function ($row) {
            return $row->page_key . '|' . $row->type_key;
        });

        $rows = [];
        foreach (config('list_banner.pages') as $pageKey => $page) {
            $types = $page['types'] ?? ['default' => '預設'];
            foreach ($types as $typeKey => $typeName) {
                $found = $existing->get($pageKey . '|' . $typeKey);
                $rows[] = [
                    'page_key' => $pageKey,
                    'page_name' => $page['name'],
                    'type_key' => (string) $typeKey,
                    'type_name' => $typeName,
                    'url' => is_array(config('list_banner.urls.' . $pageKey)) ? (config('list_banner.urls.' . $pageKey)[$typeKey] ?? '') : (config('list_banner.urls.' . $pageKey) ?? ''),
                    'img' => $found->img ?? null,
                    'img_mobile' => $found->img_mobile ?? null,
                    'kicker' => $found->kicker ?? null,
                    'title' => $found->title ?? null,
                    'description' => $found->description ?? null,
                    'kicker_color' => $found->kicker_color ?? null,
                    'title_color' => $found->title_color ?? null,
                    'desc_color' => $found->desc_color ?? null,
                ];
            }
        }

        return response()->json([
            'items' => $rows,
        ]);
    }

    /**
     * 新增／更新（同一個頁面＋分類只會有一筆，用 updateOrCreate 直接寫入）
     *
     * @param \App\Http\Requests\Admin\ListBannerResquest $request
     * @param string $page_key
     * @param string $type_key
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(ListBannerResquest $request, $page_key, $type_key)
    {
        $pages = config('list_banner.pages');
        if (!isset($pages[$page_key])) {
            return response()->json([
                'message' => '查無此頁面設定'
            ], 400);
        }

        $types = $pages[$page_key]['types'] ?? ['default' => '預設'];
        if (!isset($types[$type_key])) {
            return response()->json([
                'message' => '查無此分類設定'
            ], 400);
        }

        ListBannerModel::updateOrCreate(
            ['page_key' => $page_key, 'type_key' => $type_key],
            [
                'img' => $request->input('img') ?: null,
                'img_mobile' => $request->input('img_mobile') ?: null,
                'kicker' => trim((string) $request->input('kicker')) ?: null,
                'title' => trim((string) $request->input('title')) ?: null,
                'description' => trim((string) $request->input('description')) ?: null,
                'kicker_color' => $request->input('kicker_color') ?: null,
                'title_color' => $request->input('title_color') ?: null,
                'desc_color' => $request->input('desc_color') ?: null,
            ]
        );

        return response()->json([
            'message' => '更新成功'
        ]);
    }
}

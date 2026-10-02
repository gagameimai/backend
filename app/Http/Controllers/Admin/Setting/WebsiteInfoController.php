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
     * 把 setting.content 的 JSON 解成陣列，並確保 seo.pages 空的時候是物件 {} 而不是 []。
     * （PHP 空陣列會被 json_encode 成 []，Agent 用 seo.pages['/'] 這種寫法就會出錯；前台與 Agent 都共用這個。）
     *
     * @param string|null $json
     * @return array|null
     */
    public static function decodeContent($json)
    {
        $c = json_decode((string) $json, true);
        if (is_array($c) && isset($c['seo']) && is_array($c['seo']) && empty($c['seo']['pages'])) {
            $c['seo']['pages'] = new \stdClass();
        }
        return $c;
    }

    /**
     * 取得全部
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all()
    {
        $item = SettingModel::where('type', 'website')->first();
        $item->content = self::decodeContent($item->content);

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
            $content = $request->input('content');
            // 全站底色：只接受 #RRGGBB，其他（含空字串）一律當成「用預設」，不寫進資料庫
            if (is_array($content)) {
                foreach (['theme_dark', 'theme_footer', 'theme_accent', 'theme_bg2'] as $key) {
                    if (isset($content[$key]) && !preg_match('/^#[0-9a-fA-F]{6}$/', (string) $content[$key])) {
                        unset($content[$key]);
                    }
                }
            }
            // 全站標誌圖片：只收字串網址，空的就不存（前台＝用預設圖）
            if (is_array($content)) {
                $logoKeys = ['logo_favicon', 'logo_cobrand_dark', 'logo_cobrand_white', 'logo_clarion_dark', 'logo_clarion_white', 'logo_footer'];
                foreach ($logoKeys as $key) {
                    if (isset($content[$key]) && (!is_string($content[$key]) || trim($content[$key]) === '')) {
                        unset($content[$key]);
                    }
                }
                // 不認識的 logo_* 鍵（例如打錯字 logo_header）直接丟掉，免得 Agent 以為有改到
                foreach (array_keys($content) as $key) {
                    if (is_string($key) && strpos($key, 'logo_') === 0 && !in_array($key, $logoKeys, true)) {
                        unset($content[$key]);
                    }
                }
            }
            // SEO／GEO 設定：只留需要的欄位並限制長度
            if (is_array($content) && isset($content['seo'])) {
                $in = is_array($content['seo']) ? $content['seo'] : [];
                $str = function ($v, $n) {
                    return mb_substr(trim(is_scalar($v) ? (string) $v : ''), 0, $n);
                };
                $seo = [];
                foreach (['company_zh', 'company_en', 'phone_intl', 'addr_region', 'addr_locality', 'addr_street'] as $k) {
                    if (isset($in[$k]) && $str($in[$k], 120) !== '') {
                        $seo[$k] = $str($in[$k], 120);
                    }
                }
                foreach (['og_image', 'gsc', 'bing'] as $k) {
                    if (isset($in[$k]) && $str($in[$k], 500) !== '') {
                        $seo[$k] = $str($in[$k], 500);
                    }
                }
                $seo['flags'] = [
                    'org' => empty($in['flags']) || !array_key_exists('org', $in['flags']) ? 1 : (empty($in['flags']['org']) ? 0 : 1),
                    'product' => empty($in['flags']) || !array_key_exists('product', $in['flags']) ? 1 : (empty($in['flags']['product']) ? 0 : 1),
                    'ai_bots' => empty($in['flags']) || !array_key_exists('ai_bots', $in['flags']) ? 1 : (empty($in['flags']['ai_bots']) ? 0 : 1),
                ];
                $seo['pages'] = [];
                if (!empty($in['pages']) && is_array($in['pages'])) {
                    foreach ($in['pages'] as $path => $row) {
                        if (!is_array($row) || !is_string($path) || $path === '' || $path[0] !== '/') {
                            continue;
                        }
                        $t = $str($row['title'] ?? '', 120);
                        $d = $str($row['description'] ?? '', 400);
                        if ($t !== '' || $d !== '') {
                            $seo['pages'][mb_substr($path, 0, 120)] = ['title' => $t, 'description' => $d];
                        }
                    }
                }
                $seo['faq'] = [];
                if (!empty($in['faq']) && is_array($in['faq'])) {
                    foreach (array_slice($in['faq'], 0, 30) as $row) {
                        $q = $str($row['q'] ?? '', 200);
                        $a = $str($row['a'] ?? '', 1500);
                        if ($q !== '' && $a !== '') {
                            $seo['faq'][] = ['q' => $q, 'a' => $a];
                        }
                    }
                }
                // pages 沒有任何設定時仍要是物件 {}（不是陣列 []），Agent／前台讀 seo.pages['/'] 才不會出錯
                if (empty($seo['pages'])) {
                    $seo['pages'] = new \stdClass();
                }
                $content['seo'] = $seo;
            }
            // 產品類別開關：只留需要的欄位，避免亂塞資料
            if (is_array($content) && isset($content['categories'])) {
                $clean = [];
                foreach (['clarion', 'mm'] as $brand) {
                    $rows = $content['categories'][$brand] ?? [];
                    if (!is_array($rows)) {
                        continue;
                    }
                    // 只接受 config/product_categories.php 裡有的 key（前台也只認這些），同一個 key 只留第一筆
                    $allowed = array_column(config("product_categories.{$brand}.items", []), 'key');
                    $seen = [];
                    foreach ($rows as $r) {
                        if (!is_array($r) || empty($r['key']) || !in_array((string) $r['key'], $allowed, true) || isset($seen[$r['key']])) {
                            continue;
                        }
                        $seen[$r['key']] = true;
                        $row = [
                            'key' => mb_substr((string) $r['key'], 0, 40),
                            'name' => mb_substr(trim((string) ($r['name'] ?? '')), 0, 40),
                            'on' => empty($r['on']) ? 0 : 1,
                        ];
                        $clean[$brand][] = $row;
                    }
                }
                $content['categories'] = $clean;
            }
            $item->content = json_encode($content);
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }
}

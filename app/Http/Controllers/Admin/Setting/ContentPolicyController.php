<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingModel;

/**
 * 內容來源與更正聲明（前台 /content-policy）
 * setting.type = 'content_policy'，content 是 JSON：
 * { zh: {title, intro, body}, en: {title, intro, body} }
 * body 是 HTML（CKEditor），title／intro 是純文字。欄位留空＝前台用程式內建的預設文字。
 */
class ContentPolicyController extends Controller
{
    public const LANGS = ['zh', 'en'];

    public function index()
    {
        return view('admin.setting.content_policy');
    }

    /** 把資料庫的 content 轉成固定形狀，缺的鍵補空字串 */
    public static function decodeContent($raw): array
    {
        $data = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: []);
        $out = [];
        foreach (self::LANGS as $lang) {
            $src = is_array($data[$lang] ?? null) ? $data[$lang] : [];
            $out[$lang] = [
                'title' => trim((string) ($src['title'] ?? '')),
                'intro' => trim((string) ($src['intro'] ?? '')),
                'body'  => (string) ($src['body'] ?? ''),
            ];
        }
        return $out;
    }

    public function all()
    {
        $item = SettingModel::where('type', 'content_policy')->first();
        return response()->json([
            'item' => [
                'content' => self::decodeContent($item ? $item->content : null),
                'updated_at' => $item && $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : null,
            ],
            'defaults' => config('content_policy_default'),
        ]);
    }

    public function update(Request $request)
    {
        $item = SettingModel::where('type', 'content_policy')->first();
        if (empty($item)) {
            return response(['message' => '查無資料'], 400);
        }
        // Agent 可只送其中一種語言或其中一欄：沒送的鍵維持原值
        $current = self::decodeContent($item->content);
        $incoming = $request->input('content');
        if (!is_array($incoming)) {
            return response(['message' => 'content 必須是物件：{ zh: {title, intro, body}, en: {…} }'], 422);
        }
        foreach (self::LANGS as $lang) {
            if (!is_array($incoming[$lang] ?? null)) continue;
            foreach (['title', 'intro', 'body'] as $k) {
                if (array_key_exists($k, $incoming[$lang])) {
                    $current[$lang][$k] = $k === 'body' ? (string) $incoming[$lang][$k] : trim((string) $incoming[$lang][$k]);
                }
            }
        }
        $item->content = json_encode($current, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $item->save();

        return response()->json(['message' => '更新成功']);
    }
}

<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Agent API 寫入前後的共通處理（所有資源一體適用，不必每個後台控制器各修一次）：
 *
 * 1. 圖片欄位統一：欄位值可以是
 *      - upload 回傳的網址（或任何含 /storage/files/... 的本站網址，網域、%20 編碼不拘）
 *      - 外部 https 圖片網址（伺服器自己下載存進 files/1/AgentImport/年月/）
 *      - data:image/...;base64,... （伺服器解碼存檔）
 *      - multipart 直接帶檔案（欄位名稱＝圖片欄位名稱）
 *    一律換成「後台檔案管理員同格式的站內網址」再交給原本的後台控制器；對不到時回 422 並指出是哪個欄位。
 * 2. PATCH 合併：沒送的欄位沿用資料庫現有的值（原本是整份取代，沒送的 required 欄位會驗證失敗、有的控制器還會把沒送的欄位清空）。
 * 3. 寫完驗證：送了圖片但存進去是空的，在回應加 warnings，不再「回成功但圖不見」。
 */
class AgentWriteHelper
{
    const FRAME = 'frame'; // 車框的 imgArr[群組 0..3][序號 0..2]

    public static function imageFields(string $resource)
    {
        return config("agent_api.images.{$resource}");
    }

    /** 整理圖片網址：回傳標準網址，或 null（對不到本站檔案）。 */
    public static function resolveLocal($url)
    {
        $url = trim((string) $url);
        if ($url === '') return null;
        $path = rawurldecode((string) (parse_url($url, PHP_URL_PATH) ?: $url));
        $pos = strpos($path, '/storage/');
        if ($pos === false) return null;
        $rel = ltrim(substr($path, $pos + 9), '/');
        if ($rel === '' || strpos($rel, '..') !== false) return null;
        if (!Storage::disk('public')->exists($rel)) return null;
        return rtrim((string) config('app.url'), '/') . '/storage/' . $rel;
    }

    /**
     * 寫入前：合併沒送的欄位（僅 PATCH 主更新）＋圖片統一。
     * 回傳 JsonResponse 代表要直接擋下（422）；回傳 null 代表可以繼續。
     */
    public static function prepare(Request $request, string $resource, $recordId, bool $isMainUpdate, $action = null)
    {
        $cfg = config("agent_api.resources.{$resource}", []);
        $request->attributes->set('agent_sort_sent', $request->has('sort'));   // 合併前先記下「使用者有沒有真的送 sort」

        // 0) 切換型端點（/status、/top、/pinned、/home）：body 有帶目標值時改成「設定」，已經是那個值就不切換
        //    （原本不管 body 送什麼都是切換，Hermes 重試一次就變回去了）
        if ($recordId !== null && $request->method() === 'PATCH' && $action) {
            $map = ['status' => 'status', 'top' => 'is_top', 'pinned' => 'is_pinned', 'home' => 'is_home'];
            $col = $map[$action] ?? null;
            $modelClass = $cfg['model'] ?? null;
            if ($col && $request->has($col) && $modelClass && class_exists($modelClass)) {
                $row = $modelClass::find($recordId);
                if ($row && isset($row->{$col}) && (int) $row->{$col} === (int) $request->input($col)) {
                    return response()->json(['message' => "{$col} 已經是 " . (int) $request->input($col) . '，未變更', 'unchanged' => true], 200);
                }
            }
        }
        $cfg['_name'] = $resource;
        $style = $cfg['style'] ?? 'crud';

        // 1) PATCH 合併：沒送的欄位沿用現有值
        if ($isMainUpdate && $recordId !== null && $style === 'crud') {
            self::mergeExisting($request, $cfg, $recordId);
        }

        // 1b) kv 型（list_banner／home_section）：送一個欄位不能把另一個欄位清成空（這兩個控制器沒送就存 null）
        if ($style === 'kv' && $request->method() === 'PATCH') {
            self::mergeKv($request, $cfg);
        }

        // 1c) website（網站基本設定）：content 是一個物件（copyright／address／tel／email／facebook／instagram／youtube）。
        //     原本是整份取代，Hermes 少帶一個鍵那個鍵就不見；改成「沒送的鍵沿用現有值」，送 {"content":{"tel":"..."}} 只改電話。
        //     qa／about 的 content 是一整段 HTML，沒有可合併的結構，維持整份取代。
        if ($resource === 'website' && $request->method() === 'PATCH' && is_array($request->input('content'))) {
            try {
                $row = \App\Models\SettingModel::where('type', 'website')->first();
                $cur = $row ? json_decode($row->content, true) : null;
                if (is_array($cur)) {
                    $in = $request->input('content');
                    $merged = array_merge($cur, $in);
                    // seo 再往下合併一層：只送 seo.company_en 不會把 company_zh／pages／faq 清掉。
                    // seo.pages 以「網址 → 標題與說明」合併（送哪一頁改哪一頁）；seo.flags 逐個旗標合併；
                    // seo.faq 是整份清單，有送就整份取代（要刪一題就送少一題的完整清單）。
                    if (isset($in['seo']) && is_array($in['seo']) && isset($cur['seo']) && is_array($cur['seo'])) {
                        $seo = array_merge($cur['seo'], $in['seo']);
                        foreach (['pages', 'flags'] as $k) {
                            if (isset($in['seo'][$k]) && is_array($in['seo'][$k]) && isset($cur['seo'][$k]) && is_array($cur['seo'][$k])) {
                                $seo[$k] = array_merge($cur['seo'][$k], $in['seo'][$k]);
                                // 要刪掉某一頁的覆蓋就送 null：{"seo":{"pages":{"/about":null}}}
                                $seo[$k] = array_filter($seo[$k], function ($v) { return $v !== null; });
                            }
                        }
                        $merged['seo'] = $seo;
                    }
                    // categories：只送 clarion 不會把 mm 清掉（各品牌的清單本身仍是整份取代，順序＝清單順序）
                    if (isset($in['categories']) && is_array($in['categories']) && isset($cur['categories']) && is_array($cur['categories'])) {
                        $merged['categories'] = array_merge($cur['categories'], $in['categories']);
                    }
                    $request->merge(['content' => $merged]);
                }
            } catch (\Throwable $e) {
                // 讀不到就照原本整份取代
            }
        }

        // 2) 圖片統一
        $fields = self::imageFields($resource);
        if (!$fields) return null;
        try {
            if ($fields === self::FRAME) {
                $arr = $request->input('imgArr');
                if (is_array($arr)) {
                    for ($i = 0; $i <= 3; $i++) {
                        for ($j = 0; $j < 3; $j++) {
                            if (!isset($arr[$i]) || !is_array($arr[$i]) || !array_key_exists($j, $arr[$i])) continue;
                            $v = self::toLocalUrl($arr[$i][$j], $request, "imgArr.{$i}.{$j}");
                            $arr[$i][$j] = $v;
                        }
                    }
                    $request->merge(['imgArr' => $arr]);
                }
            } else {
                foreach ($fields as $f) {
                    if ($request->hasFile($f)) {
                        $request->merge([$f => self::storeUploaded($request->file($f))]);
                    } elseif ($request->has($f)) {
                        $request->merge([$f => self::toLocalUrl($request->input($f), $request, $f)]);
                    }
                }
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        return null;
    }

    /** 寫入後：把存進去的圖片網址回報，空的就警告。 */
    public static function verify($response, Request $request, string $resource, $recordId)
    {
        $fields = self::imageFields($resource);
        if (!$fields || $recordId === null || !($response instanceof JsonResponse)) return $response;
        if ($response->getStatusCode() >= 300) return $response;
        $modelClass = config("agent_api.resources.{$resource}.model");
        if (!$modelClass || !class_exists($modelClass)) return $response;
        $row = $modelClass::find($recordId);
        if (!$row) return $response;

        $saved = [];
        $warnings = [];
        if ($fields === self::FRAME) {
            foreach (['img' => 0, 'img1' => 1, 'img2' => 2, 'img3' => 3] as $col => $i) {
                $list = json_decode((string) $row->{$col}, true) ?: [];
                $saved["imgArr.{$i}"] = array_values($list);
                for ($j = 0; $j < 3; $j++) {
                    $sent = $request->input("imgArr.{$i}.{$j}");
                    if (!empty($sent) && empty($list[$j] ?? '')) {
                        $warnings[] = "imgArr.{$i}.{$j} 有送網址，但存進去是空的";
                    }
                }
            }
        } else {
            foreach ($fields as $f) {
                $saved[$f] = $row->{$f};
                if ($request->has($f) && !empty($request->input($f)) && empty($row->{$f})) {
                    $warnings[] = "{$f} 有送圖片，但存進去是空的";
                }
            }
        }
        $data = $response->getData(true);
        if (!is_array($data)) $data = ['result' => $data];
        $data['saved_images'] = $saved;
        if ($warnings) $data['warnings'] = $warnings;
        $response->setData($data);
        return $response;
    }

    /** POST 新增前記下目前最大 id，新增後靠它找出新那一筆（原本新增只回「新增成功」，不回 id）。 */
    public static function maxId(string $resource)
    {
        $cfg = config("agent_api.resources.{$resource}", []);
        if (($cfg['style'] ?? 'crud') !== 'crud') return null;
        $modelClass = $cfg['model'] ?? null;
        if (!$modelClass || !class_exists($modelClass)) return null;
        try {
            return (int) $modelClass::max('id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * 寫入後的共通收尾（PATCH 主更新與 POST 新增）：
     *  - POST：回應加 id／item（新增的那一筆）
     *  - 有帶 sort、但該控制器的新增／更新不收 sort（除 install_case、recommend_product 外都是）→ 直接寫進 sort 欄位
     *  - 圖片存進去了沒（verify）
     */
    public static function finish($response, Request $request, string $resource, $recordId, $maxBefore)
    {
        if (!($response instanceof JsonResponse) || $response->getStatusCode() >= 300) return $response;
        $cfg = config("agent_api.resources.{$resource}", []);
        $modelClass = $cfg['model'] ?? null;
        $extra = [];

        if ($request->method() === 'POST' && $recordId === null && $maxBefore !== null && $modelClass && class_exists($modelClass)) {
            $row = $modelClass::where('id', '>', $maxBefore)->orderByDesc('id')->first();
            if ($row) {
                $recordId = $row->id;
                $extra['id'] = $row->id;
                $extra['item'] = $row->toArray();
            }
        }

        if ($recordId !== null && $modelClass && class_exists($modelClass) && $request->attributes->get('agent_sort_sent')
            && !in_array($resource, ['install_case', 'recommend_product'], true)) {
            $row = $modelClass::find($recordId);
            if ($row && array_key_exists('sort', $row->getAttributes()) && is_numeric($request->input('sort'))) {
                $row->sort = (int) $request->input('sort');
                $row->save();
                $extra['sort_set'] = (int) $row->sort;
                if (isset($extra['item'])) $extra['item']['sort'] = $extra['sort_set'];
            }
        }

        if ($extra) {
            $data = $response->getData(true);
            if (!is_array($data)) $data = ['result' => $data];
            $response->setData($data + $extra);
        }
        return self::verify($response, $request, $resource, $recordId);
    }

    // ───────── 內部 ─────────

    private static function mergeExisting(Request $request, array $cfg, $recordId): void
    {
        $modelClass = $cfg['model'] ?? null;
        if (!$modelClass || !class_exists($modelClass)) return;
        $row = $modelClass::find($recordId);
        if (!$row) return;
        $attrs = $row->toArray();
        // 車框的 img／img1／img2／img3 是 JSON 字串，由它自己的 imgArr 流程處理，不能合併
        $skip = ['id', 'created_at', 'updated_at', 'deleted_at'];
        if (self::imageFields($cfg['_name'] ?? '') === self::FRAME) $skip = array_merge($skip, ['img', 'img1', 'img2', 'img3']);
        $fill = [];
        // 不只看驗證規則的欄位：有些控制器也會讀規則外的欄位（例如 memo_in），沒送就會被清成空
        foreach ($attrs as $key => $val) {
            if (in_array($key, $skip, true) || $request->has($key) || is_array($val)) continue;
            $fill[$key] = $val;
        }
        if ($fill) $request->merge($fill);
    }

    private static function mergeKv(Request $request, array $cfg): void
    {
        $modelClass = $cfg['model'] ?? null;
        if (!$modelClass || !class_exists($modelClass) || empty($cfg['key'])) return;
        preg_match_all('/\{(\w+)\}/', $cfg['key'], $m);
        $where = [];
        foreach ($m[1] as $param) {
            $where[$param] = $request->route($param);
        }
        if (!$where) return;
        $row = $modelClass::where($where)->first();
        if (!$row) return;
        $fill = [];
        foreach ($row->toArray() as $key => $val) {
            if (in_array($key, ['id', 'created_at', 'updated_at'], true) || isset($where[$key])) continue;
            if ($request->has($key) || is_array($val)) continue;
            $fill[$key] = $val;
        }
        if ($fill) $request->merge($fill);
    }

    private static function toLocalUrl($value, Request $request, string $field)
    {
        if ($value === null || $value === '') return $value;
        if (!is_string($value)) {
            throw new \InvalidArgumentException("{$field} 必須是圖片網址（字串）");
        }
        $v = trim($value);

        if (stripos($v, 'data:image/') === 0) {
            if (!preg_match('#^data:image/(png|jpe?g|webp|gif);base64,(.+)$#is', $v, $m)) {
                throw new \InvalidArgumentException("{$field} 的 base64 格式不支援（只收 png／jpg／webp／gif）");
            }
            $bin = base64_decode(str_replace(' ', '+', $m[2]), true);
            if ($bin === false || $bin === '') {
                throw new \InvalidArgumentException("{$field} 的 base64 內容無法解碼");
            }
            return self::storeBinary($bin, $field, $field);
        }

        $local = self::resolveLocal($v);
        if ($local) return $local;

        if (preg_match('#^https?://#i', $v)) {
            $host = parse_url($v, PHP_URL_HOST);
            if (!$host) throw new \InvalidArgumentException("{$field} 網址格式不對：{$v}");
            // 已經是本站網域但檔案不存在：不要去抓自己，直接說找不到
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            if ($appHost && strcasecmp($host, $appHost) === 0) {
                throw new \InvalidArgumentException("{$field} 的網址在本站找不到檔案：{$v}（請先用 /api/agent/upload，並直接使用回傳的 url）");
            }
            $ip = gethostbyname($host);
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new \InvalidArgumentException("{$field} 的網址主機不允許（內網或無法解析）：{$host}");
            }
            try {
                $res = Http::timeout(20)->withOptions(['allow_redirects' => ['max' => 3]])->get($v);
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException("{$field} 下載失敗：{$v}（{$e->getMessage()}）");
            }
            if (!$res->successful()) {
                throw new \InvalidArgumentException("{$field} 下載失敗，對方回 HTTP {$res->status()}：{$v}");
            }
            $bin = $res->body();
            $name = pathinfo((string) parse_url($v, PHP_URL_PATH), PATHINFO_FILENAME);
            return self::storeBinary($bin, $field, $name);
        }

        throw new \InvalidArgumentException("{$field} 的網址找不到檔案：{$v}（請先用 /api/agent/upload 上傳，並直接使用回傳的 url；也可以給 https 圖片網址、data:image base64，或直接用 multipart 帶檔案）");
    }

    private static function storeBinary(string $bin, string $field, string $baseName): string
    {
        $max = (int) config('agent_api.upload.max_kb', 20480) * 1024;
        if (strlen($bin) > $max) {
            throw new \InvalidArgumentException("{$field} 的圖片超過大小上限 " . ($max / 1048576) . 'MB');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bin);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
        if (!$ext) {
            throw new \InvalidArgumentException("{$field} 不是支援的圖片（偵測到 {$mime}；只收 jpg／png／webp／gif）");
        }
        $base = trim(preg_replace('#[\\\\/:*?"<>|\x00-\x1f\s]+#', '_', $baseName)) ?: 'image';
        $base = mb_substr($base, 0, 60);
        $dir = 'files/1/AgentImport/' . date('Ym');
        $disk = Storage::disk('public');
        $disk->makeDirectory($dir);
        $name = $base . '.' . $ext;
        $n = 1;
        while ($disk->exists($dir . '/' . $name)) {
            $name = $base . '-' . (++$n) . '.' . $ext;
        }
        $disk->put($dir . '/' . $name, $bin);
        return rtrim((string) config('app.url'), '/') . '/storage/' . $dir . '/' . $name;
    }

    private static function storeUploaded($file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            throw new \InvalidArgumentException('上傳的檔案不是支援的圖片（jpg／png／webp／gif）');
        }
        $bin = file_get_contents($file->getRealPath());
        return self::storeBinary($bin, $file->getClientOriginalName(), pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    }
}

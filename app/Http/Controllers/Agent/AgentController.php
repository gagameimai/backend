<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentAuditLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Agent API 自己的三個端點：
 *   GET  /api/agent/me       這把金鑰是誰、有哪些權限
 *   GET  /api/agent/schema   所有資源的欄位與驗證規則（直接讀既有 FormRequest 的 rules()），agent 不用猜欄位
 *   POST /api/agent/upload   上傳圖片／檔案，回傳可以直接填進 img 欄位的網址
 */
class AgentController extends Controller
{
    public function me(Request $request)
    {
        $key = $request->attributes->get('agent_key');
        return response()->json([
            'name' => $key->name,
            'key_prefix' => $key->key_prefix,
            'scopes' => $key->scopes,
            'scope_descriptions' => config('agent_api.scopes'),
            'last_used_at' => optional($key->last_used_at)->toDateTimeString(),
        ]);
    }

    public function schema(Request $request)
    {
        $out = [];
        foreach (config('agent_api.resources') as $name => $cfg) {
            $rules = [];
            $labels = [];
            if (!empty($cfg['request']) && class_exists($cfg['request'])) {
                try {
                    $req = new $cfg['request']();
                    $rules = method_exists($req, 'rules') ? $req->rules() : [];
                    $labels = method_exists($req, 'attributes') ? $req->attributes() : [];
                } catch (\Throwable $e) {
                    $rules = ['_error' => '無法讀取規則：' . $e->getMessage()];
                }
            }
            $style = $cfg['style'] ?? 'crud';
            $endpoints = $this->endpointsFor($name, $cfg, $style);
            $out[$name] = [
                'scope' => $cfg['scope'],
                'style' => $style,
                'endpoints' => $endpoints,
                'list_params' => $cfg['list_params'] ?? [],   // GET /all 可帶的篩選參數（加上 page）
                'fields' => $rules,
                'field_labels' => $labels,
                'notes' => $cfg['notes'] ?? null,             // 這個資源的特殊規則（例：車框圖片是 imgArr）
                'docs' => config("agent_field_docs.resources.{$name}"),   // 白話說明：後台叫什麼、前台哪裡用、關聯、每個欄位做什麼
                'image_fields' => config("agent_api.images.{$name}"),      // 這個資源的圖片欄位（'frame'＝imgArr）
            ];
        }
        return response()->json([
            'header' => config('agent_api.header'),
            'note' => 'fields 內的規則就是後台畫面的驗證規則：required＝必填；integer＝整數；nullable＝可留空。img 類欄位請先呼叫 POST /api/agent/upload 取得網址再填入。',
            'upload' => [
                'endpoint' => 'POST /api/agent/upload  (multipart/form-data: file 或 files[]、type、folder、name)',
                'type' => 'files（預設）＝檔案庫 files/1；images＝圖片庫 photos/1（後台 CKEditor「插入圖片」看到的那個，只收 jpg/jpeg/png/webp/gif）。老闆說「圖片庫／CKEditor／插入圖片」＝images；說「檔案庫／下載檔」＝files。回傳的 url 開頭 /storage/photos/1/ 或 /storage/files/1/ 要和老闆說的一致',
                'folder' => '後台檔案管理員 files/1（type=images 時是 photos/1）底下的相對路徑，可多層，例：Clarion 2026/GL-700_Ultra_13/Chinese/Transparent/3840x2159；沒有會自動建立；不填放 ' . config('agent_api.upload.default_folder'),
                'max_kb' => config('agent_api.upload.max_kb'),
                'max_files' => config('agent_api.upload.max_files'),
                'mimes' => config('agent_api.upload.mimes'),
                'response' => '單檔回 {url,path,size}；多檔回 {items:[{name,url,path,size}], errors:[...]}',
            ],
            'file_manager' => [
                'scope' => '需要 files:read（列表／下載／查引用）與 files:write（其餘）',
                'common' => 'type=files（檔案庫 files/1，預設）或 images（圖片庫 photos/1，CKEditor 插入圖片用）；path＝該庫底下的相對路徑（不含 files/1、photos/1）',
                'endpoints' => [
                    'GET /api/agent/files/list?type=&folder=&sort=name|time|size|type&order=asc|desc&q=&page=&per_page=&with_dimensions=1' => '列出資料夾內容（資料夾排前面）；每個檔案有 url、thumb_url、size、modified；相當於後台的縮圖／列表顯示＋排序',
                    'GET /api/agent/files/usage?type=&path=' => '查這個檔案或資料夾在資料庫哪些紀錄還在用（動它之前先查）',
                    'GET /api/agent/files/info?type=&path=' => '預覽（後台檔案管理員的「預覽」）：回單一檔案的網址、縮圖網址、尺寸、大小、格式、有哪些紀錄在用。「確認」是畫面上選取圖片的按鈕，API 不需要，直接把 url 填進欄位即可',
                    'GET /api/agent/files/download?type=&path=' => '下載檔案（回傳檔案本體）',
                    'POST /api/agent/files/folder {type,path}' => '建資料夾（可多層）',
                    'POST /api/agent/files/rename {type,path,new_name,update_references?}' => '改名（檔案或資料夾；檔案不能改副檔名）',
                    'POST /api/agent/files/move {type,path,to_folder,update_references?}' => '搬移到另一個資料夾（to_folder 空字串＝圖庫根目錄）',
                    'POST /api/agent/files/resize {type,path,width?,height?,keep_ratio?,allow_upscale?,save_as?,overwrite?}' => '縮放（預設等比、不放大、另存新檔；overwrite=1 才覆蓋，原檔備份到垃圾桶）',
                    'POST /api/agent/files/crop {type,path,x,y,width,height,save_as?,overwrite?}' => '裁剪（x、y 是左上角像素）；其餘同縮放',
                    'DELETE /api/agent/files {type,path,recursive?,force?}' => '刪除（移到垃圾桶 storage/app/agent_trash/，可由工程師還原；非空資料夾要 recursive=1）',
                ],
                'safety' => [
                    '刪除／搬移／改名前系統會掃全站資料庫，若還有紀錄在用這個網址，一律回 409 並列出是哪些紀錄，不會默默讓前台斷圖。',
                    '搬移／改名確定要做 → 加 update_references=1，系統會把資料庫裡的舊網址換成新網址（含編碼、JSON 跳脫寫法）。',
                    '刪除確定要做 → 加 force=1（前台對應圖片會斷，通常應先把那些紀錄換成別張圖）。',
                    '縮放／裁剪預設另存新檔（檔名加 -寬x高 或 -crop），原檔不動。',
                    '老闆沒明確說要刪／搬／改名，不要自己動；做之前先 usage 再回報給老闆確認。',
                ],
            ],
            'image_input' => config('agent_field_docs.image_input'),
            'common_fields' => config('agent_field_docs.common'),
            'resources' => $out,
            'audit' => 'GET /api/agent/audit?page=1&resource=car_media&record_id=12  每一次寫入的紀錄（含改前快照），改壞了可照 before 退回',
        ]);
    }

    /**
     * 稽核紀錄：這把金鑰做過的寫入（POST／PATCH／DELETE），最新在前。
     * 可用 ?resource=car_media、?record_id=12 篩選。* 權限的金鑰看得到所有金鑰的紀錄。
     */
    public function audit(Request $request)
    {
        $key = $request->attributes->get('agent_key');
        $q = AgentAuditLogModel::orderByDesc('id');
        if (!$key->can('*')) {
            $q->where('agent_key_id', $key->id);
        }
        if ($request->filled('resource')) $q->where('resource', $request->input('resource'));
        if ($request->filled('record_id')) $q->where('record_id', (string) $request->input('record_id'));
        return response()->json([
            'items' => $q->paginate((int) config('agent_api.audit_per_page', 50)),
        ]);
    }

    private function endpointsFor(string $name, array $cfg, string $style): array
    {
        $b = "/api/agent/{$name}";
        if ($style === 'single') {
            return ["GET {$b}/all", "PATCH {$b}"];
        }
        if ($style === 'kv') {
            return ["GET {$b}/all", "PATCH {$b}/" . $cfg['key']];
        }
        if ($style === 'watermark') {
            return ["GET {$b}/all", "POST {$b}/{key}  (multipart: file=PNG)", "DELETE {$b}/{key}  (還原預設)"];
        }
        $e = ["GET {$b}/all?page=1", "POST {$b}", "GET {$b}/{id}", "PATCH {$b}/{id}", "DELETE {$b}/{id}", "PATCH {$b}/{id}/status", "PATCH {$b}/all/sort  (body: items=[{id,sort},...])"];
        foreach (($cfg['actions'] ?? []) as $k => $v) {
            $seg = is_int($k) ? $v : $k;
            $e[] = "PATCH {$b}/{id}/{$seg}";
        }
        foreach (($cfg['extra_get'] ?? []) as $g) $e[] = "GET {$b}/{$g}";
        return $e;
    }

    /**
     * 通知「重新產生前台」：前台目前為即時讀取，不需要此動作；
     * 只有改成靜態 generate 部署時，才需在 .env 設定 AGENT_DEPLOY_HOOK_URL（CI／主機 webhook 網址），沒設定回 200 並標示 needed=false。
     * 每分鐘最多觸發一次，避免連續寫入時重複建置。
     */
    public function publish(Request $request)
    {
        $hook = config('agent_api.deploy_hook');
        if (!$hook) {
            // 前台是即時讀取的伺服器（2026-10-01 實測：後台寫入後前台立即變），不需要重新產生。
            return response()->json([
                'message' => '前台為即時讀取，後台資料寫入後立即生效，不需要重新產生。',
                'needed' => false,
                'configured' => false,
            ], 200);
        }
        $lock = 'agent_publish_lock';
        if (\Illuminate\Support\Facades\Cache::has($lock)) {
            return response()->json(['message' => '一分鐘內已經觸發過，請稍後再試（避免重複建置）', 'triggered' => false], 429);
        }
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(15)->post($hook, [
                'source' => 'agent',
                'key' => optional($request->attributes->get('agent_key'))->name,
                'time' => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => '觸發失敗：' . $e->getMessage(), 'triggered' => false], 502);
        }
        if (!$res->successful()) {
            return response()->json(['message' => '觸發網址回應 HTTP ' . $res->status(), 'triggered' => false], 502);
        }
        \Illuminate\Support\Facades\Cache::put($lock, 1, 60);
        return response()->json(['message' => '已通知重新產生前台（通常數分鐘後生效）', 'triggered' => true]);
    }

    public function upload(Request $request)
    {
        $cfg = config('agent_api.upload');
        // type：files（預設）＝檔案庫 files/1；images＝圖片庫 photos/1（CKEditor 插入圖片看到的那個）
        $type = strtolower(trim((string) $request->input('type', 'files'))) ?: 'files';
        if (!in_array($type, ['files', 'images'], true)) {
            return response()->json(['message' => 'type 只能是 files（檔案庫 files/1）或 images（圖片庫 photos/1）'], 422);
        }
        $isImages = $type === 'images';
        $mimes = $isImages ? ($cfg['mimes_images'] ?? 'jpg,jpeg,png,webp,gif') : $cfg['mimes'];
        $fileRule = 'file|max:' . $cfg['max_kb'] . '|mimes:' . $mimes;
        $request->validate([
            'file' => 'required_without:files|' . $fileRule,
            'files' => 'required_without:file|array|max:' . $cfg['max_files'],
            'files.*' => $fileRule,
            'folder' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:80',   // 只有單檔時有用；多檔一律沿用原檔名
        ]);

        // folder：files/1 底下的相對路徑，可多層。擋掉 ..、開頭的 /、反斜線與控制字元，其餘（含空格、中文）照檔案管理員的規則保留
        $folder = trim((string) $request->input('folder', ''), " /\\");
        if ($folder === '') $folder = $cfg['default_folder'];
        if (preg_match('#(^|/)\.\.?(/|$)#', $folder) || preg_match('#[\\\\\x00-\x1f]#', $folder)) {
            return response()->json(['message' => '資料夾路徑不合法（不能含 ..、反斜線）'], 422);
        }
        $base = $isImages ? ($cfg['base_images'] ?? 'photos/1') : $cfg['base'];
        $dir = rtrim($base, '/') . '/' . $folder;
        $disk = Storage::disk($cfg['disk']);
        $disk->makeDirectory($dir);

        $single = $request->hasFile('file');
        $list = $single ? [$request->file('file')] : $request->file('files');
        $items = [];
        $errors = [];
        foreach ($list as $file) {
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $orig = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // 檔名：單檔可用 name 指定；否則沿用原檔名（保留中文與底線，去掉路徑符號），同名就加流水號，不再硬加時間戳
            $base = ($single && $request->filled('name')) ? $request->input('name') : $orig;
            $base = trim(preg_replace('#[\\\\/:*?"<>|\x00-\x1f]+#', '_', $base)) ?: 'file';
            $filename = $base . '.' . $ext;
            $n = 1;
            while ($disk->exists($dir . '/' . $filename)) {
                $filename = $base . '-' . (++$n) . '.' . $ext;
            }
            try {
                $path = $file->storeAs($dir, $filename, $cfg['disk']);
                $items[] = [
                    'name' => $filename,
                    'url' => $disk->url($path),   // 與後台檔案管理員同一種網址格式：{APP_URL}/storage/files/1/<folder>/<file>
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            } catch (\Throwable $e) {
                $errors[] = ['name' => $file->getClientOriginalName(), 'message' => $e->getMessage()];
            }
        }

        if ($single) {
            if (!$items) return response()->json(['message' => '上傳失敗', 'errors' => $errors], 500);
            return response()->json(['message' => '上傳成功', 'type' => $type] + $items[0]);
        }
        return response()->json([
            'message' => count($items) . ' 個上傳成功' . ($errors ? '、' . count($errors) . ' 個失敗' : ''),
            'type' => $type,
            'folder' => $folder,
            'items' => $items,
            'errors' => $errors,
        ], $items ? 200 : 500);
    }
}

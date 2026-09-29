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
            ];
        }
        return response()->json([
            'header' => config('agent_api.header'),
            'note' => 'fields 內的規則就是後台畫面的驗證規則：required＝必填；integer＝整數；nullable＝可留空。img 類欄位請先呼叫 POST /api/agent/upload 取得網址再填入。',
            'upload' => [
                'endpoint' => 'POST /api/agent/upload  (multipart/form-data: file 或 files[]、folder、name)',
                'folder' => '後台檔案管理員 files/1 底下的相對路徑，可多層，例：Clarion 2026/GL-700_Ultra_13/Chinese/Transparent/3840x2159；沒有會自動建立；不填放 ' . config('agent_api.upload.default_folder'),
                'max_kb' => config('agent_api.upload.max_kb'),
                'max_files' => config('agent_api.upload.max_files'),
                'mimes' => config('agent_api.upload.mimes'),
                'response' => '單檔回 {url,path,size}；多檔回 {items:[{name,url,path,size}], errors:[...]}',
            ],
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
        $e = ["GET {$b}/all?page=1", "POST {$b}", "GET {$b}/{id}", "PATCH {$b}/{id}", "DELETE {$b}/{id}", "PATCH {$b}/{id}/status", "PATCH {$b}/all/sort  (body: items=[{id,sort},...])"];
        foreach (($cfg['actions'] ?? []) as $k => $v) {
            $seg = is_int($k) ? $v : $k;
            $e[] = "PATCH {$b}/{id}/{$seg}";
        }
        foreach (($cfg['extra_get'] ?? []) as $g) $e[] = "GET {$b}/{$g}";
        return $e;
    }

    public function upload(Request $request)
    {
        $cfg = config('agent_api.upload');
        $fileRule = 'file|max:' . $cfg['max_kb'] . '|mimes:' . $cfg['mimes'];
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
        $dir = rtrim($cfg['base'], '/') . '/' . $folder;
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
            return response()->json(['message' => '上傳成功'] + $items[0]);
        }
        return response()->json([
            'message' => count($items) . ' 個上傳成功' . ($errors ? '、' . count($errors) . ' 個失敗' : ''),
            'folder' => $folder,
            'items' => $items,
            'errors' => $errors,
        ], $items ? 200 : 500);
    }
}

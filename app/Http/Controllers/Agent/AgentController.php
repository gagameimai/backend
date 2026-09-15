<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
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
                'fields' => $rules,
                'field_labels' => $labels,
            ];
        }
        return response()->json([
            'header' => config('agent_api.header'),
            'note' => 'fields 內的規則就是後台畫面的驗證規則：required＝必填；integer＝整數；nullable＝可留空。img 類欄位請先呼叫 POST /api/agent/upload 取得網址再填入。',
            'upload' => [
                'endpoint' => 'POST /api/agent/upload  (multipart/form-data: file, folder)',
                'folders' => config('agent_api.upload.folders'),
                'max_kb' => config('agent_api.upload.max_kb'),
                'mimes' => config('agent_api.upload.mimes'),
            ],
            'resources' => $out,
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
        $request->validate([
            'file' => 'required|file|max:' . $cfg['max_kb'] . '|mimes:' . $cfg['mimes'],
            'folder' => 'nullable|string',
            'name' => 'nullable|string|max:80',
        ]);

        $folder = $request->input('folder') ?: 'Agent';
        if (!in_array($folder, $cfg['folders'], true)) {
            return response()->json(['message' => '不允許的資料夾，可用：' . implode(', ', $cfg['folders'])], 422);
        }

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $base = $request->input('name') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $base = Str::slug(Str::ascii($base)) ?: 'file';
        $filename = $base . '-' . date('YmdHis') . '-' . Str::lower(Str::random(4)) . '.' . $ext;

        $dir = rtrim($cfg['base'], '/') . '/' . $folder;
        $path = $file->storeAs($dir, $filename, $cfg['disk']);
        $url = Storage::disk($cfg['disk'])->url($path); // 與後台檔案管理員同一種網址格式：{APP_URL}/storage/files/1/<folder>/<file>

        return response()->json([
            'message' => '上傳成功',
            'url' => $url,
            'path' => $path,
            'size' => $file->getSize(),
        ]);
    }
}

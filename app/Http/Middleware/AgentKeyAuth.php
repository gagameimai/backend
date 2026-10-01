<?php

namespace App\Http\Middleware;

use App\Models\AgentAuditLogModel;
use App\Models\AgentKeyModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Agent API 金鑰驗證 + 權限範圍 + 稽核。
 *
 * 用法：Route::middleware('agent.key:products')
 *   GET            → 需要 products:read
 *   POST/PATCH/DELETE → 需要 products:write
 *
 * 金鑰放在 header：X-Agent-Key: mmk_xxxx   （或 Authorization: Bearer mmk_xxxx）
 */
class AgentKeyAuth
{
    public function handle(Request $request, Closure $next, ?string $scopeGroup = null)
    {
        // 一律當 JSON 請求：沒帶 Accept 時，$request->validate() 失敗會變成 302 轉址（給瀏覽器用的行為），agent 看不懂
        $request->headers->set('Accept', 'application/json');

        $plain = $request->header(config('agent_api.header', 'X-Agent-Key')) ?: $request->bearerToken();
        $key = AgentKeyModel::findByPlain($plain);
        if (!$key) {
            return response()->json(['message' => '金鑰無效或已停用'], 401);
        }

        $isWrite = !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
        if ($scopeGroup) {
            $need = $scopeGroup . ':' . ($isWrite ? 'write' : 'read');
            if (!$key->can($need)) {
                return response()->json(['message' => "這把金鑰沒有 {$need} 權限"], 403);
            }
        }

        // 每分鐘上限（每把金鑰）
        $limit = (int) config('agent_api.rate_limit_per_minute', 120);
        $bucket = 'agent_rl:' . $key->id . ':' . now()->format('YmdHi');
        $count = Cache::increment($bucket);
        if ($count === 1) Cache::put($bucket, 1, 70);
        if ($count > $limit) {
            return response()->json(['message' => "超過每分鐘 {$limit} 次上限，請稍後再試"], 429);
        }

        $request->attributes->set('agent_key', $key);

        // 寫入動作先拍「改前快照」
        $before = null;
        [$resource, $recordId] = $this->resolveResource($request);
        if ($isWrite && $recordId !== null && $resource) {
            $modelClass = config("agent_api.resources.{$resource}.model");
            if ($modelClass && class_exists($modelClass)) {
                $row = $modelClass::find($recordId);
                $before = $row ? $row->toArray() : null;
            }
        }

        // 寫入前：圖片欄位統一、PATCH 沒送的欄位沿用現有值（所有資源共用，見 App\Support\AgentWriteHelper）
        if ($isWrite && $resource && $request->method() !== 'DELETE') {
            $segments = $request->segments();
            $i = array_search('agent', $segments, true);
            $prefix = config("agent_api.resources.{$resource}.prefix");
            $offset = ($prefix && strpos($prefix, '{') !== false) ? 3 : 2;
            $isMainUpdate = $request->method() === 'PATCH' && $recordId !== null && !isset($segments[$i + $offset + 1]);
            $action = $isMainUpdate ? null : ($segments[$i + $offset + 1] ?? null);
            $maxBefore = $request->method() === 'POST' ? \App\Support\AgentWriteHelper::maxId($resource) : null;
            $blocked = \App\Support\AgentWriteHelper::prepare($request, $resource, $recordId, $isMainUpdate, $action);
            if ($blocked) {
                AgentAuditLogModel::create([
                    'agent_key_id' => $key->id, 'method' => $request->method(),
                    'path' => '/' . ltrim($request->path(), '/'), 'resource' => $resource, 'record_id' => $recordId,
                    'payload' => $request->except(['file', 'files']), 'before' => $before,
                    'response_status' => $blocked->getStatusCode(), 'ip' => $request->ip(),
                ]);
                return $blocked;
            }
        }

        $response = $next($request);

        if ($isWrite && $resource && $request->method() !== 'DELETE') {
            $response = \App\Support\AgentWriteHelper::finish($response, $request, $resource, $recordId, $maxBefore ?? null);
        }

        $key->forceFill(['last_used_at' => now()])->saveQuietly();

        if ($isWrite) {
            $payload = $request->except(['file', 'files']);
            AgentAuditLogModel::create([
                'agent_key_id' => $key->id,
                'method' => $request->method(),
                'path' => '/' . ltrim($request->path(), '/'),
                'resource' => $resource,
                'record_id' => $recordId,
                'payload' => $payload,
                'before' => $before,
                'response_status' => $response->getStatusCode(),
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }

    /**
     * 從路徑推出資源名稱與 id：/api/agent/car_media/12/status → ['car_media', '12']
     */
    private function resolveResource(Request $request): array
    {
        $segments = $request->segments(); // ['api','agent','car_media','12','status']
        $i = array_search('agent', $segments, true);
        if ($i === false || !isset($segments[$i + 1])) return [null, null];
        $resource = $segments[$i + 1];
        // 巢狀資源（例：car_blind_spot_format/{car_blind_spot}/{id}）多一層父層參數，id 往後一格
        $prefix = config("agent_api.resources.{$resource}.prefix");
        $offset = ($prefix && strpos($prefix, '{') !== false) ? 3 : 2;
        $id = $segments[$i + $offset] ?? null;
        if ($id !== null && !ctype_digit((string) $id)) $id = null; // 'all'、'{page_key}' 之類不是 id
        return [$resource, $id];
    }
}

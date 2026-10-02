<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * Agent API：圖片庫／檔案庫管理（等同後台檔案管理員的 重新命名、下載、搬移、縮放、裁剪、刪除、列表、排序）。
 * type=files → files/1（檔案庫）；type=images → photos/1（圖片庫，CKEditor 插入圖片用）。
 * 安全設計：
 *  - 刪除／搬移／改名之前，先掃全站資料庫有沒有地方還在用這個網址；有的話預設拒絕（409），
 *    搬移／改名可帶 update_references=1 讓系統一併把資料庫裡的舊網址換成新網址，刪除要帶 force=1 才會硬刪。
 *  - 刪除不是真的刪：檔案移到 storage/app/agent_trash/<時間>/…（不在公開網址底下），工程師可手動還原。
 *  - 縮放、裁剪預設另存新檔；overwrite=1 才覆蓋原檔，且原檔會先備份到 agent_trash。
 */
class AgentFileController extends Controller
{
    private const SKIP_TABLES = ['agent_audit_logs', 'agent_keys', 'sessions', 'migrations', 'failed_jobs', 'jobs', 'password_resets', 'personal_access_tokens'];
    private const IMG_EXT = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    private function disk()
    {
        return Storage::disk(config('agent_api.upload.disk', 'public'));
    }

    /** 回傳 [base, error]。base 例：files/1 或 photos/1 */
    private function base(Request $request): array
    {
        $type = strtolower(trim((string) $request->input('type', 'files'))) ?: 'files';
        $cfg = config('agent_api.upload');
        if ($type === 'files') return [rtrim($cfg['base'], '/'), $type, null];
        if ($type === 'images') return [rtrim($cfg['base_images'] ?? 'photos/1', '/'), $type, null];
        return [null, $type, response()->json(['message' => 'type 只能是 files（檔案庫 files/1）或 images（圖片庫 photos/1）'], 422)];
    }

    /** 清理使用者給的相對路徑：不能有 ..、反斜線、控制字元、thumbs 資料夾。空字串＝庫的根目錄 */
    private function cleanRel($v, bool &$ok = null): string
    {
        $ok = true;
        $v = trim((string) $v, " /\\");
        if ($v === '') return '';
        if (preg_match('#(^|/)\.\.?(/|$)#', $v) || preg_match('#[\\\\\x00-\x1f]#', $v) || preg_match('#(^|/)thumbs(/|$)#i', $v)) {
            $ok = false;
            return '';
        }
        return preg_replace('#/+#', '/', $v);
    }

    private function cleanName(string $n): string
    {
        return trim(preg_replace('#[\\\\/:*?"<>|\x00-\x1f]+#', '_', $n));
    }

    private function bad(string $m, int $code = 422)
    {
        return response()->json(['message' => $m], $code);
    }

    private function url(string $rel): string
    {
        return $this->disk()->url($rel);
    }

    // ───────────────────────── 資料庫引用檢查／改寫 ─────────────────────────

    /** 一個站內路徑（例 files/1/A B/x.png）在資料庫裡可能出現的寫法（固定順序，新舊路徑可逐一對應）：
     *  原樣、網址編碼，以及各自的 JSON 跳脫（\/ 與 \uXXXX 的有無組合）。 */
    private function forms(string $rel, bool $isDir): array
    {
        $needle = 'storage/' . $rel . ($isDir ? '/' : '');
        $enc = implode('/', array_map('rawurlencode', explode('/', $needle)));
        $out = [$needle, $enc];
        foreach ([$needle, $enc] as $s) {
            foreach ([0, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES] as $flags) {
                $out[] = substr(json_encode($s, $flags), 1, -1);
            }
        }
        return $out;
    }

    private function variants(string $rel, bool $isDir): array
    {
        return array_values(array_unique(array_filter($this->forms($rel, $isDir), 'strlen')));
    }

    /** 對應表：把「舊路徑的每種寫法」換成「新路徑同一種寫法」 */
    private function variantPairs(string $oldRel, string $newRel, bool $isDir): array
    {
        $a = $this->forms($oldRel, $isDir);
        $b = $this->forms($newRel, $isDir);
        $pairs = [];
        foreach ($a as $i => $from) {
            if ($from !== '' && !isset($pairs[$from]) && $from !== $b[$i]) $pairs[$from] = $b[$i];
        }
        return $pairs;
    }

    private function textColumns(): array
    {
        $db = DB::getDatabaseName();
        $rows = DB::select(
            "SELECT TABLE_NAME t, COLUMN_NAME c FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext','longtext','json')",
            [$db]
        );
        $out = [];
        foreach ($rows as $r) {
            if (in_array($r->t, self::SKIP_TABLES, true)) continue;
            $out[] = [$r->t, $r->c];
        }
        return $out;
    }

    private function like(string $s): string
    {
        return '%' . str_replace(['|', '%', '_'], ['||', '|%', '|_'], $s) . '%';
    }

    /** 回傳 [{table,column,count,ids:[…最多 10 個]}] */
    private function findReferences(string $rel, bool $isDir): array
    {
        $vars = $this->variants($rel, $isDir);
        $refs = [];
        foreach ($this->textColumns() as [$t, $c]) {
            $where = [];
            $bind = [];
            foreach ($vars as $v) {
                $where[] = "`{$c}` LIKE ? ESCAPE '|'";
                $bind[] = $this->like($v);
            }
            $cond = '(' . implode(' OR ', $where) . ')';
            try {
                $count = (int) DB::table($t)->whereRaw($cond, $bind)->count();
            } catch (\Throwable $e) {
                continue;
            }
            if ($count > 0) {
                $ids = [];
                try {
                    $ids = DB::table($t)->whereRaw($cond, $bind)->limit(10)->pluck('id')->all();
                } catch (\Throwable $e) {
                }
                $refs[] = ['table' => $t, 'column' => $c, 'count' => $count, 'ids' => $ids];
            }
        }
        return $refs;
    }

    private function rewriteReferences(string $oldRel, string $newRel, bool $isDir): int
    {
        $pairs = $this->variantPairs($oldRel, $newRel, $isDir);
        $total = 0;
        DB::transaction(function () use ($pairs, &$total) {
            foreach ($this->textColumns() as [$t, $c]) {
                foreach ($pairs as $from => $to) {
                    $total += DB::update(
                        "UPDATE `{$t}` SET `{$c}` = REPLACE(`{$c}`, ?, ?) WHERE `{$c}` LIKE ? ESCAPE '|'",
                        [$from, $to, $this->like($from)]
                    );
                }
            }
        });
        return $total;
    }

    private function refsResponse(array $refs, string $hint)
    {
        return response()->json([
            'message' => '這個' . '檔案／資料夾在資料庫裡還有 ' . array_sum(array_column($refs, 'count')) . ' 處在使用，直接動會讓前台斷圖。' . $hint,
            'references' => $refs,
        ], 409);
    }

    // ───────────────────────── 列表／用量 ─────────────────────────

    public function list(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $folder = $this->cleanRel($request->input('folder', ''), $ok);
        if (!$ok) return $this->bad('資料夾路徑不合法');
        $dir = $base . ($folder !== '' ? '/' . $folder : '');
        $disk = $this->disk();
        if (!$disk->exists($dir)) return $this->bad('資料夾不存在：' . ($folder ?: '/'), 404);

        $q = trim((string) $request->input('q', ''));
        $items = [];
        foreach ($disk->directories($dir) as $d) {
            $n = basename($d);
            if (strtolower($n) === 'thumbs') continue;
            if ($q !== '' && mb_stripos($n, $q) === false) continue;
            $items[] = ['name' => $n, 'kind' => 'folder', 'path' => $this->relFromBase($d, $base), 'size' => 0, 'modified' => date('c', $disk->lastModified($d)), '_t' => $disk->lastModified($d)];
        }
        foreach ($disk->files($dir) as $f) {
            $n = basename($f);
            if ($q !== '' && mb_stripos($n, $q) === false) continue;
            $ext = strtolower(pathinfo($n, PATHINFO_EXTENSION));
            $it = ['name' => $n, 'kind' => 'file', 'path' => $this->relFromBase($f, $base), 'url' => $this->url($f), 'size' => $disk->size($f),
                'modified' => date('c', $disk->lastModified($f)), 'is_image' => in_array($ext, self::IMG_EXT, true), '_t' => $disk->lastModified($f)];
            $thumb = $dir . '/thumbs/' . $n;
            $it['thumb_url'] = $disk->exists($thumb) ? $this->url($thumb) : ($it['is_image'] ? $it['url'] : null);
            if ($it['is_image'] && $request->boolean('with_dimensions')) {
                $sz = @getimagesize($disk->path($f));
                if ($sz) { $it['width'] = $sz[0]; $it['height'] = $sz[1]; }
            }
            $items[] = $it;
        }

        $sort = in_array($request->input('sort'), ['name', 'time', 'size', 'type'], true) ? $request->input('sort') : 'name';
        $desc = strtolower((string) $request->input('order', 'asc')) === 'desc';
        usort($items, function ($a, $b) use ($sort, $desc) {
            // 資料夾永遠排在檔案前面（和檔案管理員一樣）
            if ($a['kind'] !== $b['kind']) return $a['kind'] === 'folder' ? -1 : 1;
            switch ($sort) {
                case 'time': $r = $a['_t'] <=> $b['_t']; break;
                case 'size': $r = $a['size'] <=> $b['size']; break;
                case 'type': $r = strcmp(pathinfo($a['name'], PATHINFO_EXTENSION), pathinfo($b['name'], PATHINFO_EXTENSION)) ?: strnatcasecmp($a['name'], $b['name']); break;
                default: $r = strnatcasecmp($a['name'], $b['name']);
            }
            return $desc ? -$r : $r;
        });
        foreach ($items as &$i) unset($i['_t']);
        unset($i);

        $per = max(1, min(500, (int) $request->input('per_page', 100)));
        $page = max(1, (int) $request->input('page', 1));
        $total = count($items);
        return response()->json([
            'type' => $type, 'folder' => $folder, 'sort' => $sort, 'order' => $desc ? 'desc' : 'asc',
            'total' => $total, 'page' => $page, 'per_page' => $per, 'last_page' => (int) max(1, ceil($total / $per)),
            'items' => array_slice($items, ($page - 1) * $per, $per),
        ]);
    }

    private function relFromBase(string $full, string $base): string
    {
        return ltrim(substr($full, strlen($base)), '/');
    }

    public function usage(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $full = $base . '/' . $rel;
        $disk = $this->disk();
        $isDir = is_dir($disk->path($full));
        if (!$isDir && !$disk->exists($full)) return $this->bad('找不到：' . $rel, 404);
        $refs = $this->findReferences($full, $isDir);
        return response()->json(['type' => $type, 'path' => $rel, 'kind' => $isDir ? 'folder' : 'file', 'in_use' => count($refs) > 0, 'references' => $refs]);
    }

    // ───────────────────────── 預覽（檔案管理員的「預覽」） ─────────────────────────

    public function info(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $full = $base . '/' . $rel;
        $disk = $this->disk();
        if (!$disk->exists($full) || is_dir($disk->path($full))) return $this->bad('找不到檔案：' . $rel, 404);
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $out = ['type' => $type, 'path' => $rel, 'name' => basename($full), 'url' => $this->url($full), 'size' => $disk->size($full),
            'modified' => date('c', $disk->lastModified($full)), 'mime' => $disk->mimeType($full), 'is_image' => in_array($ext, self::IMG_EXT, true)];
        $t = dirname($full) . '/thumbs/' . basename($full);
        $out['thumb_url'] = $disk->exists($t) ? $this->url($t) : ($out['is_image'] ? $out['url'] : null);
        if ($out['is_image'] && ($sz = @getimagesize($disk->path($full)))) { $out['width'] = $sz[0]; $out['height'] = $sz[1]; }
        $refs = $this->findReferences($full, false);
        $out['in_use'] = count($refs) > 0;
        $out['references'] = $refs;
        return response()->json($out);
    }

    // ───────────────────────── 下載 ─────────────────────────

    public function download(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $full = $base . '/' . $rel;
        $disk = $this->disk();
        if (!$disk->exists($full)) return $this->bad('找不到檔案：' . $rel, 404);
        return response()->download($disk->path($full), basename($full));
    }

    // ───────────────────────── 建資料夾 ─────────────────────────

    public function makeFolder(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $disk = $this->disk();
        $full = $base . '/' . $rel;
        if ($disk->exists($full)) return $this->bad('已經存在：' . $rel, 409);
        $disk->makeDirectory($full);
        return response()->json(['message' => '資料夾已建立', 'type' => $type, 'path' => $rel]);
    }

    // ───────────────────────── 改名／搬移 ─────────────────────────

    public function rename(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $new = $this->cleanName((string) $request->input('new_name', ''));
        if ($new === '' || $new === '.' || $new === '..' || strtolower($new) === 'thumbs') return $this->bad('new_name 必填且不能是 thumbs');
        $parent = dirname($rel) === '.' ? '' : dirname($rel) . '/';
        return $this->doMove($request, $base, $type, $rel, $parent . $new, 'rename');
    }

    public function move(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線');
        $to = $this->cleanRel($request->input('to_folder', ''), $ok);
        if (!$ok) return $this->bad('to_folder 路徑不合法');
        $dest = ($to !== '' ? $to . '/' : '') . basename($rel);
        return $this->doMove($request, $base, $type, $rel, $dest, 'move');
    }

    private function doMove(Request $request, string $base, string $type, string $rel, string $destRel, string $op)
    {
        $disk = $this->disk();
        $from = $base . '/' . $rel;
        $to = $base . '/' . $destRel;
        $isDir = is_dir($disk->path($from));
        if (!$isDir && !$disk->exists($from)) return $this->bad('找不到：' . $rel, 404);
        if ($from === $to) return $this->bad('新舊位置相同，沒有變動');
        if ($isDir && strpos($to . '/', $from . '/') === 0) return $this->bad('不能把資料夾搬進它自己裡面');
        if (!$isDir) {
            $e1 = strtolower(pathinfo($from, PATHINFO_EXTENSION));
            $e2 = strtolower(pathinfo($to, PATHINFO_EXTENSION));
            if ($e1 !== $e2) return $this->bad('不能改副檔名（' . $e1 . ' → ' . $e2 . '）；改名時請保留原副檔名');
        }
        if ($disk->exists($to)) return $this->bad('目的地已經有同名項目：' . $destRel, 409);

        $update = $request->boolean('update_references');
        $refs = $this->findReferences($from, $isDir);
        if ($refs && !$update) {
            return $this->refsResponse($refs, '若確定要動，請加 update_references=1，系統會一併把資料庫裡的舊網址換成新網址；或先改用 GET /api/agent/files/usage 看是哪些紀錄。');
        }

        $disk->makeDirectory(dirname($to));
        $disk->move($from, $to);
        if (!$isDir) {   // 縮圖一起搬
            $t1 = dirname($from) . '/thumbs/' . basename($from);
            if ($disk->exists($t1)) {
                $disk->makeDirectory(dirname($to) . '/thumbs');
                $t2 = dirname($to) . '/thumbs/' . basename($to);
                if (!$disk->exists($t2)) $disk->move($t1, $t2);
            }
        }
        $rewritten = 0;
        if ($refs) $rewritten = $this->rewriteReferences($from, $to, $isDir);

        return response()->json([
            'message' => ($op === 'rename' ? '已改名' : '已搬移') . ($refs ? "，並更新了資料庫裡 {$rewritten} 個欄位的網址" : ''),
            'type' => $type, 'kind' => $isDir ? 'folder' : 'file',
            'old_path' => $rel, 'path' => $destRel, 'url' => $isDir ? null : $this->url($to),
            'references_updated' => $rewritten,
        ]);
    }

    // ───────────────────────── 刪除（進垃圾桶，不是真的刪） ─────────────────────────

    public function delete(Request $request)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return $err;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') return $this->bad('path 必填且不能含 ..、反斜線（不能刪整個圖庫根目錄）');
        $disk = $this->disk();
        $full = $base . '/' . $rel;
        $isDir = is_dir($disk->path($full));
        if (!$isDir && !$disk->exists($full)) return $this->bad('找不到：' . $rel, 404);
        if ($isDir && !$request->boolean('recursive')) {
            $has = array_filter(array_merge($disk->files($full), $disk->directories($full)), function ($p) { return basename($p) !== 'thumbs'; });
            if ($has) return $this->bad('資料夾不是空的（有 ' . count($has) . ' 個項目）。要整個刪請加 recursive=1', 409);
        }
        $refs = $this->findReferences($full, $isDir);
        if ($refs && !$request->boolean('force')) {
            return $this->refsResponse($refs, '建議先把那些紀錄換成別張圖；若確定仍要刪（前台會斷圖），請加 force=1。');
        }

        $trashRel = 'agent_trash/' . date('Ymd-His') . '-' . substr(uniqid(), -5) . '/' . $type . '/' . $rel;
        $trashAbs = storage_path('app/' . $trashRel);
        File::makeDirectory(dirname($trashAbs), 0755, true, true);
        if ($isDir) File::moveDirectory($disk->path($full), $trashAbs); else File::move($disk->path($full), $trashAbs);
        if (!$isDir) {
            $t = dirname($full) . '/thumbs/' . basename($full);
            if ($disk->exists($t)) $disk->delete($t);
        }
        return response()->json([
            'message' => '已刪除（移到垃圾桶，可由工程師還原）',
            'type' => $type, 'kind' => $isDir ? 'folder' : 'file', 'path' => $rel,
            'trash_path' => 'storage/app/' . $trashRel,
            'had_references' => count($refs),
        ]);
    }

    // ───────────────────────── 縮放／裁剪 ─────────────────────────

    private function loadImage(Request $request, &$base, &$type, &$rel, &$err)
    {
        [$base, $type, $err] = $this->base($request);
        if ($err) return null;
        $ok = true;
        $rel = $this->cleanRel($request->input('path', ''), $ok);
        if (!$ok || $rel === '') { $err = $this->bad('path 必填且不能含 ..、反斜線'); return null; }
        $full = $base . '/' . $rel;
        $disk = $this->disk();
        if (!$disk->exists($full)) { $err = $this->bad('找不到檔案：' . $rel, 404); return null; }
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        if (!in_array($ext, self::IMG_EXT, true)) { $err = $this->bad('只能處理 jpg／jpeg／png／webp／gif'); return null; }
        return [$full, $ext];
    }

    private function saveResult(Request $request, string $base, string $full, string $ext, $img, string $suffix, string $type, string $rel)
    {
        $disk = $this->disk();
        $overwrite = $request->boolean('overwrite');
        $dir = dirname($full);
        if ($overwrite) {
            $target = $full;
            $trashRel = 'agent_trash/' . date('Ymd-His') . '-' . substr(uniqid(), -5) . '/' . $type . '/' . $rel;
            $trashAbs = storage_path('app/' . $trashRel);
            File::makeDirectory(dirname($trashAbs), 0755, true, true);
            File::copy($disk->path($full), $trashAbs);   // 原檔先備份
        } else {
            $name = $request->filled('save_as') ? $this->cleanName((string) $request->input('save_as')) : pathinfo($full, PATHINFO_FILENAME) . $suffix;
            $name = preg_replace('#\.(jpe?g|png|webp|gif)$#i', '', $name);
            $target = $dir . '/' . $name . '.' . $ext;
            $n = 1;
            while ($disk->exists($target)) $target = $dir . '/' . $name . '-' . (++$n) . '.' . $ext;
        }
        $img->save($disk->path($target), 90);
        $t = $dir . '/thumbs/' . basename($target);
        if ($overwrite && $disk->exists($t)) $disk->delete($t);   // 舊縮圖作廢，讓檔案管理員重建
        $sz = @getimagesize($disk->path($target));
        return response()->json([
            'message' => $overwrite ? '已覆蓋原檔（原檔備份在 ' . 'storage/app/' . $trashRel . '）' : '已另存新檔，原檔不動',
            'type' => $type, 'path' => $this->relFromBase($target, $base),
            'url' => $this->url($target), 'width' => $sz[0] ?? null, 'height' => $sz[1] ?? null, 'size' => $disk->size($target),
        ]);
    }

    public function resize(Request $request)
    {
        $request->validate(['width' => 'nullable|integer|min:1|max:8000', 'height' => 'nullable|integer|min:1|max:8000']);
        if (!$request->filled('width') && !$request->filled('height')) return $this->bad('width、height 至少要給一個');
        $r = $this->loadImage($request, $base, $type, $rel, $err);
        if (!$r) return $err;
        [$full, $ext] = $r;
        $disk = $this->disk();
        $manager = new ImageManager(['driver' => 'gd']);
        $img = $manager->make($disk->path($full))->orientate();
        $w = $request->filled('width') ? (int) $request->input('width') : null;
        $h = $request->filled('height') ? (int) $request->input('height') : null;
        $keep = $request->has('keep_ratio') ? $request->boolean('keep_ratio') : true;
        $up = $request->boolean('allow_upscale');
        $img->resize($w, $h, function ($c) use ($keep, $up) {
            if ($keep) $c->aspectRatio();
            if (!$up) $c->upsize();
        });
        return $this->saveResult($request, $base, $full, $ext, $img, '-' . $img->width() . 'x' . $img->height(), $type, $rel);
    }

    public function crop(Request $request)
    {
        $request->validate(['x' => 'required|integer|min:0', 'y' => 'required|integer|min:0', 'width' => 'required|integer|min:1|max:8000', 'height' => 'required|integer|min:1|max:8000']);
        $r = $this->loadImage($request, $base, $type, $rel, $err);
        if (!$r) return $err;
        [$full, $ext] = $r;
        $disk = $this->disk();
        $manager = new ImageManager(['driver' => 'gd']);
        $img = $manager->make($disk->path($full))->orientate();
        $x = (int) $request->input('x'); $y = (int) $request->input('y');
        $w = (int) $request->input('width'); $h = (int) $request->input('height');
        if ($x + $w > $img->width() || $y + $h > $img->height()) {
            return $this->bad("裁剪範圍超出圖片（圖片 {$img->width()}×{$img->height()}，要求 x={$x} y={$y} 寬{$w} 高{$h}）");
        }
        $img->crop($w, $h, $x, $y);
        return $this->saveResult($request, $base, $full, $ext, $img, '-crop', $type, $rel);
    }
}

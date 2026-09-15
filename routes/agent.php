<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Agent API（/api/agent/...）
|--------------------------------------------------------------------------
| 給業主自己的 AI agent 用的「快速通道」：不用開後台畫面，直接用 HTTP 改資料。
| 全部指向「既有的 Admin 控制器」，所以驗證、欄位、限制與後台按鈕一模一樣，沒有第二套邏輯。
| 驗證與權限見 App\Http\Middleware\AgentKeyAuth；資源清單見 config/agent_api.php。
|
| 由 routes/api.php 引入，已在 'api' middleware group 內（無 session、無 CSRF）。
*/

Route::prefix('agent')->group(function () {

    // 金鑰資訊／欄位說明／上傳（上傳需要 files:write）
    // 控制器一律寫成「以反斜線開頭的完整類別名稱」，才不會被 api 路由群組的 namespace 再往前接一段
    $agent = '\\App\\Http\\Controllers\\Agent\\AgentController';
    Route::get('me', "{$agent}@me")->middleware('agent.key');
    Route::get('schema', "{$agent}@schema")->middleware('agent.key');
    Route::post('upload', "{$agent}@upload")->middleware('agent.key:files');

    foreach (config('agent_api.resources') as $name => $cfg) {
        $c = '\\' . ltrim($cfg['controller'], '\\');
        $style = $cfg['style'] ?? 'crud';
        $prefix = $cfg['prefix'] ?? $name;   // 巢狀資源可自訂，例：car_blind_spot_format/{car_blind_spot}

        Route::prefix($prefix)->middleware('agent.key:' . $cfg['scope'])->group(function () use ($c, $cfg, $style) {

            if ($style === 'single') {           // website / qa / about：只有讀全部＋更新
                Route::get('all', "{$c}@all");
                Route::patch('/', "{$c}@update");
                return;
            }

            if ($style === 'kv') {               // list_banner / home_section：patch {key}
                Route::get('all', "{$c}@all");
                Route::patch($cfg['key'], "{$c}@update");
                return;
            }

            // 標準 CRUD（與 routes/web.php 後台路由一對一）
            Route::post('/', "{$c}@create");
            Route::get('all', "{$c}@all");
            Route::patch('all/sort', "{$c}@sort");
            foreach (($cfg['extra_get'] ?? []) as $g) {
                Route::get($g, "{$c}@{$g}");
            }
            Route::prefix('{id}')->where(['id' => '[0-9]+'])->group(function () use ($c, $cfg) {
                Route::get('/', "{$c}@find");
                Route::patch('/', "{$c}@update");
                Route::delete('/', "{$c}@delete");
                Route::patch('status', "{$c}@status");
                foreach (($cfg['actions'] ?? []) as $k => $v) {
                    $segment = is_int($k) ? $v : $k;   // 'top' 或 'img' => 'deleteImg'
                    $method  = is_int($k) ? $v : $v;
                    Route::patch($segment, "{$c}@{$method}");
                }
            });
        });
    }
});

<?php

namespace App\Console\Commands;

use App\Models\AgentKeyModel;
use Illuminate\Console\Command;

/**
 * Agent API 金鑰管理
 *
 *   php artisan agent:key create --name=hermes --scopes=products:write,products:read,files:write
 *   php artisan agent:key create --name=hermes-all --scopes=*
 *   php artisan agent:key list
 *   php artisan agent:key revoke --id=3
 *
 * 明碼金鑰只在 create 時印一次，之後查不到（資料庫只存雜湊）。
 */
class AgentKeyCommand extends Command
{
    protected $signature = 'agent:key {action : create|list|revoke} {--name=} {--scopes=} {--id=}';
    protected $description = 'Agent API 金鑰：建立／列出／停用';

    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'create') {
            $name = $this->option('name') ?: $this->ask('這把金鑰的名稱（例：hermes-products）');
            $scopesRaw = $this->option('scopes') ?: $this->ask('權限範圍，逗號分隔（例：products:read,products:write,files:write；全部＝*）');
            $scopes = array_values(array_filter(array_map('trim', explode(',', $scopesRaw))));
            $valid = array_keys(config('agent_api.scopes'));
            foreach ($scopes as $s) {
                if ($s === '*') continue;
                [$g, $rw] = array_pad(explode(':', $s), 2, null);
                if (!in_array($g, $valid, true) || !in_array($rw, ['read', 'write', '*'], true)) {
                    $this->error("不認識的 scope：{$s}。可用群組：" . implode(', ', $valid) . "；後面接 :read、:write 或 :*");
                    return 1;
                }
            }
            [$model, $plain] = AgentKeyModel::issue($name, $scopes);
            $this->info('金鑰已建立（id=' . $model->id . '）。下面這串只會出現這一次，請立刻複製保存：');
            $this->line('');
            $this->line('    ' . $plain);
            $this->line('');
            $this->line('使用方式：HTTP header  ' . config('agent_api.header') . ': ' . $plain);
            $this->line('權限：' . implode(', ', $scopes));
            return 0;
        }

        if ($action === 'list') {
            $rows = AgentKeyModel::orderBy('id')->get()->map(function ($k) {
                return [
                    $k->id, $k->name, $k->key_prefix . '…', implode(',', $k->scopes ?? []),
                    optional($k->last_used_at)->toDateTimeString() ?: '-',
                    $k->revoked_at ? '已停用 ' . $k->revoked_at->toDateTimeString() : '啟用中',
                ];
            })->all();
            $this->table(['id', '名稱', '前綴', '權限', '最後使用', '狀態'], $rows);
            return 0;
        }

        if ($action === 'revoke') {
            $id = $this->option('id') ?: $this->ask('要停用的金鑰 id（用 list 查）');
            $k = AgentKeyModel::find($id);
            if (!$k) { $this->error('查無此金鑰'); return 1; }
            if ($k->revoked_at) { $this->warn('這把本來就已停用'); return 0; }
            $k->forceFill(['revoked_at' => now()])->save();
            $this->info("金鑰 #{$k->id}（{$k->name}）已停用，立即失效。");
            return 0;
        }

        $this->error('action 只能是 create、list、revoke');
        return 1;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentKeyModel extends Model
{
    protected $table = 'agent_keys';
    protected $guarded = [];
    protected $casts = [
        'scopes' => 'array',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * 產生一把新金鑰。回傳 [model, 明碼金鑰]；明碼只有這一次拿得到。
     */
    public static function issue(string $name, array $scopes): array
    {
        $plain = 'mmk_' . Str::random(40);
        $model = static::create([
            'name' => $name,
            'key_prefix' => substr($plain, 0, 8),
            'key_hash' => hash('sha256', $plain),
            'scopes' => array_values(array_unique($scopes)),
        ]);
        return [$model, $plain];
    }

    public static function findByPlain(?string $plain): ?self
    {
        if (!$plain) return null;
        return static::where('key_hash', hash('sha256', $plain))->whereNull('revoked_at')->first();
    }

    /**
     * 是否擁有某個 scope（例：products:write）。"*" 代表全部；"products:*" 代表該資源讀寫。
     */
    public function can(string $scope): bool
    {
        $scopes = $this->scopes ?? [];
        if (in_array('*', $scopes, true)) return true;
        if (in_array($scope, $scopes, true)) return true;
        [$res] = explode(':', $scope) + [null];
        return in_array($res . ':*', $scopes, true);
    }
}

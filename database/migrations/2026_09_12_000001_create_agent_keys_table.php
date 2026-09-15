<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agent API 專用金鑰與稽核紀錄。
 *
 * 用途：讓業主自己的 AI agent 不用開後台畫面、不用模擬點滑鼠，
 *       直接用 HTTP 把資料丟進後台（/api/agent/...）。
 *
 * 安全設計：
 *   ・金鑰只存 SHA-256 雜湊，資料庫外洩也拿不到原始金鑰；原始金鑰只在建立當下印一次。
 *   ・scopes 限制能動哪些資源（products / banners / cases / dealers / resources / settings / files），
 *     且分 read／write，跟後台管理員帳號完全分開。
 *   ・revoked_at 有值就立即失效，不用改任何管理員密碼。
 *   ・每一次寫入（POST／PATCH／DELETE）都寫一筆 agent_audit_logs，含改前快照，出事可查可退。
 */
class CreateAgentKeysTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('agent_keys')) {
            Schema::create('agent_keys', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('金鑰用途／持有者，例：hermes-products');
                $table->string('key_prefix', 12)->comment('金鑰前 8 碼，方便辨認是哪一把，不含秘密');
                $table->string('key_hash', 64)->unique()->comment('SHA-256(金鑰)');
                $table->json('scopes')->comment('權限範圍陣列，例：["products:read","products:write","files:write"]；"*" 代表全部');
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('revoked_at')->nullable()->comment('有值＝已停用');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('agent_audit_logs')) {
            Schema::create('agent_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('agent_key_id')->index();
                $table->string('method', 10);
                $table->string('path');
                $table->string('resource')->nullable()->comment('例：car_media、banner');
                $table->string('record_id')->nullable();
                $table->json('payload')->nullable()->comment('送進來的資料（去掉檔案本體）');
                $table->json('before')->nullable()->comment('修改／刪除前的資料快照');
                $table->unsignedSmallInteger('response_status');
                $table->string('ip', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('agent_audit_logs');
        Schema::dropIfExists('agent_keys');
    }
}

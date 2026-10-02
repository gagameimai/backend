<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 新增 setting.type = 'content_policy'（內容來源與更正聲明，前台 /contentPolicy）。
 * content 是 JSON：{ zh: {title, intro, body}, en: {title, intro, body} }，起始值取 config/content_policy_default.php。
 */
class AddContentPolicySettingRow extends Migration
{
    public function up()
    {
        if (DB::table('setting')->where('type', 'content_policy')->exists()) {
            return;
        }
        DB::table('setting')->insert([
            'type' => 'content_policy',
            'content' => json_encode(config('content_policy_default'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('setting')->where('type', 'content_policy')->delete();
    }
}

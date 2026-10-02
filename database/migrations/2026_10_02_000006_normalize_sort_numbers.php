<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 把所有「可排序」資料表的 sort 重新編成 1、2、3…（照目前後台顯示順序），
 * 清掉歷史資料裡 0,0,0,1,1,1 這種重複／全 0 的號碼。顯示順序不會變，只是號碼變乾淨。
 * 之後在後台拖曳或按 ↑↓，整頁會自動重新編號（public/js/self-plugin.js applyOrder），不會再亂掉。
 * 可以重複執行（每次都只是照目前順序重編）。
 */
class NormalizeSortNumbers extends Migration
{
    /** 資料表 => 目前後台列表的排序依據（第一個是群組，例如置頂／狀態；之後是 sort；最後拿 id 或名稱當 tie-break） */
    private function tables(): array
    {
        return [
            'car_media'             => [['is_top', 'desc'], ['sort', 'asc'], ['name', 'asc'], ['id', 'asc']],
            'car_head_unit'         => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_dashcam'           => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_camera'            => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_audio_accessories' => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_headrest'          => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_portable'          => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_fitting'           => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'car_blind_spot'        => [['is_top', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'banner'                => [['status', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'dealer'                => [['status', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'resource_category'     => [['status', 'desc'], ['sort', 'asc'], ['id', 'asc']],
            'resource'              => [['status', 'desc'], ['sort', 'asc'], ['created_at', 'desc'], ['id', 'asc']],
            'car_brand'             => [['status', 'desc'], ['sort', 'asc'], ['name', 'asc'], ['id', 'asc']],
            'recommend_products'    => [['sort', 'asc'], ['created_at', 'desc'], ['id', 'asc']],
        ];
    }

    public function up()
    {
        foreach ($this->tables() as $table => $orders) {
            if (!DB::getSchemaBuilder()->hasTable($table) || !DB::getSchemaBuilder()->hasColumn($table, 'sort')) {
                continue;
            }
            $q = DB::table($table);
            foreach ($orders as [$col, $dir]) {
                if (DB::getSchemaBuilder()->hasColumn($table, $col)) {
                    $q->orderBy($col, $dir);
                }
            }
            $ids = $q->pluck('id');
            DB::transaction(function () use ($table, $ids) {
                $n = 1;
                foreach ($ids as $id) {
                    DB::table($table)->where('id', $id)->update(['sort' => $n++]);
                }
            });
        }
    }

    public function down()
    {
        // 號碼重編無法還原成原本的重複號碼，也不需要（顯示順序沒變）。
    }
}

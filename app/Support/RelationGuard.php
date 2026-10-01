<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * 刪除前的關聯檢查（2026-09-30 新增）
 *
 * 原本後台／Agent API 刪除車廠、車款、資源分類、商品時完全不檢查關聯，
 * 會留下孤兒資料（例：車框 JOIN 車廠時整筆消失、首頁精選指到不存在的商品）。
 * 這裡集中寫檢查邏輯：有關聯就回傳阻擋訊息（控制器回 400），沒有就回 null 放行。
 * 只做「檢查」，不做連帶刪除；要下架請改 status=0。
 */
class RelationGuard
{
    /** 車廠：被 車款／車框／導入事例／盲點適用車款 使用中就不能刪 */
    public static function carBrand($id): ?string
    {
        $uses = [
            '車款'       => DB::table('car')->where('car_brand_id', $id)->count(),
            '安卓車框'   => DB::table('car_frame')->where('car_brand_id', $id)->count(),
            '導入事例'   => DB::table('install_cases')->where('car_brand_id', $id)->count(),
            '盲點適用車款' => DB::table('car_blind_spot_format')->where('car_brand_id', $id)->count(),
        ];
        return self::message('此車廠', $uses);
    }

    /** 車款：被 車框／導入事例 使用中就不能刪 */
    public static function car($id): ?string
    {
        $uses = [
            '安卓車框' => DB::table('car_frame')->where('car_id', $id)->count(),
            '導入事例' => DB::table('install_cases')->where('car_id', $id)->count(),
        ];
        return self::message('此車款', $uses);
    }

    /** 資源分類：底下還有檔案就不能刪 */
    public static function resourceCategory($id): ?string
    {
        $uses = ['資源檔案' => DB::table('resource')->where('resource_category_id', $id)->count()];
        return self::message('此分類', $uses);
    }

    /**
     * 商品（car_media、car_dashcam…、car_frame）：還在「首頁精選商品」裡就不能刪
     * @param string $type config/recommend_product.php 的 types 鍵
     */
    public static function product(string $type, $id): ?string
    {
        $uses = [
            '首頁精選商品' => DB::table('recommend_products')->where('product_type', $type)->where('product_id', $id)->count(),
        ];
        return self::message('此商品', $uses);
    }

    private static function message(string $subject, array $uses): ?string
    {
        $parts = [];
        foreach ($uses as $label => $n) {
            if ($n > 0) $parts[] = "{$n} 筆{$label}";
        }
        if (!$parts) return null;
        return "{$subject}仍被 " . implode('、', $parts) . " 使用中，無法刪除。請先處理那些資料，或改為「停用」即可。";
    }
}

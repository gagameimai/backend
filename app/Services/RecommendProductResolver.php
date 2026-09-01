<?php

namespace App\Services;

class RecommendProductResolver
{
    /**
     * 取得所有支援的分類清單（後台下拉選單第一層用）。
     * 回傳 [ ['value' => 'car_frame', 'label' => '安卓車框'], ... ]
     *
     * @return array
     */
    public static function types(): array
    {
        $types = config('recommend_product.types', []);

        $list = [];
        foreach ($types as $key => $conf) {
            $list[] = [
                'value' => $key,
                'label' => $conf['label'] ?? $key,
            ];
        }

        return $list;
    }

    /**
     * 依分類，取得該分類底下可選的商品清單（後台下拉選單第二層，AJAX 用）。
     * 回傳 [ ['value' => 1, 'label' => '商品名稱'], ... ]
     *
     * @param  string  $type
     * @return array
     */
    public static function options(string $type): array
    {
        $conf = config("recommend_product.types.{$type}");

        if (!$conf || !isset($conf['model'])) {
            return [];
        }

        $modelClass = $conf['model'];

        $rows = $modelClass::select('id', 'name')->orderByDesc('id')->get();

        $list = [];
        foreach ($rows as $row) {
            $list[] = [
                'value' => $row->id,
                'label' => $row->name ?: "#{$row->id}",
            ];
        }

        return $list;
    }

    /**
     * 批次解析 recommend_products 清單，把每一筆的 name / img / link 補上去。
     * 用「先依分類分組、再一次 whereIn 撈」的方式，避免一筆一筆查詢（N+1）。
     *
     * @param  \Illuminate\Support\Collection|array  $rows  RecommendProductModel 清單
     * @return array
     */
    public static function resolveMany($rows): array
    {
        $types = config('recommend_product.types', []);

        // 依 product_type 分組，收集要查的 id
        $idsByType = [];
        foreach ($rows as $row) {
            $idsByType[$row->product_type][] = $row->product_id;
        }

        // 每個分類一次 whereIn 撈出資料，索引成 [id => 資料]
        $dataByType = [];
        foreach ($idsByType as $type => $ids) {
            $conf = $types[$type] ?? null;
            if (!$conf || !isset($conf['model'])) {
                continue;
            }

            $modelClass = $conf['model'];
            $query = $modelClass::whereIn('id', array_unique($ids));

            // car_frame 需要帶出 brand/car 關聯，組合完整名稱
            if ($type === 'car_frame') {
                $query->with(['brand', 'car']);
            }

            $items = $query->get()->keyBy('id');
            $dataByType[$type] = $items;
        }

        // 組合最終輸出
        $result = [];
        foreach ($rows as $row) {
            $conf = $types[$row->product_type] ?? null;
            $item = $dataByType[$row->product_type][$row->product_id] ?? null;

            if (!$conf || !$item) {
                // 對應的商品可能已被刪除，跳過該筆，避免前台顯示壞資料
                continue;
            }

            $name = self::resolveName($row->product_type, $item);
            $img = self::resolveImg($row->product_type, $item);
            $brand = $conf['has_brand'] ?? false ? ($item->brand ?? 0) : null;

            $result[] = [
                'id' => $row->id,
                'product_type' => $row->product_type,
                'product_id' => $row->product_id,
                'sort' => $row->sort,
                'name' => $name,
                'img' => $img,
                'link' => self::buildLink($row->product_type, $row->product_id, $brand),
            ];
        }

        return $result;
    }

    /**
     * 組合顯示名稱。car_frame 是「品牌+車款+名稱」，其餘直接用 name 欄位。
     *
     * @param  string  $type
     * @param  mixed  $item
     * @return string
     */
    protected static function resolveName(string $type, $item): string
    {
        if ($type === 'car_frame') {
            $brandName = $item->brand->name ?? '';
            $carName = $item->car->name ?? '';
            return trim("{$brandName} {$carName} {$item->name}");
        }

        return $item->name ?? '';
    }

    /**
     * 取得縮圖網址。car_frame 的圖片欄位是 JSON 陣列字串，取第一張；其餘直接用 img 欄位。
     *
     * @param  string  $type
     * @param  mixed  $item
     * @return string
     */
    protected static function resolveImg(string $type, $item): string
    {
        if ($type === 'car_frame') {
            $decoded = json_decode($item->img ?? '', true);
            return $decoded[0] ?? '';
        }

        return $item->img ?? '';
    }

    /**
     * 組合前台商品詳情頁連結。
     *
     * @param  string  $type
     * @param  int  $id
     * @param  int|null  $brand  0=MM／1=Clarion，只有 car_dashcam／car_camera 需要
     * @return string
     */
    protected static function buildLink(string $type, int $id, $brand = null): string
    {
        switch ($type) {
            case 'car_frame':
                return "/carFrameDetail/{$id}";
            case 'car_media':
                return "/multimediaDetail/{$id}";
            case 'car_blind_spot':
                return "/safetyDetail/{$id}";
            case 'car_dashcam':
                return (int) $brand === 1 ? "/clarion/dashcamDetail/{$id}" : "/mm/dashcamDetail/{$id}";
            case 'car_camera':
                return (int) $brand === 1 ? "/clarion/cameraDetail/{$id}" : "/mm/cameraDetail/{$id}";
            case 'car_fitting':
                return "/fittingDetail/{$id}";
            case 'car_headrest':
                return "/headrestDetail/{$id}";
            case 'car_portable':
                return "/portableDetail/{$id}";
            case 'car_audio_accessories':
                return "/clarion/audioAccessoriesDetail/{$id}";
            case 'car_head_unit':
                return "/clarion/headUnitDetail/{$id}";
            default:
                return '/';
        }
    }
}

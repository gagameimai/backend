<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 浮水印設定：讓老闆自己在後台換「安卓車框」用的浮水印（換 LOGO 時用）。
 *
 * 運作方式：
 * ・車框（CarFrameController）存檔時會把浮水印 PNG 蓋到圖片上。原本浮水印是寫死在 public/images/watermark*.png，
 *   只能請工程師換檔。
 * ・這裡上傳的新浮水印放在 storage/app/public/watermark-config/（不在程式資料夾，程式更新時不會被蓋掉），
 *   CarFrameController 會「優先用這裡的、沒有才用 public/images 的預設檔」。
 * ・換掉或還原前，舊檔會自動備份到 watermark-config/backup/。
 * ・只影響「之後新上傳／重新存檔」的車框圖片，已經處理過的舊圖不會自動改。
 */
class WatermarkController extends Controller
{
    // 自訂浮水印存放位置（storage/app/public 底下，對外網址 /storage/watermark-config/）
    const DIR = 'watermark-config';

    /**
     * 四組浮水印，key 對應車框圖片群組（imgArr 的 0～3）。
     * 檔名要與 CarFrameController::$watermarkImg 一致。
     *
     * @return array
     */
    protected function slots()
    {
        return [
            0 => ['name' => '車框主圖（列表圖）', 'file' => 'watermark.png'],
            1 => ['name' => '車框配件', 'file' => 'watermark1.png'],
            2 => ['name' => '實際安裝（完工照）', 'file' => 'watermark2.png'],
            3 => ['name' => '車框概觀', 'file' => 'watermark3.png'],
        ];
    }

    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.setting.watermark');
    }

    /**
     * 取得四組浮水印目前使用中的檔案（自訂優先，沒有自訂就顯示預設檔）
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $disk = Storage::disk('public');
        $items = [];

        foreach ($this->slots() as $key => $slot) {
            $customPath = self::DIR . '/' . $slot['file'];
            $defaultPath = public_path('images/' . $slot['file']);
            $isCustom = $disk->exists($customPath);
            $hasDefault = file_exists($defaultPath);

            $url = null;
            $updatedAt = null;
            if ($isCustom) {
                $mtime = $disk->lastModified($customPath);
                $url = asset('storage/' . $customPath) . '?v=' . $mtime;
                $updatedAt = date('Y-m-d H:i', $mtime);
            } elseif ($hasDefault) {
                $url = asset('images/' . $slot['file']) . '?v=' . filemtime($defaultPath);
            }

            $items[] = [
                'key' => $key,
                'name' => $slot['name'],
                'file' => $slot['file'],
                'url' => $url,
                'is_custom' => $isCustom,
                'has_default' => $hasDefault,
                'updated_at' => $updatedAt,
            ];
        }

        return response()->json(['items' => $items]);
    }

    /**
     * 上傳（換掉）某一組浮水印
     *
     * @param \Illuminate\Http\Request $request
     * @param int $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $key)
    {
        $slots = $this->slots();
        if (!isset($slots[$key])) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file|mimes:png|max:5120',
        ], [
            'file.required' => '請選擇要上傳的浮水印圖片',
            'file.file' => '上傳失敗，請重新選擇檔案',
            'file.mimes' => '浮水印只能上傳 PNG 檔（要透明背景）',
            'file.max' => '檔案太大，請壓縮到 5MB 以下',
        ]);

        // 確認真的是圖片，且尺寸合理（避免上傳空檔或過大的圖把車框處理拖慢）
        $info = @getimagesize($request->file('file')->getRealPath());
        if ($info === false || $info[2] !== IMAGETYPE_PNG) {
            return response()->json(['message' => '這不是有效的 PNG 圖片，請重新匯出'], 422);
        }
        if ($info[0] < 20 || $info[1] < 10 || $info[0] > 4000 || $info[1] > 4000) {
            return response()->json(['message' => '圖片尺寸不合理（寬高需在 20×10 到 4000×4000 之間）'], 422);
        }

        $disk = Storage::disk('public');
        $file = $slots[$key]['file'];
        $this->backup($disk, $file);

        $request->file('file')->storeAs(self::DIR, $file, 'public');

        return response()->json([
            'message' => "已更新「{$slots[$key]['name']}」浮水印，之後新上傳或重新存檔的車框圖片會使用新浮水印",
        ]);
    }

    /**
     * 還原成預設浮水印（刪掉自訂檔，舊的先備份）
     *
     * @param int $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($key)
    {
        $slots = $this->slots();
        if (!isset($slots[$key])) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $file = $slots[$key]['file'];

        if (!$disk->exists(self::DIR . '/' . $file)) {
            return response()->json(['message' => '目前已經是預設浮水印'], 422);
        }

        $this->backup($disk, $file);
        $disk->delete(self::DIR . '/' . $file);

        return response()->json(['message' => "已還原「{$slots[$key]['name']}」為預設浮水印"]);
    }

    /**
     * 備份目前的自訂檔到 watermark-config/backup/（檔名加時間），不會自動刪除
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @param string $file
     * @return void
     */
    protected function backup($disk, $file)
    {
        $path = self::DIR . '/' . $file;
        if ($disk->exists($path)) {
            $name = pathinfo($file, PATHINFO_FILENAME) . '_' . date('Ymd_His') . '.png';
            $disk->copy($path, self::DIR . '/backup/' . $name);
        }
    }
}

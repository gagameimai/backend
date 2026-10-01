<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\CarFrameResquest;
use App\Models\CarBrandModel;
use App\Models\CarModel;
use App\Models\CarFrameModel;
use DB, File, Storage;
use Intervention\Image\Facades\Image;

class CarFrameController extends Controller
{
    // 浮水印存放位置(放在storage)
    public $watermarkSave = 'app/public/files/1/watermark/';

    // 浮水印url
    public $watermarkUrl = 'storage/files/1/watermark/';

    // 浮水印圖片(放在public)
    public $watermarkImg = [
        '0' => 'images/watermark.png',
        '1' => 'images/watermark1.png',
        '2' => 'images/watermark2.png',
        '3' => 'images/watermark3.png',
    ];

    // 取得浮水印檔案路徑：優先使用後台「浮水印設定」上傳的自訂檔，沒有則用 public/images 預設檔
    protected function watermarkFile($i)
    {
        $custom = storage_path('app/public/watermark-config/' . basename($this->watermarkImg[$i]));

        return File::exists($custom) ? $custom : public_path($this->watermarkImg[$i]);
    }

    // 浮水印位置
    public $watermarkPath = [
        1 => 'top-left',
        2 => 'bottom-left',
        3 => 'top-right',
        4 => 'bottom-right',
        5 => 'center',
    ];

    /**
     * 把圖片網址整理成「本站 storage 裡真的存在的檔案」。
     * 原本只接受「以 APP_URL/storage 開頭、且沒有 %20／%E4 這類編碼」的網址，其他一律當成沒有圖、存成空字串，
     * 所以 Hermes 送 www 網域、http/https 不同、或帶 URL 編碼的網址時，整張圖就被清空。
     * 這裡改成：不管網域、不管有沒有編碼，只要 /storage/ 後面的路徑對得到檔案就算數。
     *
     * @return array|null [標準網址, storage 磁碟路徑]；找不到檔案回 null
     */
    protected function resolveImg($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        $path = rawurldecode((string) (parse_url($url, PHP_URL_PATH) ?: $url));
        $pos = strpos($path, '/storage/');
        if ($pos === false) {
            return null;
        }
        $rel = ltrim(substr($path, $pos + 9), '/');
        if ($rel === '' || strpos($rel, '..') !== false) {
            return null;
        }
        if (!Storage::exists('public/' . $rel)) {
            return null;
        }

        return [rtrim((string) config('app.url'), '/') . '/storage/' . $rel, 'public/' . $rel];
    }

    // 圖片中文連結處理
    public function link_urldecode($url)
    {
        $uri = '';
        $cs = unpack('C*', $url);
        $len = count($cs);
        for ($i=1; $i<=$len; $i++) {
            if ($cs[$i] > 127 || $cs[$i] == 32) {
                $uri .= '%'. strtoupper(dechex($cs[$i]));
            } else {
                $uri .= $url[$i-1];
            }
        }

        return $uri;
    }

    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.product.car_frame');
    }

    /**
     * 取得全部
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function all(Request $request)
    {
        $query = CarFrameModel::selectRaw('car_frame.*, car_brand.name as brand_name')
            ->with('car')
            ->join('car_brand', 'car_brand.id', '=', 'car_frame.car_brand_id')
            ->orderByDesc('car_frame.status')
            ->orderBy('car_brand.name', 'ASC')
            ->orderBy('car_frame.name', 'ASC')
            ->orderBy('car_frame.year_start', 'ASC');

        if ($request->filled('car_brand_id')) {
            $query = $query->where('car_brand_id', $request->input('car_brand_id'));
            $isSearch = true;
        }

        if ($request->filled('car_id')) {
            $query = $query->where('car_id', $request->input('car_id'));
            $isSearch = true;
        }

        $items = $query->paginate(30);
        foreach ($items as $item) {
            $img = json_decode($item->img, true);
            $item->img = [
                $img[0] ?? '',
                $img[1] ?? '',
                $img[2] ?? '',
            ];
            
            $item->img1 = count(array_filter(json_decode($item->img1, true)));
            $item->img2 = count(array_filter(json_decode($item->img2, true)));
            $item->img3 = count(array_filter(json_decode($item->img3, true)));
        }

        return response()->json([
            'items' => $items,
            'brands' => CarBrandModel::orderByDesc('status')
                ->orderBy('name', 'ASC')
                ->get(),
            'cars' => CarModel::orderByDesc('status')
                ->orderBy('name', 'ASC')
                ->get(),
            'is_search' => $isSearch ?? false
        ]);
    }

    /**
     * 新增
     *
     * @param \App\Http\Requests\Admin\Product\CarFrameResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function create(CarFrameResquest $request)
    {
        $imgArr = [];

        // 圖片
        for ($i=0; $i <= 3; $i++) {
            $imgArr[$i] = [];

            // 圖片數量
            for ($j=0; $j < 3; $j++) {
                try {
                    $imgTmp = $request->input("imgArr.{$i}.{$j}", '');
                    $resolved = $this->resolveImg($imgTmp);
                    if ($resolved) {
                        $imgTmp = $resolved[0];
                        $path = $request->input("watermarkArr.{$i}.{$j}", 0);
                        if ($path != 0 && File::exists($this->watermarkFile($i))) {
                            $fileName = date('Ymdhis') . rand(0, 9) . rand(0, 9) . '.' . pathinfo($imgTmp, PATHINFO_EXTENSION);
                            $imgTmp = $this->link_urldecode($imgTmp);

                            if ($path == -1) {
                                $image = Image::make($imgTmp);
                                $watermark = Image::make($this->watermarkFile($i));
                                $watermark->resize($image->width(), $image->height());
                                $image->insert($watermark)->save(storage_path($this->watermarkSave . $fileName));
                            } else {
                                Image::make($imgTmp)->insert(
                                    $this->watermarkFile($i),
                                    $this->watermarkPath[$path],
                                    10, 10
                                )->save(storage_path($this->watermarkSave . $fileName));
                            }

                            $imgArr[$i][$j] = asset($this->watermarkUrl . $fileName);
                        } else {
                            $imgArr[$i][$j] = $imgTmp;
                        }
                    } else {
                        $imgArr[$i][$j] = '';
                    }
                } catch (\Throwable $th) {
                    $imgArr[$i][$j] = '';
                }
            }
        }

        CarFrameModel::create([
            'car_brand_id' => $request->input('car_brand_id'),
            'car_id' => $request->input('car_id'),
            'year_start' => $request->input('year_start'),
            'year_end' => $request->input('year_end'),
            'size' => $request->input('size'),
            'name' => $request->input('name') ?? '',
            'img' => json_encode($imgArr[0]),
            'img1' => json_encode($imgArr[1]),
            'img2' => json_encode($imgArr[2]),
            'img3' => json_encode($imgArr[3]),
            'content' => $request->input('content', null),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'message' => '新增成功'
        ]);
    }

    /**
     * 更新
     *
     * @param \App\Http\Requests\Admin\Product\CarFrameResquest $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function update(CarFrameResquest $request, $id)
    {
        $item = CarFrameModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            $imgArr = [
                json_decode($item->img, true),
                json_decode($item->img1, true),
                json_decode($item->img2, true),
                json_decode($item->img3, true)
            ];

            // 圖片
            for ($i=0; $i <= 3; $i++) {
                // 圖片數量
                for ($j=0; $j < 3; $j++) {
                    try {
                        $imgTmp = $request->input("imgArr.{$i}.{$j}", '');
                        $old = $imgArr[$i][$j] ?? '';
                        // 這一格整個沒送：保留原本的圖（以前會被清成空字串，Hermes 只改一張圖時其他圖全消失）。
                        // 後台畫面會把每一格都送上來，刪圖是送空字串，不受影響。
                        if (!$request->has("imgArr.{$i}.{$j}")) {
                            continue;
                        }
                        $resolved = $this->resolveImg($imgTmp);
                        if (!$resolved && !empty($imgTmp)) {
                            // 送了網址但對不到檔案：如果就是原本那張（檔案後來被刪）照舊保留；否則明確報錯，不要默默清空
                            if ($old !== '' && rawurldecode($imgTmp) === rawurldecode($old)) {
                                continue;
                            }
                            return response()->json([
                                'message' => "imgArr.{$i}.{$j} 的網址找不到檔案：{$imgTmp}（請直接使用 upload 回傳的 url，必須含 /storage/files/1/…）"
                            ], 422);
                        }
                        if ($resolved) {
                            $imgTmp = $resolved[0];
                            $path = $request->input("watermarkArr.{$i}.{$j}", 0);
                            if ($path != 0 && File::exists($this->watermarkFile($i))) {
                                $fileName = date('Ymdhis') . rand(0, 9) . rand(0, 9) . '.' . pathinfo($imgTmp, PATHINFO_EXTENSION);
                                $imgTmp = $this->link_urldecode($imgTmp);

                                if ($path == -1) {
                                    $image = Image::make($imgTmp);
                                    $watermark = Image::make($this->watermarkFile($i));
                                    $watermark->resize($image->width(), $image->height());
                                    $image->insert($watermark)->save(storage_path($this->watermarkSave . $fileName));
                                } else {
                                    Image::make($imgTmp)->insert(
                                        $this->watermarkFile($i),
                                        $this->watermarkPath[$path],
                                        10, 10
                                    )->save(storage_path($this->watermarkSave . $fileName));
                                }

                                $imgArr[$i][$j] = asset($this->watermarkUrl . $fileName);
                            } else {
                                $imgArr[$i][$j] = $imgTmp;
                            }
                        } else {
                            $imgArr[$i][$j] = '';
                        }
                    } catch (\Throwable $th) {
                        // $imgArr[$i][$j] = '';
                    }
                }
            }

            $item->car_brand_id = $request->input('car_brand_id');
            $item->car_id = $request->input('car_id');
            $item->year_start = $request->input('year_start');
            $item->year_end = $request->input('year_end');
            $item->size = $request->input('size');
            $item->name = $request->input('name') ?? '';
            $item->img = json_encode($imgArr[0]);
            $item->img1 = json_encode($imgArr[1]);
            $item->img2 = json_encode($imgArr[2]);
            $item->img3 = json_encode($imgArr[3]);
            $item->content = $request->input('content', null);
            $item->status = $request->input('status');
            $item->save();

            return response()->json([
                'message' => '更新成功'
            ]);
        }
    }

    /**
     * 刪除
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete($id)
    {
        $item = CarFrameModel::find($id);
        if (empty($item)) {
            return response([
                'message' => '查無資料'
            ], 400);
        } else {
            // 刪除前檢查關聯（App\Support\RelationGuard），有人在用就擋下來
            if ($msg = \App\Support\RelationGuard::product('car_frame', $id)) {
                return response()->json(['message' => $msg], 400);
            }
            $item->delete();

            return response()->json([
                'message' => '刪除成功'
            ]);
        }
    }

    /**
     * 取得單一
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function find($id)
    {
        $item = CarFrameModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            $img = json_decode($item->img, true);
            $item->img = [
                $img[0] ?? '',
                $img[1] ?? '',
                $img[2] ?? '',
            ];

            $img1 = json_decode($item->img1, true);
            $item->img1 = [
                $img1[0] ?? '',
                $img1[1] ?? '',
                $img1[2] ?? '',
            ];

            $img2 = json_decode($item->img2, true);
            $item->img2 = [
                $img2[0] ?? '',
                $img2[1] ?? '',
                $img2[2] ?? '',
            ];

            $img3 = json_decode($item->img3, true);
            $item->img3 = [
                $img3[0] ?? '',
                $img3[1] ?? '',
                $img3[2] ?? '',
            ];

            return response()->json([
                'item' => $item
            ]);
        }
    }

    /**
     * 排序
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory Response
     */
    public function sort(Request $request)
    {
        if (!$request->has(['items'])) {
            return response([
                'message' => '排序更新失敗。'
            ], 400);
        } else {
            DB::update(update_when_case_string('car_frame', 'sort', $request->items));

            return response([
                'message' => '排序更新成功。'
            ]);
        }
    }

    /**
     * 狀態
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function status($id)
    {
        $item = CarFrameModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();

            return response()->json([
                'message' => '狀態更新成功'
            ]);
        }
    }

    /**
     * 刪除圖片
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteImg(Request $request, $id)
    {
        $item = CarFrameModel::find($id);
        if (empty($item)) {
            return response()->json([
                'message' => '查無資料'
            ], 400);
        } else {
            try {
                $type = $request->input('type');
                $index = $request->input('index');

                if (!isset($item->$type)) {
                    throw new \Exception('查無刪除對象');
                }

                $imgArr = json_decode($item->$type, true);
                if (!isset($imgArr[$index])) {
                    throw new \Exception('查無圖片');
                }

                // 2026-09-30 改：只清掉這一格的網址，不再實體刪檔。
                // 原本會 Storage::delete，但同一張圖可能被其他車框／Banner／內文共用，刪了會變壞圖；
                // 檔案要清理請到後台檔案管理員手動處理。
                if ($imgArr[$index] === '' || $imgArr[$index] === null) {
                    throw new \Exception('查無圖片');
                }

                $imgArr[$index] = '';
                $item->$type = json_encode($imgArr);
                $item->save();

                return response()->json([
                    'message' => '狀態更新成功'
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => $th->getMessage()
                ], 400);
            }
        }
    }
}

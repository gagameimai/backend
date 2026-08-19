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

    // 浮水印位置
    public $watermarkPath = [
        1 => 'top-left',
        2 => 'bottom-left',
        3 => 'top-right',
        4 => 'bottom-right',
        5 => 'center',
    ];

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
                    if (!empty($imgTmp) && Storage::exists(str_replace(env('APP_URL') . '/storage', 'public', $imgTmp))) {
                        $path = $request->input("watermarkArr.{$i}.{$j}", 0);
                        if ($path != 0 && File::exists(public_path($this->watermarkImg[$i]))) {
                            $fileName = date('Ymdhis') . rand(0, 9) . rand(0, 9) . '.' . pathinfo($imgTmp, PATHINFO_EXTENSION);
                            $imgTmp = $this->link_urldecode($imgTmp);

                            if ($path == -1) {
                                $image = Image::make($imgTmp);
                                $watermark = Image::make(public_path($this->watermarkImg[$i]));
                                $watermark->resize($image->width(), $image->height());
                                $image->insert($watermark)->save(storage_path($this->watermarkSave . $fileName));
                            } else {
                                Image::make($imgTmp)->insert(
                                    public_path($this->watermarkImg[$i]),
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
                        if (!empty($imgTmp) && Storage::exists(str_replace(env('APP_URL') . '/storage', 'public', $imgTmp))) {
                            $path = $request->input("watermarkArr.{$i}.{$j}", 0);
                            if ($path != 0 && File::exists(public_path($this->watermarkImg[$i]))) {
                                $fileName = date('Ymdhis') . rand(0, 9) . rand(0, 9) . '.' . pathinfo($imgTmp, PATHINFO_EXTENSION);
                                $imgTmp = $this->link_urldecode($imgTmp);

                                if ($path == -1) {
                                    $image = Image::make($imgTmp);
                                    $watermark = Image::make(public_path($this->watermarkImg[$i]));
                                    $watermark->resize($image->width(), $image->height());
                                    $image->insert($watermark)->save(storage_path($this->watermarkSave . $fileName));
                                } else {
                                    Image::make($imgTmp)->insert(
                                        public_path($this->watermarkImg[$i]),
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

                $img = str_replace(env('APP_URL') . '/storage', 'public', $imgArr[$index]);
                if (!Storage::exists($img)) {
                    throw new \Exception('查無圖片');
                }

                Storage::delete($img);

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

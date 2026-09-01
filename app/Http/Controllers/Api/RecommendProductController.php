<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecommendProductModel;
use App\Services\RecommendProductResolver;

class RecommendProductController extends Controller
{
    /**
     * 首頁精選商品
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            $rows = RecommendProductModel::where('status', 1)
                ->orderByDesc('sort')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'result' => RecommendProductResolver::resolveMany($rows)
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('RecommendProductController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

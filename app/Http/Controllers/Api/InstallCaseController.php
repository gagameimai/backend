<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstallCaseModel;

class InstallCaseController extends Controller
{
    /**
     * 首頁安裝案例
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get()
    {
        try {
            return response()->json([
                'result' => InstallCaseModel::selectRaw('id, category, name, img')
                    ->where('status', 1)
                    ->orderByDesc('sort')
                    ->orderByDesc('created_at')
                    ->get()
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('InstallCaseController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealerModel;

class PartnerController extends Controller
{
    /**
     * 經銷商據點
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function get(Request $request)
    {
        try {
            $county = config('county');

            $query = DealerModel::selectRaw('name, tel, county, address')
                ->where('status', 1)
                ->orderBy('sort', 'ASC');

            // search
            if ($request->filled('county')) {
                $query = $query->where('county', $request->get('county'));
            }

            $dealers = $query->get();
            foreach ($dealers as $row) {
                $row->county = $county[$row->county];
            }

            return response()->json([
                'result' => [
                    'county' => $county,
                    'partner' => $dealers
                ]
            ]);
        } catch (\Throwable $th) {
            $this->apiLog('PartnerController->get()異常', $th);

            return response()->json([
                'message' => '系統異常'
            ], 500);
        }
    }
}

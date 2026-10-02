<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SettingModel;
use App\Http\Controllers\Admin\Setting\ContentPolicyController as AdminController;

class ContentPolicyController extends Controller
{
    /** 內容來源與更正聲明：回 { result: { zh: {title, intro, body}, en: {...} } }，空字串＝前台用內建文字 */
    public function get()
    {
        try {
            $raw = SettingModel::where('type', 'content_policy')->value('content');
            return response()->json(['result' => AdminController::decodeContent($raw)]);
        } catch (\Throwable $th) {
            $this->apiLog('ContentPolicyController->get()異常', $th);
            return response()->json(['message' => '系統異常'], 500);
        }
    }
}

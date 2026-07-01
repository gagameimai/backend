<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginResquest;
use Auth;

class LoginController extends Controller
{
    /**
     * 初始化
     *
     * @return mixed
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::guard('admin')->check() || Auth::guard('admin')->viaRemember()) {
                return redirect()->route('admin.main');
            } else {
                return $next($request);
            }
       });
    }

    /**
     * 畫面
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('admin.login');
    }

    /**
     * 驗證
     *
     * @param \App\Http\Requests\LoginResquest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function loginAuth(LoginResquest $request)
    {
        $attempt = Auth::guard('admin')->attempt([
            'account' => $request->input('account'),
            'password' => $request->input('password'),
        ], false);
        if (!$attempt) {
            return response()->json([
                'message' => '帳號密碼輸入錯誤。'
            ], 401);
        } else {
            $auth = Auth::guard('admin')->user();
            if ($auth->status != 1) {
                Auth::guard('admin')->logout();

                return response()->json([
                    'status' => 'error',
                    'message' => '帳號已被關閉。'
                ], 400);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => '登入成功。'
                ]);
            }
        }
    }
}

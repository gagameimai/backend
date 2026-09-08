<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 導入事例（安裝實績）擴充欄位
 *
 * 說明：install_cases 資料表已存在（2026_09_01_000001），原本只有
 * category / name / img / sort / status，僅夠首頁的小卡使用。
 * 獨立的「導入事例」頁需要更多資訊，因此以「擴充既有資料表」的方式新增欄位，
 * 不新建資料表、不影響既有資料。
 *
 * 車型：不是自由文字，而是接既有的 car_brand（汽車品牌）與 car（汽車車款）兩張表，
 *       後台用兩層連動下拉選取，前台 API 再把品牌與車款組成顯示字串。
 *
 * 排序規則（前台）：先看 is_pinned（置頂在前），再看 installed_at 由新到舊。
 * 首頁只取 is_home = 1 的資料，依 home_sort 由小到大，最多 3 筆。
 *
 * 本檔案為冪等（idempotent）寫法：每個欄位都先用 hasColumn 判斷，
 * 重複執行不會出錯，也可安全地在已部分套用的資料庫上再跑一次。
 *
 * ※ 早期版本曾建立自由文字欄位 car_model，已改為 car_brand_id / car_id。
 *   若資料庫裡還留著 car_model，本檔案會一併移除。
 */
class AddCaseFieldsToInstallCasesTable extends Migration
{
    public function up()
    {
        Schema::table('install_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('install_cases', 'installed_at')) {
                $table->date('installed_at')->nullable()->after('img')
                    ->comment('安裝日期（前台排序用，不填則以 created_at 遞補）');
            }
            if (!Schema::hasColumn('install_cases', 'is_pinned')) {
                $table->tinyInteger('is_pinned')->default(0)->after('installed_at')
                    ->comment('是否置頂：0=否，1=是');
            }
            if (!Schema::hasColumn('install_cases', 'is_home')) {
                $table->tinyInteger('is_home')->default(0)->after('is_pinned')
                    ->comment('是否顯示於首頁：0=否，1=是（首頁最多顯示 3 筆）');
            }
            if (!Schema::hasColumn('install_cases', 'home_sort')) {
                $table->integer('home_sort')->default(0)->after('is_home')
                    ->comment('首頁排序，數字小的在前');
            }

            // 車型＝汽車品牌 + 汽車車款（沿用安卓車框在用的那兩張表）
            if (!Schema::hasColumn('install_cases', 'car_brand_id')) {
                $table->integer('car_brand_id')->nullable()->after('home_sort')
                    ->comment('汽車品牌 car_brand.id');
            }
            if (!Schema::hasColumn('install_cases', 'car_id')) {
                $table->integer('car_id')->nullable()->after('car_brand_id')
                    ->comment('汽車車款 car.id');
            }

            if (!Schema::hasColumn('install_cases', 'product')) {
                $table->string('product')->nullable()->after('car_id')
                    ->comment('安裝產品，例：Clarion GL-1002');
            }
            if (!Schema::hasColumn('install_cases', 'dealer')) {
                $table->string('dealer')->nullable()->after('product')
                    ->comment('施工據點名稱');
            }
            if (!Schema::hasColumn('install_cases', 'need')) {
                $table->text('need')->nullable()->after('dealer')
                    ->comment('客戶需求');
            }
            if (!Schema::hasColumn('install_cases', 'work')) {
                $table->text('work')->nullable()->after('need')
                    ->comment('施工內容 / 處理重點');
            }
        });

        // 舊版的自由文字車型欄位，若存在則移除（獨立一次 Schema::table，確保上面的異動先套用）
        if (Schema::hasColumn('install_cases', 'car_model')) {
            Schema::table('install_cases', function (Blueprint $table) {
                $table->dropColumn('car_model');
            });
        }
    }

    public function down()
    {
        Schema::table('install_cases', function (Blueprint $table) {
            $drop = [];
            foreach ([
                'installed_at', 'is_pinned', 'is_home', 'home_sort',
                'car_brand_id', 'car_id', 'product', 'dealer', 'need', 'work'
            ] as $col) {
                if (Schema::hasColumn('install_cases', $col)) {
                    $drop[] = $col;
                }
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminModel;
use Hash;

class AdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:admin-user {--mode=add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '後台管理者';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            switch ($this->option('mode')) {
                case 'add':
                    $name = $this->ask('請輸入姓名?');
                    $account = $this->ask('請輸入帳號');
                    $password = $this->secret('請輸入密碼');
                    if (empty($name) || empty($account) || empty($password)) {
                        $this->error('新增失敗,請確實完整輸入');
                    } else {
                        $admin = AdminModel::where('account', $account)->first();
                        if (!empty($admin)) {
                            $this->error('新增失敗,帳號已存在');
                        } else {
                            AdminModel::create([
                                'name' => $name,
                                'account' => $account,
                                'password' => Hash::make($password),
                            ]);

                            $this->info('新增成功');
                        }
                    }
                    break;

                case 'edit':
                    # code...
                    break;

                default:
                    $this->error('操作失敗');
                    break;
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}

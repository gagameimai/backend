<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SettingModel;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingModel::insert([
            [
                'type' => 'website',
                'content' => json_encode([
                    'copyright' => '',
                    'address' => '',
                    'tel' => '',
                    'email' => '',
                    'facebook' => '',
                    'instagram' => '',
                    'youtube' => ''
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'qa',
                'contnet' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}

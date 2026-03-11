<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Identitas Aplikasi
            [
                'key' => 'app_name',
                'value' => 'ArsipSurat',
                'group' => 'app',
                'type' => 'text',
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Pengelolaan Arsip Digital',
                'group' => 'app',
                'type' => 'text',
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'group' => 'app',
                'type' => 'image',
            ],
            
            // Identitas Instansi
            [
                'key' => 'institution_name',
                'value' => 'Nama Instansi',
                'group' => 'institution',
                'type' => 'text',
            ],
            [
                'key' => 'institution_address',
                'value' => 'Jl. Contoh Alamat No. 123, Kota Contoh',
                'group' => 'institution',
                'type' => 'textarea',
            ],
            [
                'key' => 'institution_email',
                'value' => 'info@instansi.com',
                'group' => 'institution',
                'type' => 'text',
            ],
            [
                'key' => 'institution_phone',
                'value' => '021-12345678',
                'group' => 'institution',
                'type' => 'text',
            ],
            [
                'key' => 'institution_logo',
                'value' => null,
                'group' => 'institution',
                'type' => 'image',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

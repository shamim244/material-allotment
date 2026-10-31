<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'mail_mailer', 'value' => 'smtp', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_host', 'value' => 'mailhog', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_port', 'value' => '1025', 'group' => 'smtp', 'type' => 'integer'],
            ['key' => 'mail_username', 'value' => null, 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_password', 'value' => null, 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_encryption', 'value' => null, 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_address', 'value' => 'hello@example.com', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_name', 'value' => 'Example', 'group' => 'smtp', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

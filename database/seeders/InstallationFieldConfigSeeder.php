<?php

namespace Database\Seeders;

use App\Models\InstallationFieldConfig;
use Illuminate\Database\Seeder;

class InstallationFieldConfigSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['field_name' => 'request_number', 'label' => 'Request Number', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 1],
            ['field_name' => 'mobile_number', 'label' => 'Mobile Number', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 2],
            ['field_name' => 'location', 'label' => 'Location (GPS)', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 3],
            ['field_name' => 'remark', 'label' => 'Remark', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 4],
        ];

        foreach ($fields as $field) {
            InstallationFieldConfig::updateOrCreate(
                ['field_name' => $field['field_name']],
                $field
            );
        }
    }
}

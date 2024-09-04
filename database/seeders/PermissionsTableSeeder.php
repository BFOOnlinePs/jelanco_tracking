<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'اضافة مستخدم',
            'تعديل مستخدم',
            'حذف مستخدم',
            'تعديل الحالة',
            'عرض المستخدمين',
            'اضافة مهمة',
            'تعديل مهمة',
            'حذف مهمة',
            'عرض المهام',
            'تعيين مهمة',
            'تسليم مهمة',
            'اضافة فئة لمهمة',
            'تعديل فئة لمهمة',
            'عرض فئات المهام',
            'اضافة تعليق',
            'اضافة دور',
            'تعديل دور',
            'عرض الادوار',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],[] 
            );
        }  
    }
}

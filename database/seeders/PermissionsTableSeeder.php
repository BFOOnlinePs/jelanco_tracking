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
            'اضافة تكليف',
            'تعديل تكليف',
            'حذف تكليف',
            'عرض التكليفات',
            'تعيين تكليف',
            'تسليم تكليف',
            'اضافة فئة تكليف',
            'تعديل فئة تكليف',
            'عرض فئات التكليفات',
            'اضافة تعليق',
            'عرض التعليقات',
            'اضافة دور',
            'تعديل دور',
            'عرض الادوار',
            'تعديل تسليم تكليف',
            'عرض التسليمات',
            'متابعة الموظفين',
            'عرض تكليفاتي'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                []
            );
        }

        // $adminRole = Role::create(['name' => 'admin']);
        // $userRole = Role::create(['name' => 'user']);

        // $adminRole->givePermissionTo($permissions);

        // $userRole->givePermissionTo(['create task', 'view reports']);
    }
}

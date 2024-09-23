<?php

namespace App\Helpers;

use App\Models\User;

class SystemPermissions
{
    // Permissions from the database
    const ADD_USER = 'اضافة مستخدم';
    const EDIT_USER = 'تعديل مستخدم';
    const DELETE_USER = 'حذف مستخدم';
    const EDIT_STATUS = 'تعديل الحالة';
    const VIEW_USERS = 'عرض المستخدمين';
    const ADD_TASK = 'اضافة مهمة';
    const EDIT_TASK = 'تعديل مهمة';
    const DELETE_TASK = 'حذف مهمة';
    const VIEW_TASKS = 'عرض المهام';
    const ASSIGN_TASK = 'تعيين مهمة';
    const SUBMIT_TASK = 'تسليم مهمة';
    const ADD_TASK_CATEGORY = 'اضافة فئة لمهمة';
    const EDIT_TASK_CATEGORY = 'تعديل فئة لمهمة';
    const VIEW_TASK_CATEGORIES = 'عرض فئات المهام';
    const ADD_COMMENT = 'اضافة تعليق';
    const ADD_ROLE = 'اضافة دور';
    const EDIT_ROLE = 'تعديل دور';
    const VIEW_ROLES = 'عرض الادوار';

    // not added to database yet
    const EDIT_SUBMISSION = 'تعديل تسليم';
    const VIEW_COMMENTS = 'عرض التعليقات';
    const VIEW_MY_EMPLOYEES_SUBMISSIONS = 'عرض تسليمات موظفيني'; // all submissions of my employees (even tasks assigned by another manager)


    /**
     * Check if the authenticated user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public static function hasPermission($permission)
    {
        $user = User::find(auth()->user()->id);
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        return in_array($permission, $userPermissions);
    }

    /**
     * Check if the authenticated user has all the required permissions.
     *
     * @param array $requiredPermissions
     * @return bool
     */

    public static function hasAllPermissions($requiredPermissions)
    {
        $user = User::find(auth()->user()->id);
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        return collect($requiredPermissions)->every(function ($permission) use ($userPermissions) {
            return in_array($permission, $userPermissions);
        });
    }
}

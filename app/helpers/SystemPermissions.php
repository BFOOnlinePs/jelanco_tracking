<?php

namespace App\helpers;

use App\Models\User;

class SystemPermissions
{
    const ADD_USER = 'اضافة مستخدم';
    const EDIT_USER = 'تعديل مستخدم';
    const DELETE_USER = 'حذف مستخدم';
    const EDIT_STATUS = 'تعديل الحالة';
    const VIEW_USERS = 'عرض المستخدمين';
    const ADD_TASK = 'اضافة تكليف';
    const EDIT_TASK = 'تعديل تكليف';
    const DELETE_TASK = 'حذف تكليف';
    const VIEW_TASKS = 'عرض التكليفات';
    const ASSIGN_TASK = 'تعيين تكليف';
    const SUBMIT_TASK = 'تسليم تكليف';
    const ADD_TASK_CATEGORY = 'اضافة فئة تكليف';
    const EDIT_TASK_CATEGORY = 'تعديل فئة تكليف';
    const VIEW_TASK_CATEGORIES = 'عرض فئات التكليفات';
    const ADD_COMMENT = 'اضافة تعليق';
    const ADD_ROLE = 'اضافة دور';
    const EDIT_ROLE = 'تعديل دور';
    const VIEW_ROLES = 'عرض الادوار';
    const EDIT_SUBMISSION = 'تعديل تسليم تكليف';
    const VIEW_COMMENTS = 'عرض التعليقات';
    // const VIEW_MY_EMPLOYEES_SUBMISSIONS = 'عرض تسليمات موظفيني'; // all submissions of my employees (even tasks assigned by another manager)
    const VIEW_SUBMISSIONS = 'عرض التسليمات'; // my submissions + all submissions of my employees / if he has the permission (even tasks assigned by another manager)
    const VIEW_MANAGER_EMPLOYEES = 'متابعة الموظفين';
    const VIEW_ASSIGNED_TASKS = 'عرض تكليفاتي'; // المهام الموكلة إلي
    const USERS_FOLLOW_UP_MANAGEMENT = 'تعيين متابعين';


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

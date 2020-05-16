<?php

namespace App\Libs;

use Auth;

class MyCheck
{
    public static $roleTitle = [
        0 => '用户',
        1 => '管理员',
        2 => '站长'
    ];

    public static $roleName = [
        0 => 'user',
        1 => 'admin',
        2 => 'super'
    ];

    public static $roleColor = [
        0 => 'gray',
        1 => 'green',
        2 => 'gold'
    ];

    static function roleTitle ($right) {
       return collect(Self::$roleTitle)->get($right);
    }

    static function roleName ($right) {
        return collect(Self::$roleName)->get($right);
    }

    static function heHasRight($right,$roleName) {
        return $right >= collect(Self::$roleName)->flip()->get($roleName);
    }

    static function IhaveRight($roleName) {
        $user = Auth::user();
        if(!$user ) return false;

        $right = $user->right;
        return $right >= collect(Self::$roleName)->flip()->get($roleName);
    }

    static function roleColor($right) {
        return collect(Self::$roleColor)->get($right);
    }
}
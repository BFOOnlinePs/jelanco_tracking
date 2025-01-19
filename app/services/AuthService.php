<?php

namespace App\Services;

class AuthService
{
    public function removeTokens($user)
    {
        $user->tokens()->delete();
        $user->notificationTokens()->delete();
    }
}

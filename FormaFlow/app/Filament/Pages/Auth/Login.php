<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.auth.split-screen-layout';

    public function hasLogo(): bool
    {
        return false;
    }
}

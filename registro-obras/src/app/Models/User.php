<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'email_verified_at'
    ];

    protected $appends = [
        'type'
    ];

    public function sendPasswordResetNotification($token)
    {
        ResetPasswordNotification::$createUrlCallback = [$this, 'createUrlCallback'];
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }

    public function createUrlCallback($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $url .= '#users';

        return $url;
    }

    public function getTypeAttribute() {
        return 'user';
    }
}

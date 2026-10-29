<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [        
        'surname',
        'firstname',
        'middlename',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function contributor()
    {
        return $this->hasMany(FlowContributor::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    /**
     * Sends the "set a new password" email (called by the password broker).
     * The link is built on DRIP_BASE_URL, like the portal's other emails.
     * A mail failure is left to throw, so the page can say it was not sent.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = \App\Http\Classes\Notifier::url('password.reset', ['token' => $token, 'email' => $this->email]);
        $minutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\PasswordResetMail($this, $url, $minutes));
    }
}

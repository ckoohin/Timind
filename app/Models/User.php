<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $table = 'users';
    protected $primaryKey = 'id';
<<<<<<< HEAD

=======
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
<<<<<<< HEAD

=======
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
    public function activities() {
        return $this->hasMany(Activity::class, 'user_id' , 'id');
    }

    public function activityCategory() {
        return $this->belongsToMany(ActivityCategory::class, 'activities', 'user_id' , 'category_id');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color_code',
        'icon',
        'type',
        'is_system_default'
    ];

    protected $casts = [
        'is_system_default' => 'boolean',
    ];

    protected $table = 'activity_categories';
    protected $primaryKey = 'id';

    public function activities() {
        return $this->hasMany(Activity::class, 'category_id' , 'id');
    }

    public function user() {
        return $this->belongsToMany(User::class, 'activities', 'user_id' , 'category_id');
    }
}

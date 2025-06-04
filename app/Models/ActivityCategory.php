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

    public function activities()
    {
        return $this->hasMany(Activity::class, 'category_id');
    }
}

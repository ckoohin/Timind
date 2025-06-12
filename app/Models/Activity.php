<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'is_all_day',
        'recurrence_type',
        'recurrence_data',
        'priority',
        'status',
        'location',
        'reminder_settings'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_all_day' => 'boolean',
        'recurrence_data' => 'json',
        'reminder_settings' => 'json',
    ];

    protected $table = 'activities';
    protected $primaryKey = 'id';

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ActivityCategory::class,'category_id', 'id');
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }
    
    public function calculateDuration()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $end->diffInMinutes($start);
    }
}
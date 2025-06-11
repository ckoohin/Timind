<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInteractionLog extends Model
{
    protected $table = 'ai_interaction_logs';
    protected $fillable = ['comment_text', 'feedback_text'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoverLetter extends Model
{
    protected $fillable = ['user_hh_id', 'title', 'content'];
    
    /**
     * Получить сопроводительные письма для конкретного пользователя HH
     */
    public static function forUser(string $userHhId)
    {
        return static::where('user_hh_id', $userHhId);
    }
}

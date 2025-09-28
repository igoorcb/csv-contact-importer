<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthdate',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function getGravatarUrlAttribute()
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=64";
    }
}

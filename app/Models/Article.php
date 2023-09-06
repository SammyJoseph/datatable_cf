<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = []; // permite que se guarden todos los campos

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

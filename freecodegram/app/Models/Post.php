<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
//    use HasFactory;
    protected $guarded = [];


    public static function create(array $data)
    {

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

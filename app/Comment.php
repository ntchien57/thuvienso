<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    protected $table = 'danhgia';
    protected $fillable = ['user_id', 'sach_id', 'rating', 'content'];

    public function user()
    {
        return $this->belongsTo(Qltv_Docgia::class, 'user_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public $timestamps = false;
    protected $table        = 'yeuthich';
    protected $fillable     = ['user_id', 'sach_id'];
    public function Sach(){
        return $this->belongsTo('App\Qltv_Sach', 'sach_id', 'id');
    }
}

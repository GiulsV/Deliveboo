<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'img'
    ];

    public function users() {
        return $this->belongsToMany('App\Models\User');
    }
}

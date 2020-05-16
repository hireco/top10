<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected  $table = 'posts';
    public $timestamps = false;
    protected  $guarded = ['id'];

    protected $dates = ['deleted_at'];
}

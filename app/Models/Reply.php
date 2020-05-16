<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use SoftDeletes;

    protected  $table = 'replies';
    public $timestamps = false;
    protected  $guarded = ['id'];

    protected $dates = ['deleted_at'];
}

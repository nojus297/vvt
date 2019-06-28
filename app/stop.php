<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class stop extends Model
{
    //
    protected $primaryKey = 'stop_id';
    public $incrementing  = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

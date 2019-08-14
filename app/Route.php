<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $primaryKey = 'route_id';
    public $incrementing  = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

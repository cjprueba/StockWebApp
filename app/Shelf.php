<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    protected $connection = 'retail';
	protected $table = 'shelf';
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	protected $table = 'telas';
	protected $fillable = ['title', 'descripcion'];
}

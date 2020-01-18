<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarcaAux extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'marca_aux';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    //public $timestamps=false;
    protected $fillable = [
        'CODIGO_MARCA',
        'DESCUENTO',
        'FECHAINI',
        'FECHAFIN',
        'FECMODIF',
        'FECALTAS'
   ];
}

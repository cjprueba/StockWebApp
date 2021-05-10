<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\rptTopArticulos;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Temp_venta;

class rptTopArticulosExport implements WithMultipleSheets
{
	  private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea=[];
  private $nullsheets;
  private $sheets = [];
  private $marcas=[];
  private $hojas=1;
  private $inicio;
  private $final;
  private $checkedProveedor;
  private $insert;
  private $checkedSucursal;
  private $sucursal;
  private $agrupar;
  private $checkedTipo;
  private $stock;
  private $seccion;
  private $top;
  

   private $vales = array("v", "a", "l", "e", "%");
         use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
 public function __construct($datos)
    {

     /*var_dump($datos);*/
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
        $this->checkedProveedor= $datos['AllProveedores'];
        /*$this->checkedSucursal=$datos['AllSucursales'];*/
        $this->Proveedores=$datos['Proveedores'];
        $this->checkedTipo=$datos['Agrupar'];
        $this->stock=$datos["Stock"];
        $this->seccion=$datos['Seccion'];
        $this->insert=$datos['Insert'];
        $this->top=$datos['Top'];
    }
        public function sheets(): array
    {
    	$user = auth()->user();
    	
    		$datos=array(
    		 'inicio'=>$this->inicio,
    		 'final'=>$this->final,
    		 'sucursal'=>$this->sucursal,
    		 'checkedProveedor'=>$this->checkedProveedor,
    		 'checkedTipo'=>$this->checkedTipo,
    		 'proveedores'=>$this->Proveedores,
    		 'seccion'=>$this->seccion,
    		 'stock'=>$this->seccion,
    		 'insert'=>$this->insert,
    		 'top'=>$this->top
    		);

	    $this->sheets[]= new rptTopArticulos($datos);
	    return $this->sheets;
    }

}

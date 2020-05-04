<?php

namespace App\Exports;

use App\ProductosAux;
use App\Parametro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\StockProductos;
use Illuminate\Support\Facades\DB;

class StockExport implements WithMultipleSheets
{
    use Exportable;
 
    protected $datos;
    
    public function __construct($datos)
    {
        $this->datos = $datos;
    }
 
    /**
     * @return array
     */
    public function sheets(): array
    {
    	// var_dump($this->datos);
    	// return false;
        $sheets = [];
 		
 		/*  --------------------------------------------------------------------------------- */

 		// OBTENER MONEDA SUCURSAL 

 		$parametros = Parametro::select(DB::raw('parametros.MONEDA, MONEDAS.DESCRIPCION, MONEDAS.CANDEC, parametros.TAB_UNICA, parametros.ID_SUCURSAL'))
        ->join('MONEDAS', 'parametros.MONEDA', '=', 'MONEDAS.CODIGO')
        ->where('parametros.ID_SUCURSAL', '=', $this->datos['Sucursal'])
        ->get();

        if ($parametros[0]['CANDEC'] === 0) {
        	$candec = '0';
        } else {
        	$candec = '0.00';
        }

 		/*  --------------------------------------------------------------------------------- */

 		foreach ($this->datos['Marcas'] as $key => $marca) {

 			/*  --------------------------------------------------------------------------------- */

 			// REVISAR SI EXISTE LA COMBINACION DE MARCA Y CATEGORIA 

 			foreach ($this->datos['Categorias'] as $key => $categoria) {

 				$productos = ProductosAux::select(DB::raw('MARCA.DESCRIPCION AS MARCA, LINEAS.DESCRIPCION AS LINEA'))
 						 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
 						 ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
 						 ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                         ->where('PRODUCTOS.MARCA','=', $marca)
                         ->where('PRODUCTOS.LINEA','=', $categoria)
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $this->datos['Sucursal'])
                         ->groupBy('PRODUCTOS.MARCA')
                         ->groupBy('PRODUCTOS.LINEA')
                         ->get();

                /*  --------------------------------------------------------------------------------- */

                // CARGAR LAS PAGINAS 
                // var_dump($this->datos['Stock']);
                // return false;
                if (count($productos) > 0) {
 					$sheets[] = new StockProductos($categoria, $marca, $productos[0]['LINEA'], $productos[0]['MARCA'], $candec, $this->datos['Sucursal'], (string)$this->datos['Inicio'], (string)$this->datos['Final'], (string)$this->datos['InicioCompra'], (string)$this->datos['FinalCompra'], $this->datos['Stock']);         
                }

                /*  --------------------------------------------------------------------------------- */

            }

 		}
 			

 		/*  --------------------------------------------------------------------------------- */

 
        return $sheets;
    }
}

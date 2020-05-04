<?php

namespace App\Exports;

use App\User;
use App\ProductosAux;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class StockProductos implements FromArray, WithTitle, ShouldAutoSize, WithEvents,WithColumnFormatting 
{
    private $marcas;
    private $categorias;
    private $marca_nombre;
    private $categoria_nombre;
    private $candec;
 	private $productos = [];
 	private $posicion = 1;

 	private $stock = 0;
 	private $precio_venta = 0;
 	private $precio_mayorista = 0;

 	private $compra_inicial = 0;
 	private $compra_final = 0;

 	private $stock_boolean;

    public function __construct(int $categorias, int $marcas, string $categoria_nombre, string $marca_nombre, string $candec, int $sucursal, string $venta_inicial, string $venta_final, string $compra_inicial, string $compra_final, bool $stock_boolean)
    {
        $this->marcas = $marcas;
        $this->categorias  = $categorias;
        $this->categoria_nombre = $categoria_nombre;
        $this->marca_nombre = $marca_nombre;
        $this->candec = $candec;
        $this->sucursal = $sucursal;
        $this->venta_inicial = date('Y-m-d', strtotime($venta_inicial));
        $this->venta_final = date('Y-m-d', strtotime($venta_final));
        $this->compra_inicial = date('Y-m-d', strtotime($compra_inicial));
        $this->compra_final = date('Y-m-d', strtotime($compra_final));
        $this->stock_boolean = $stock_boolean;
    }

    public function  array(): array
    {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$venta_inicial = $this->venta_inicial;
    	$venta_final = $this->venta_final;
    	$compra_inicial = $this->compra_inicial;
    	$compra_final = $this->compra_final;
    	$stock_boolean = $this->stock_boolean;

    	/*  --------------------------------------------------------------------------------- */

    	$this->productos = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA,  PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.FECHULT_V, PRODUCTOS_AUX.FECHULT_C'),
	    DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
    	->when($venta_inicial !== '1969-12-31', function ($query) use($venta_inicial, $venta_final) {
			return $query->whereBetween('PRODUCTOS_AUX.FECHULT_V', [$venta_inicial, $venta_final]);
		})
		->when($compra_inicial !== '1969-12-31', function ($query) use($compra_inicial, $compra_final) {
			return $query->whereBetween('PRODUCTOS_AUX.FECHULT_C', [$compra_inicial, $compra_final]);
		})
	    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
	    ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
	    ->where('PRODUCTOS.MARCA','=', $this->marcas)
	    ->where('PRODUCTOS.LINEA','=', $this->categorias)
	    ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $this->sucursal)
	    ->when($stock_boolean, function ($query) {
			return $query->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0)) > 0');
		})
		->when($stock_boolean === false, function ($query) {
			return $query->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0)) = 0');
		})
	    ->get()
	    ->toArray();

        /*  --------------------------------------------------------------------------------- */

        // ARRAY PARA TITULOS

        $productos_array[] = array('CODIGO', 'DESCRIPCION', 'ULTIMA COMPRA', 'ULTIMA VENTA', 'STOCK', 'PRECIO VENTA','PRECIO MAYORISTA');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR ARRAY

        foreach ($this->productos as $key => $value) {

        	/*  --------------------------------------------------------------------------------- */

        	// SUMAR LA CANTIDAD DE FILAS 

        	$this->posicion=$this->posicion+1;

        	/*  --------------------------------------------------------------------------------- */

        	$productos_array[] = array(
                 
                'CODIGO'=> $value['CODIGO'],
                'DESCRIPCION'=> $value['DESCRIPCION'],
                'ULTIMA COMPRA'=> $value['FECHULT_C'],
                'ULTIMA VENTA'=> $value['FECHULT_V'],
                'STOCK'=> $value['STOCK'],
                'PRECIO VENTA'=> $value['PREC_VENTA'],
                'PRECIO MAYORISTA'=> $value['PREMAYORISTA'],

        	);

        	/*  --------------------------------------------------------------------------------- */

        	// SUMAR TOTALES

        	$this->stock = $this->stock + $value['STOCK'];
		 	$this->precio_venta = $this->precio_venta + $value['PREC_VENTA'];
		 	$this->precio_mayorista = $this->precio_mayorista + $value['PREMAYORISTA'];

        	/*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // TOTAL GENERAL 

        $productos_array[] = array(
            'CODIGO'=> "",
            'DESCRIPCION'=> "",
            'ULTIMA COMPRA'=> "",
            'ULTIMA VENTA'=> "TOTALES",
            'STOCK'=> $this->stock,
            'PRECIO VENTA' => $this->precio_venta,
            'PRECIO MAYORISTA' => $this->precio_mayorista
        );


        /*  --------------------------------------------------------------------------------- */

        // RETORNAR ARRAY

        return $productos_array;

        /*  --------------------------------------------------------------------------------- */

    }
 	
    /**
     * @return array
    */

 	public function registerEvents(): array
    {

	    $styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
		    ],
		    'borders' => [
		        'top' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'argb' => '97B4C8',
		        ],
		        'endColor' => [
		            'argb' => '97B4C8',
		        ],
		    ],
		];

		return [
            // Handle by a closure.
	        AfterSheet::class => function(AfterSheet $event) use($styleArray)  {
	            $event->sheet->getStyle('A1:G1')->applyfromarray($styleArray);
	            $this->posicion=$this->posicion+1;
	            $event->sheet->getStyle('D'.$this->posicion.':G'.$this->posicion)->applyfromarray($styleArray);
	            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##'.$this->candec.'');
	            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##'.$this->candec.'');
	             
	        }

        ];
	}	

	public function columnFormats(): array
    {
         return [
           'E' => NumberFormat::FORMAT_NUMBER,
           'F' => NumberFormat::FORMAT_NUMBER,
           'G' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return substr($this->marca_nombre, 0, 15).' ' . substr($this->categoria_nombre, 0, 15);
    }
}

<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;

class Producto extends Model
{

	protected $connection = 'retail';
	protected $table = 'productos_aux';

	

    public static function encontrarProducto($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER EL PRODUCTO

    	$producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
    	DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $data['codigo'])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto];
        } else {
            return ['producto' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

     public static function mostrarProductoImagen($dato)
    {

        /*  --------------------------------------------------------------------------------- */

        //  INICIAR VARIABLES

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO CON LA IMAGEN

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, IMAGENES.PICTURE, PRODUCTOS_AUX.FECHULT_C, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PREMAYORISTA, PRODUCTOS_AUX.PREVIP, MONEDAS.CANDEC, PRODUCTOS_AUX.MONEDA'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $dato['codigo'])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER DATOS OBTENIDOS 

        foreach ($producto as $product) {
            $nestedData['CODIGO'] = $product->CODIGO;
            $nestedData['DESCRIPCION'] = $product->DESCRIPCION;
            $nestedData['STOCK'] = Common::formato_precio($product->STOCK, 0);
            $nestedData['PRECOSTO'] = Common::formato_precio($product->PRECOSTO, $product->CANDEC);
            $nestedData['PREC_VENTA'] = Common::formato_precio($product->PREC_VENTA, $product->CANDEC);
            $nestedData['PREMAYORISTA'] = Common::formato_precio($product->PREMAYORISTA, $product->CANDEC);
            $nestedData['PREVIP'] = Common::formato_precio($product->PREVIP, $product->CANDEC);
            $nestedData['FECHULT_C'] = $product->FECHULT_C;
            $nestedData['IMAGEN'] = 'data:image/jpg;base64,'.base64_encode($product->PICTURE);
            $nestedData['MONEDA'] = $product->MONEDA;

            $data[] = $nestedData;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($producto) > 0) {
            return ['productoImagen' => $data];
        } else {
            return ['productoImagen' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function filtrarProductos($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where('PRODUCTOS_AUX.CODIGO', 'LIKE', ''.$data['codigo'].'%')
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(10)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto];
        } else {
            return ['producto' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function codigoInterno($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where('PRODUCTOS_AUX.CODIGO_INTERNO', '=', $data['codigo'])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto];
        } else {
            return ['producto' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }


    public static function datosVariosProducto($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO_INTERNO'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto];
        } else {
            return ['producto' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function existeProducto($codigo, $id_sucursal)
    {

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return true;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    
}

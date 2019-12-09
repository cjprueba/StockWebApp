<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;
use App\ProductosAux;
use App\Imagen;
use App\Cotizacion;

class Producto extends Model
{

	protected $connection = 'retail';
	protected $table = 'productos';

	

    public static function encontrarProducto($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */
    	
        // INICIAR VARIABLES 

        $cotizacion = '';
        $cod_prod = '';

        /*  --------------------------------------------------------------------------------- */

        // CODIGO INTERNO 

        $codigo = Producto::codigoInterno(['codigo' => $data['data']['codigo']]);
       
        if ($codigo["producto"] === 0) {
            $cod_prod = $data['data']['codigo'];
        } else {
            $cod_prod = $codigo["producto"][0]["CODIGO"]; 
        }

        /*  --------------------------------------------------------------------------------- */

    	// OBTENER EL PRODUCTO

    	$producto = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
    	DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $cod_prod)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CONSEGUIR COTIZACION 

        foreach ($producto as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // Cotizacion
            
            $cotizacion = Cotizacion::CALMONED([
                                'monedaProducto' => $value->MONEDA,
                                'monedaSistema' => $data['data']['monedaSistema'],
                                'precio' => $value->PREC_VENTA,
                                'tab_unica' => $data['data']['tab_unica'],
                                'decSistema' => $data['data']['candec']]);

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */
       
        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto[0], 'valor' => $cotizacion["valor"]];
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

        $producto = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
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

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
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

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
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

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO_INTERNO'))
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

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
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


    public static function existeProductoTodasSucursales($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
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

    public static function existeProductoCodigoInterno($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $producto = Producto::select(DB::raw('PRODUCTOS.CODIGO_INTERNO'))
        ->where('PRODUCTOS.CODIGO_INTERNO', '=', $codigo)
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

    public static function generar_ci()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo_interno = '';

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS 

        $parametro = Parametro::consultaPersonalizada('ULT_CODIGO, ULT_CODIGO_INTERNO, SEPARADOR');

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL CODIGO INTERNO - BUSCAR SEPARADOR - FILTRAR POR GENERADO Y TAMAÑO DEL CODIGO

        $producto = Producto::select(DB::raw('MAX(PRODUCTOS.CODIGO_INTERNO) AS CODIGO_INTERNO'))
        ->where('PRODUCTOS.CODIGO_INTERNO', 'LIKE', ''.$user->id_sucursal.''.$parametro->SEPARADOR.'%')
        ->where('PRODUCTOS.GENERADO', '=', 1)
        ->whereRaw('CHAR_LENGTH(PRODUCTOS.GENERADO) = 7')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES NULO EL CODIGO 

        if ($producto[0]->CODIGO_INTERNO === null) {

            /*  --------------------------------------------------------------------------------- */

            // CARGAR CODIGO INTERNO 

            $codigo_interno = ''.$user->id_sucursal.''.$parametro->SEPARADOR.''.$parametro->ULT_CODIGO_INTERNO;

            /*  --------------------------------------------------------------------------------- */

            // SI EXISTE PRODUCTO 

            $existe = Producto::existeProductoCodigoInterno($codigo_interno);

            if ($existe === true) {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR CODIGO INTERNO 

                $numeracion = $parametro->ULT_CODIGO_INTERNO;

                /*  --------------------------------------------------------------------------------- */

                while($existe) {

                    /*  --------------------------------------------------------------------------------- */

                    // AUMENTAR NUMERACION DEL CODIGO INTERNO 

                    $numeracion = (int)$numeracion + 1;

                    /*  --------------------------------------------------------------------------------- */

                    // CARGAR CODIGO INTERNO 

                    $codigo_interno = ''.$user->id_sucursal.''.$parametro->SEPARADOR.''.$numeracion;

                    /*  --------------------------------------------------------------------------------- */

                    // BUSCAR HASTA QUE NO EXISTA 

                    $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */
            }

            /*  --------------------------------------------------------------------------------- */
        } else {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER NUMERACION 

            $numeracion = (int)substr($producto[0]->CODIGO_INTERNO, -2, 5);

            /*  --------------------------------------------------------------------------------- */

            if($numeracion < 99999) {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR CODIGO INTERNO 

                $codigo_interno = ''.$user->id_sucursal.''.$parametro->SEPARADOR.''.(string)($numeracion + 1).'';

                /*  --------------------------------------------------------------------------------- */

                // SI EXISTE PRODUCTO 

                $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                if ($existe === true) {

                    /*  --------------------------------------------------------------------------------- */

                    while($existe) {

                        /*  --------------------------------------------------------------------------------- */

                        // AUMENTAR NUMERACION DEL CODIGO INTERNO 

                        $numeracion = (int)$numeracion + 1;

                        /*  --------------------------------------------------------------------------------- */

                        // CARGAR CODIGO INTERNO 

                        $codigo_interno = ''.$user->id_sucursal.''.$parametro->SEPARADOR.''.$numeracion;

                        /*  --------------------------------------------------------------------------------- */

                        // BUSCAR HASTA QUE NO EXISTA 

                        $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                        /*  --------------------------------------------------------------------------------- */

                    }

                    /*  --------------------------------------------------------------------------------- */
                }

                /*  --------------------------------------------------------------------------------- */

            } else {

                if ($parametro->SEPARADOR = "-") {

                    /*  --------------------------------------------------------------------------------- */

                    // CARGAR CODIGO INTERNO 

                    $codigo_interno = ''.$user->id_sucursal.'A'.(string)($parametro->ULT_CODIGO_INTERNO + 1).'';

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR PARAMETROS CON EL NUEVO SEPARADOR A

                    Parametro::where('ID_SUCURSAL', $user->id_sucursal)
                    ->update(['SEPARADOR' => 'A']);

                    /*  --------------------------------------------------------------------------------- */

                    $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                    /*  --------------------------------------------------------------------------------- */

                    $numeracion = $parametro->ULT_CODIGO_INTERNO;

                    /*  --------------------------------------------------------------------------------- */

                    if ($existe === true) {

                        /*  --------------------------------------------------------------------------------- */

                        // NUMERACION 

                        $numeracion = (int)$numeracion + 1;

                        /*  --------------------------------------------------------------------------------- */

                        while($existe) {

                            /*  --------------------------------------------------------------------------------- */

                            // AUMENTAR NUMERACION DEL CODIGO INTERNO 

                            $numeracion = (int)$numeracion + 1;

                            /*  --------------------------------------------------------------------------------- */

                            // CARGAR CODIGO INTERNO 

                            $codigo_interno = ''.$user->id_sucursal.''.'A'.''.$numeracion;

                            /*  --------------------------------------------------------------------------------- */

                            // BUSCAR HASTA QUE NO EXISTA 

                            $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                            /*  --------------------------------------------------------------------------------- */

                        }

                        /*  --------------------------------------------------------------------------------- */
                    }

                    /*  --------------------------------------------------------------------------------- */
                } else {
                    $numeracion = (int)substr($producto[0]->CODIGO_INTERNO, -2, 5);

                    /*  --------------------------------------------------------------------------------- */

                    // REVISAR SI EL ULTIMO SEPARADOR SE USA Y LA ULTIMA NUMERCION 

                    if ($parametro->SEPARADOR = "Z" && $numeracion === 99999) {
                        return;
                    }

                    /*  --------------------------------------------------------------------------------- */

                    $codigo_interno = ''.$user->id_sucursal.''.chr(ord($parametro->SEPARADOR)+1).''.$numeracion;

                    /*  --------------------------------------------------------------------------------- */

                    $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                    /*  --------------------------------------------------------------------------------- */

                    $numeracion = $parametro->ULT_CODIGO_INTERNO;

                    /*  --------------------------------------------------------------------------------- */

                    if ($existe === true) {

                        /*  --------------------------------------------------------------------------------- */

                        // NUMERACION 

                        $numeracion = (int)$numeracion + 1;

                        /*  --------------------------------------------------------------------------------- */

                        while($existe) {

                            /*  --------------------------------------------------------------------------------- */

                            // AUMENTAR NUMERACION DEL CODIGO INTERNO 

                            $numeracion = (int)$numeracion + 1;

                            /*  --------------------------------------------------------------------------------- */

                            // CARGAR CODIGO INTERNO 

                            $codigo_interno = ''.$user->id_sucursal.''.chr(ord($parametro->SEPARADOR)+1).''.$numeracion;

                            /*  --------------------------------------------------------------------------------- */

                            // BUSCAR HASTA QUE NO EXISTA 

                            $existe = Producto::existeProductoCodigoInterno($codigo_interno);

                            /*  --------------------------------------------------------------------------------- */

                        }

                        /*  --------------------------------------------------------------------------------- */
                    }

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR SEPARADOR 

                    Parametro::where('ID_SUCURSAL', $user->id_sucursal)
                    ->update(['SEPARADOR' => chr(ord($parametro->SEPARADOR)+1)]);

                    /*  --------------------------------------------------------------------------------- */

                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        return ['codigo_interno' => $codigo_interno];
        
        /*  --------------------------------------------------------------------------------- */

    }

    public static function generar_codigo() {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // GENERAR CODIGO

        $dia = date('YmdHis');
        $codigo = $dia.'-'.$user->id_sucursal;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ['codigo' => $codigo];
        
        /*  --------------------------------------------------------------------------------- */

    }

    public static function guardar($datos)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $datos = $datos["datos"];
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO INTERNO 

        if (Producto::existeProductoCodigoInterno($datos["codigo_interno"]) === true) {

            /*  --------------------------------------------------------------------------------- */

            // GENERAR NUEVAMENTE CODIGO INTERNO 

            $codigo_interno = Producto::generar_ci();

            /*  --------------------------------------------------------------------------------- */

            // REVISAR DE VUELTA SI EXISTO CODIGO INTERNO 

            if (Producto::existeProductoCodigoInterno($codigo_interno["codigo_interno"]) === true) {
                return ["response" => false, "statusText" => "Codigo Interno Existente"];
            } 
            
            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO

        if (Producto::existeProductoTodasSucursales($datos["codigo_producto"]) === true) {

            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER QUE REPITIO PRODUCTO

            return ["response" => false, "statusText" => "Codigo Producto Existente"];
            
            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // REEMPLAZAR IMAGEN

        if ($datos["imagen"] = "/images/SinImagen.png?343637be705e4033f95789ab8ec70808") {
            $datos["imagen"] = "";
        }

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $producto = Producto::insert(
            [
            'CODIGO' => $datos["codigo_producto"],
            'CODIGO_INTERNO' => $datos["codigo_interno"],
            'CODIGO_REAL' => $datos["codigo_real"],
            'DESCRIPCION' => $datos["descripcion"],
            'LINEA' => $datos["categoria"],
            'SUBLINEA' => $datos["subCategoria"],
            'COLOR' => $datos["color"],
            'TELA' => $datos["tela"],
            'TALLE' => $datos["talle"],
            'GENERO' => $datos["genero"],
            'MARCA' => $datos["marca"],
            'PROVEEDOR' => $datos["proveedor"],
            'PRESENTACION' => $datos["presentacion"],
            'IMPUESTO' => $datos["iva"],
            'DESCUENTO' => $datos["descuentoMaximo"],
            'MONEDA' => $datos["moneda"],
            'PREC_VENTA' => $datos["precioVenta"],
            'PREMAYORISTA' => $datos["precioMayorista"],
            'PREVIP' => $datos["precioVip"],
            'PRECOSTO' => $datos["precioCosto"],
            'STOCK_MIN' => $datos["stockMinimo"],
            'OBSERVACION' => $datos["observacion"],
            'BAJA' => "NO",
            'FECALTAS' => date("Y-m-d H:i:s"),
            'HORALTAS' => date("H:i:s"),
            'USER' => $user->id,
            'ID_SUCURSAL' => $user->id_sucursal,
            'GENERADO' => $datos["generado"]
            ]
        );

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if ($producto === true) {
            return ["response" => true];
        }

        /*  --------------------------------------------------------------------------------- */
    }


    public static function obtener_datos($datos)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["codigo"];
        $tipo = $datos["tipo"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        if ($tipo === 1) {
            $filtro = 'PRODUCTOS_AUX.CODIGO';
        } else if ($tipo === 2) {
            $filtro = 'PRODUCTOS_AUX.CODIGO_INTERNO';
        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO,
                          PRODUCTOS_AUX.CODIGO_INTERNO, 
                          PRODUCTOS_AUX.CODIGO_REAL, 
                          PRODUCTOS.DESCRIPCION, 
                          PRODUCTOS.IMPUESTO AS IVA, 
                          PRODUCTOS.LINEA,
                          PRODUCTOS.SUBLINEA,
                          PRODUCTOS_AUX.PROVEEDOR,
                          PRODUCTOS.COLOR,
                          PRODUCTOS.TELA,
                          PRODUCTOS.TALLE,
                          PRODUCTOS.GENERO,
                          PRODUCTOS.MARCA,
                          PRODUCTOS.PRESENTACION,
                          PRODUCTOS.DESCUENTO, 
                          PRODUCTOS_AUX.PREC_VENTA,
                          PRODUCTOS_AUX.PREMAYORISTA,
                          PRODUCTOS_AUX.PREVIP,
                          PRODUCTOS_AUX.PRECOSTO,
                          PRODUCTOS_AUX.STOCK_MIN,
                          PRODUCTOS_AUX.FK_GONDOLA,
                          PRODUCTOS_AUX.OBSERVACION,
                          PRODUCTOS_AUX.MONEDA'))
        ->where($filtro, '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // IMAGEN 

            $imagen = Imagen::obtenerImagen($producto[0]->CODIGO);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "producto" => $producto[0], "imagen" => $imagen["imagen"]];

            /*  --------------------------------------------------------------------------------- */

        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    
}

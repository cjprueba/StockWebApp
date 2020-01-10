<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;
use App\ProductosAux;
use App\Imagen;
use App\Moneda;
use App\Compra;
use App\Transferencia;
use App\Gondola_tiene_Productos;
use App\Ventas_det;
use App\ComprasDet;
use App\Cotizacion;

class Producto extends Model
{

	protected $connection = 'retail';
	protected $table = 'productos';
    public $timestamps = false;
	

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

    public static function guardar_modificar($datos){

        /*  --------------------------------------------------------------------------------- */

        // DEPENDIENDO DE LA OPCION QUE VENGA GUARDAR O MODIFICAR 

        if ($datos["datos"]["modificar"] === true) {
            return Producto::modificar($datos);
        } else {
            return Producto::guardar($datos);
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function modificar($datos){

        /*  --------------------------------------------------------------------------------- */
        // IMPORTANTE
        // AGREGAR FK USER A LA TABLA PRODUCTOS_AUX 
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $datos = $datos["datos"];
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO INTERNO 

        if (Producto::existeProductoCodigoInterno($datos["codigo_interno"]) === false) {

            /*  --------------------------------------------------------------------------------- */

            // REVISAR DE VUELTA SI EXISTO CODIGO INTERNO 

            return ["response" => false, "statusText" => "Este codigo interno no existe"];
            
            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO

        if (Producto::existeProductoTodasSucursales($datos["codigo_producto"]) === false) {

            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER QUE REPITIO PRODUCTO

            return ["response" => false, "statusText" => "Este producto no existe"];
            
            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // REEMPLAZAR IMAGEN

        if ($datos["imagen"] = "/images/SinImagen.png?343637be705e4033f95789ab8ec70808") {
            $datos["imagen"] = "";
        }

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $producto = Producto::where('CODIGO', '=', $datos["codigo_producto"])
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->update(
            [
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
            'FECMODIF' => date("Y-m-d H:i:s"),
            'HORMODIF' => date("H:i:s"),
            'USERM' => $user->id,
            'GENERADO' => $datos["generado"],
            'VENCIMIENTO' => $datos["vencimiento"]
            ]
        );

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR GONDOLA

        //Gondola_tiene_Productos::asignar_gondolas($datos["codigo_producto"], $datos["gondola"]);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];
        

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
            'GENERADO' => $datos["generado"],
            'VENCIMIENTO' => $datos["vencimiento"]
            ]
        );

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR GONDOLA

        Gondola_tiene_Productos::asignar_gondolas($datos["codigo_producto"], $datos["gondola"]);

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
                          PRODUCTOS_AUX.MONEDA,
                          0 AS GONDOLAS,
                          0 AS AUTODESCRIPCION'))
        ->where($filtro, '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();
            
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS 

        $gondola = Gondola::obtener_gondolas_por_producto($codigo);
        
        $producto[0]["GONDOLAS"] = $gondola['gondolas'];
        $producto[0]["AUTODESCRIPCION"] = false;

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


    public static function productos_datatable($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 =>'CODIGO', 
                            1 =>'DESCRIPCION',
                            2=> 'PREC_VENTA',
                            3=> 'PRECOSTO',
                            4=> 'PREMAYORISTA',
                            5=> 'STOCK',
                            6=> 'IMAGEN',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = ProductosAux::where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                     ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $imagen_producto = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                            ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                             ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)   
                             ->where(function ($query) use ($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                             })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->CODIGO)
                ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:60px;height:60px;'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    public static function producto_detalle($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $dato = [];

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR IMAGEN

        $imagen = Imagen::obtenerImagen($data['codigo']);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRODUCTO

        $producto = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('LINEAS', 'PRODUCTOS.LINEA', '=', 'LINEAS.CODIGO')
        ->leftjoin('SUBLINEAS', 'PRODUCTOS.SUBLINEA', '=', 'SUBLINEAS.CODIGO')
        ->leftjoin('COLORES', 'PRODUCTOS.COLOR', '=', 'COLORES.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO_INTERNO, 
                PRODUCTOS.DESCRIPCION, 
                LINEAS.DESCRIPCION AS CATEGORIA,
                SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
                COLORES.DESCRIPCION AS COLOR,
                PRODUCTOS.IMPUESTO,
                PRODUCTOS.PRESENTACION,
                PRODUCTOS_AUX.MONEDA,
                PRODUCTOS_AUX.DESCUENTO,
                PRODUCTOS_AUX.OBSERVACION,
                PRODUCTOS_AUX.PREC_VENTA, 
                PRODUCTOS_AUX.PREMAYORISTA,
                PRODUCTOS_AUX.PROVEEDOR,
                PRODUCTOS_AUX.PREVIP,
                PRODUCTOS_AUX.PRECOSTO,
                PRODUCTOS_AUX.FECALTAS,
                PRODUCTOS_AUX.FECMODIF,
                PRODUCTOS_AUX.FECHULT_C,
                PRODUCTOS_AUX.FECHULT_V,
                PRODUCTOS_AUX.STOCK_MIN'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $data['codigo'])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER EL ARRAY 

        foreach ($producto as $key => $value) {

            $dato["CODIGO_INTERNO"] = $value->CODIGO_INTERNO;
            $dato["DESCRIPCION"] = $value->DESCRIPCION;
            $dato["CATEGORIA"] = $value->CATEGORIA;
            $dato["SUBCATEGORIA"] = $value->SUBCATEGORIA;
            $dato["COLOR"] = $value->COLOR;
            $dato["IMPUESTO"] = $value->IMPUESTO."%";
            $dato["PRESENTACION"] = $value->PRESENTACION;
            
            /*  --------------------------------------------------------------------------------- */

            // DESCRIPCION MONEDA 

            $moneda = Moneda::select(DB::raw('DESCRIPCION_LARGA'))
            ->where("CODIGO","=",$value->MONEDA)
            ->get();

            $dato["MONEDA"] = $moneda[0]["DESCRIPCION_LARGA"];

            /*  --------------------------------------------------------------------------------- */

            // OBERVACION 
            
            if ($value->OBSERVACION !== NULL AND $value->OBSERVACION !== "") {
                $dato["OBSERVACION"] = $value->OBSERVACION;
            } else {
                $dato["OBSERVACION"] = "No hay observaciones";
            }

            /*  --------------------------------------------------------------------------------- */

            $dato["DESCUENTO"] = $value->DESCUENTO."%";

            /*  --------------------------------------------------------------------------------- */

            // PRECIOS 

            $dato["PREC_VENTA"] = Common::precio_candec($value->PREC_VENTA, $value->MONEDA);
            $dato["PREMAYORISTA"] = Common::precio_candec($value->PREMAYORISTA, $value->MONEDA);
            $dato["PREVIP"] = Common::precio_candec($value->PREVIP, $value->MONEDA);
            $dato["PRECOSTO"] = Common::precio_candec($value->PRECOSTO, $value->MONEDA);

            
            /*  --------------------------------------------------------------------------------- */

            $dato["FECALTAS"] = date('Y-m-d', strtotime($value->FECALTAS));
            $dato["FECMODIF"] = date('Y-m-d', strtotime($value->FECMODIF));
            $dato["FECHULT_C"] = date('Y-m-d', strtotime($value->FECHULT_C));
            $dato["FECHULT_V"] = date('Y-m-d', strtotime($value->FECHULT_V));
            $dato["STOCK"] = $value->STOCK;
            $dato["STOCK_MIN"] = $value->STOCK_MIN;

            /*  --------------------------------------------------------------------------------- */

            // PARAMETRO MAYORISTA

            $mayorista = Parametro::consultaPersonalizada('DESTINO');
            $dato["CANT_MAYORISTA"] = $mayorista->DESTINO;

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $dato, 'imagen' => $imagen["imagen"]];
        } else {
            return ['producto' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function producto_proveedor($datos)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["codigo"];
        $data = [];
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = Compra::
        leftjoin('COMPRASDET', 'COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
        ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
        ->select(DB::raw('COUNT(*) AS CANTIDAD_COMPRA, COMPRAS.PROVEEDOR, PROVEEDORES.NOMBRE, SUM(COMPRASDET.CANTIDAD) AS CANTIDAD, COMPRAS.FECALTAS'))
        ->where([
            ['COMPRASDET.COD_PROD', '=', $codigo],
            ['COMPRAS.ID_SUCURSAL', '=', $user->id_sucursal],
        ])
        ->groupBy('COMPRAS.PROVEEDOR')
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // SE OBTIENE LA SUMA DE LOS TOTALES POR MONEDA 
        // ES AGRUPADO POR PROVEEDOR 
        // ES AGRUPADO POR MONEDA 

        $monedas = Compra::
        leftjoin('COMPRASDET', 'COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
        ->select(DB::raw('COMPRAS.PROVEEDOR, COMPRAS.MONEDA, SUM(COMPRAS.TOTAL) AS TOTAL'))
        ->where([
            ['COMPRASDET.COD_PROD', '=', $codigo],
            ['COMPRAS.ID_SUCURSAL', '=', $user->id_sucursal],
        ])
        ->groupBy('COMPRAS.PROVEEDOR')
        ->groupBy('COMPRAS.MONEDA')
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        foreach ($producto as $key => $value) {

            $c = $c + 1;
            $data[$value->PROVEEDOR]['C'] = $c;
            $data[$value->PROVEEDOR]['PROVEEDOR'] = $value->PROVEEDOR;
            $data[$value->PROVEEDOR]['CANTIDAD_COMPRA'] = $value->CANTIDAD_COMPRA;
            $data[$value->PROVEEDOR]['NOMBRE'] = $value->NOMBRE;
            $data[$value->PROVEEDOR]['CANTIDAD'] = $value->CANTIDAD;
            $data[$value->PROVEEDOR]['GUARANIES'] = 0;
            $data[$value->PROVEEDOR]['DOLARES'] = 0;
            $data[$value->PROVEEDOR]['PESOS'] = 0;
            $data[$value->PROVEEDOR]['REALES'] = 0;
            

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA ULTIMA FECHA COMPRA 

            $creacion = Compra::
            leftjoin('COMPRASDET', 'COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
            ->select(DB::raw('SUBSTR(COMPRAS.FECALTAS, 1,11) AS FECALTAS'))
            ->where([
                ['COMPRASDET.COD_PROD', '=', $codigo],
                ['COMPRAS.ID_SUCURSAL', '=', $user->id_sucursal],
                ['COMPRAS.PROVEEDOR', '=', $value->PROVEEDOR],
            ])
            ->orderBy('COMPRAS.ID', 'desc')
            ->limit(1)
            ->get();

            $data[$value->PROVEEDOR]['FECALTAS'] = $creacion[0]->FECALTAS;

            /*  --------------------------------------------------------------------------------- */

        }

        foreach ($monedas as $key => $value) {
            if (array_key_exists($value->PROVEEDOR, $data)) {
                if ($value->MONEDA === 1) {
                    $data[$value->PROVEEDOR]['GUARANIES'] = Common::precio_candec($value->TOTAL, 1);
                } else if ($value->MONEDA === 2) {
                    $data[$value->PROVEEDOR]['DOLARES'] = Common::precio_candec($value->TOTAL, 2);
                } else if ($value->MONEDA === 3) {
                    $data[$value->PROVEEDOR]['PESOS'] = Common::precio_candec($value->TOTAL, 3);
                } else if ($value->MONEDA === 4) {
                    $data[$value->PROVEEDOR]['REALES'] = Common::precio_candec($value->TOTAL, 4);
                }
            }
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "proveedor" => $data];

            /*  --------------------------------------------------------------------------------- */

        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function transferencias_enviadas_producto($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];
        $data = [];

        /*  --------------------------------------------------------------------------------- */

        $transferencia = Transferencia::leftjoin('TRANSFERENCIAS_DET', 'TRANSFERENCIAS_DET.CODIGO', '=','TRANSFERENCIAS.CODIGO')
        ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=','TRANSFERENCIAS.SUCURSAL_ORIGEN')
        ->select(DB::raw('COUNT(*) AS CANTIDAD_TRANSFERENCIA, TRANSFERENCIAS.SUCURSAL_DESTINO, SUCURSALES.DESCRIPCION, TRANSFERENCIAS_DET.CODIGO_PROD, SUM(TRANSFERENCIAS_DET.CANTIDAD) AS CANTIDAD, SUM(TRANSFERENCIAS_DET.TOTAL) AS TOTAL'))
        ->where([
            ['TRANSFERENCIAS_DET.CODIGO_PROD', '=', $codigo],
            ['TRANSFERENCIAS.SUCURSAL_ORIGEN', '=', $user->id_sucursal]
        ])
        ->groupBy('TRANSFERENCIAS.SUCURSAL_DESTINO')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($transferencia as $key => $value) {

            $monedas = Transferencia::leftjoin('TRANSFERENCIAS_DET', 'TRANSFERENCIAS_DET.CODIGO', '=','TRANSFERENCIAS.CODIGO')
            ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=','TRANSFERENCIAS.SUCURSAL_ORIGEN')
            ->select(DB::raw('TRANSFERENCIAS.MONEDA, SUM(TRANSFERENCIAS_DET.TOTAL) AS TOTAL'))
            ->where([
                ['TRANSFERENCIAS_DET.CODIGO_PROD', '=', $codigo], 
                ['TRANSFERENCIAS.SUCURSAL_DESTINO', '=', $value->SUCURSAL_DESTINO],
                ['TRANSFERENCIAS.SUCURSAL_ORIGEN', '=', $user->id_sucursal]
            ])
            ->groupBy('TRANSFERENCIAS.MONEDA')
            ->get();

            $data[$key]['CANTIDAD_TRANSFERENCIA'] = $value->CANTIDAD_TRANSFERENCIA;
            $data[$key]['CANTIDAD'] = $value->CANTIDAD;
            $data[$key]['DESCRIPCION'] = $value->DESCRIPCION;
            $data[$key]['GUARANIES'] = 0;
            $data[$key]['DOLARES'] = 0;
            $data[$key]['PESOS'] = 0;
            $data[$key]['REALES'] = 0;

            foreach ($monedas as $key_moneda => $valor) {

               if ($valor->MONEDA === 1) {
                    $data[$key]['GUARANIES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 2){
                    $data[$key]['DOLARES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 3){
                    $data[$key]['PESOS'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 4){
                    $data[$key]['REALES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } 
               
            }
            
        }   

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function transferencias_importados_producto($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];
        $data = [];
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        $transferencia = Transferencia::leftjoin('TRANSFERENCIAS_DET', 'TRANSFERENCIAS_DET.CODIGO', '=','TRANSFERENCIAS.CODIGO')
        ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=','TRANSFERENCIAS.SUCURSAL_ORIGEN')
        ->select(DB::raw('0 AS C, COUNT(*) AS CANTIDAD_TRANSFERENCIA, TRANSFERENCIAS.SUCURSAL_ORIGEN, SUCURSALES.DESCRIPCION, TRANSFERENCIAS_DET.CODIGO_PROD, SUM(TRANSFERENCIAS_DET.CANTIDAD) AS CANTIDAD, SUM(TRANSFERENCIAS_DET.TOTAL) AS TOTAL'))
        ->where([
            ['TRANSFERENCIAS_DET.CODIGO_PROD', '=', $codigo],
            ['TRANSFERENCIAS.SUCURSAL_DESTINO', '=', $user->id_sucursal]
        ])
        ->groupBy('TRANSFERENCIAS.SUCURSAL_ORIGEN')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($transferencia as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // NUMERACION 

            $c++;
            $data[$key]['C'] = $c;

            /*  --------------------------------------------------------------------------------- */
            
            $monedas = Transferencia::leftjoin('TRANSFERENCIAS_DET', 'TRANSFERENCIAS_DET.CODIGO', '=','TRANSFERENCIAS.CODIGO')
            ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=','TRANSFERENCIAS.SUCURSAL_ORIGEN')
            ->select(DB::raw('TRANSFERENCIAS.MONEDA, SUM(TRANSFERENCIAS_DET.TOTAL) AS TOTAL'))
            ->where([
                ['TRANSFERENCIAS_DET.CODIGO_PROD', '=', $codigo], 
                ['TRANSFERENCIAS.SUCURSAL_ORIGEN', '=', $value->SUCURSAL_ORIGEN],
                ['TRANSFERENCIAS.SUCURSAL_DESTINO', '=', $user->id_sucursal]
            ])
            ->groupBy('TRANSFERENCIAS.MONEDA')
            ->get();

            $data[$key]['CANTIDAD_TRANSFERENCIA'] = $value->CANTIDAD_TRANSFERENCIA;
            $data[$key]['CANTIDAD'] = $value->CANTIDAD;
            $data[$key]['DESCRIPCION'] = $value->DESCRIPCION;
            $data[$key]['GUARANIES'] = 0;
            $data[$key]['DOLARES'] = 0;
            $data[$key]['PESOS'] = 0;
            $data[$key]['REALES'] = 0;

            foreach ($monedas as $key_moneda => $valor) {

               if ($valor->MONEDA === 1) {
                    $data[$key]['GUARANIES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 2){
                    $data[$key]['DOLARES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 3){
                    $data[$key]['PESOS'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } else if ($valor->MONEDA === 4){
                    $data[$key]['REALES'] = Common::precio_candec($valor->TOTAL, $valor->MONEDA);
               } 
               
            }
            
        }   

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function producto_transferencia($data)
    {
        $enviadas = Producto::transferencias_enviadas_producto($data);
        $importados = Producto::transferencias_importados_producto($data);
        return ["response" => true, "enviadas" => $enviadas, "importados" => $importados];

    }

    public static function eliminar($data) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI HAY VENTAS CON ESTE CODIGO 

        $ventas_det = Ventas_det::
        where([
            ['COD_PROD', '=', $codigo]
        ])
        ->limit(1)
        ->get();

        if (count($ventas_det) > 0) {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI HAY COMPRAS CON ESTE CODIGO 

        $compras_det = ComprasDet::
        where([
            ['COD_PROD', '=', $codigo]
        ])
        ->limit(1)
        ->get();

        if (count($compras_det) > 0) {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR PRODUCTO AUX

        ProductosAux::where([
            ['CODIGO', '=', $codigo]
        ])
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR PRODUCTO

        Producto::where([
            ['CODIGO', '=', $codigo]
        ])
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */
    }    
}

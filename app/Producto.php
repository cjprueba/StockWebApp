<?php

namespace App;


use vendor\autoload;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Automattic\WooCommerce\Client;
use App\Common;
use App\Parametro;
use App\ProductosAux;
use App\Imagen;
use App\ImagenesWeb;
use App\Moneda;
use App\Compra;
use App\Transferencia;
use App\Gondola_tiene_Productos;
use App\Ventas_det;
use App\ComprasDet;
use App\Cotizacion;
use App\LineasDescuento;
use App\Categoria;

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
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS_AUX.CODIGO_INTERNO,PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA,PRODUCTOS_AUX.DESCUENTO,PRODUCTOS_AUX.PREMAYORISTA, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
    	DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $cod_prod)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */
$lotes= DB::connection('retail')
        ->table('Lotes')->select(DB::raw('ID,CANTIDAD,LOTE'))
        ->where('COD_PROD', '=', $cod_prod)
        ->where('id_sucursal', '=', $user->id_sucursal)
        ->get();
        // CONSEGUIR COTIZACION 

        foreach ($producto as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // Cotizacion
            
            $cotizacion = Cotizacion::CALMONED([
                                'monedaProducto' => $value->MONEDA,
                                'monedaSistema' => $data['data']['monedaSistema'],
                                'precio' => $value->PREC_VENTA,
                                'tab_unica' => $data['data']['tab_unica'],
                                'decSistema' => $data['data']['candec'],
                                "id_sucursal" => $user->id_sucursal]);

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */
       
        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['producto' => $producto[0], 'valor' => $cotizacion["valor"],'lote' => $lotes];
        } else {
            return ['producto' => 0,'lote' => 0];
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
        ->whereRaw('CHAR_LENGTH(PRODUCTOS.CODIGO_INTERNO) = 7')
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
			//var_dump("entre");
			//var_dump($producto[0]->CODIGO_INTERNO);
            $numeracion = (int)substr($producto[0]->CODIGO_INTERNO, -5, 10);
			//var_dump($numeracion);
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

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true 
            ]
        );


        /*  --------------------------------------------------------------------------------- */
        // IMPORTANTE
        // AGREGAR FK USER A LA TABLA PRODUCTOS_AUX 
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $datos["datos"]["imagen"]);
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
        ->update(
            [
            'CODIGO_REAL' => $datos["codigo_real"],
            'DESCRIPCION' => $datos["descripcion"],
            'LINEA' => $datos["categoria"],
            'SUBLINEA' => $datos["subCategoria"],
            'SUBLINEADET' => $datos["subCategoriaDet"],
            'COLOR' => $datos["color"],
            'TELA' => $datos["tela"],
            'TALLE' => $datos["talle"],
            'GENERO' => $datos["genero"],
            'MARCA' => $datos["marca"],
            'PROVEEDOR' => $datos["proveedor"],
            'PRESENTACION' => $datos["presentacion"],
            'IMPUESTO' => $datos["iva"],
            'DESCUENTO' => $datos["descuentoMaximo"],
            'PORCENTAJE' => 0,
            'MONEDA' => $datos["moneda"],
            'PREC_VENTA' => Common::quitar_coma($datos["precioVenta"], 2),
            'PREMAYORISTA' => Common::quitar_coma($datos["precioMayorista"], 2),
            'PREVIP' => Common::quitar_coma($datos["precioVip"], 2),
            'PRECOSTO' => Common::quitar_coma($datos["precioCosto"], 2),
            'STOCK_MIN' => $datos["stockMinimo"],
            'OBSERVACION' => $datos["observacion"],
            'BAJA' => "NO",
            'FECMODIF' => date("Y-m-d H:i:s"),
            'HORMODIF' => date("H:i:s"),
            'USERM' => $user->id,
            'GENERADO' => $datos["generado"],
            'PERIODO' => $datos["periodo"],
            'TEMPORADA' => $datos["temporada"],
            'VENCIMIENTO' => $datos["vencimiento"],
            'ONLINE' => $datos["online"],
            'ID_SUCURSAL' => $user->id_sucursal
            ]
        );

        //ACTUALIZAR EN PRODUCTOS AUX

        $pro_aux = ProductosAux::where('CODIGO', '=', $datos["codigo_producto"])
                    ->update(['ONLINE' => $datos["online"]]);

        //OBTENER ID WEB DE CATEGORIA Y SUBCATEGORIA

        $cate=DB::connection('retail')->table('LINEAS')->select(DB::raw('ID_WEB'))
                    ->where('LINEAS.CODIGO', '=', $datos["categoria"])
                    ->get()
                    ->toArray();

        $subcate=DB::connection('retail')->table('SUBLINEAS')->select(DB::raw('ID_WEB'))
                    ->where('SUBLINEAS.CODIGO', '=', $datos["subCategoria"])
                    ->get()
                    ->toArray();

        if($datos["online"] == 1){

            //BUSCAR PRODUCTO EN BASE DE DATOS WEB SI EXISTE

            if($datos["longitudWeb"]=="0.00" || $datos["longitudWeb"]=="0"){
                $datos["longitudWeb"]="";
            }

            if($datos["anchoWeb"]=="0.00" || $datos["longitudWeb"]=="0"){
                $datos["anchoWeb"]="";
            }
               
            if($datos["alturaWeb"]=="0.00" || $datos["longitudWeb"]=="0"){
                $datos["alturaWeb"]="";
            }

            $item=$woocommerce->get('products',$parameters=["sku"=>$datos["codigo_interno"]]);

            //ACTUALIZACION DE LOS PRODUCTOS EN WEB

            if(count($item)>0){

                //CARGA DATOS EN UNA VARIABLE

                $data = [
                    'name'=>$datos["nombreWeb"],
                    'price'=> $datos["precioVenta"],
                    'regular_price'=> $datos["precioVenta"],
                    'description'=> $datos["descripcionWeb"],
                    'categories'=> [
                        [
                        'id'=> $cate["0"]->ID_WEB
                        ],
                        [
                        'id'=> $subcate["0"]->ID_WEB
                        ]

                    ],
                    'weight'=> $datos["pesoWeb"],
                    'dimensions'=> [
                        'length' => $datos["longitudWeb"],
                        'width' => $datos["anchoWeb"],
                        'height' => $datos["alturaWeb"],
                    ]
                ];

                //ACTUALIZACION DE LOS PRODUCTOS EN WOOCOMMERCE

                $woocommerce->put('products/'.$item["0"]->id.'',$data);
    
            }else{

                //OBTENER EL STOCK DEL PRODUCTO

                $cantidad = DB::connection('retail')->table('LOTES')->select(DB::raw('sum(CANTIDAD) as CANTIDAD'))
                            ->where('LOTES.ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where('LOTES.COD_PROD', '=', $datos["codigo_producto"])
                            ->groupBy("LOTES.COD_PROD")
                            ->get()
                            ->toArray();

                //CARGAR DATOS EN UNA VARIABLE

                $data = [
                    'sku' => $datos["codigo_interno"],
                    'name'=>$datos["nombreWeb"],
                    'regular_price'=> $datos["precioVenta"],
                    'price'=> $datos["precioVenta"],
                    'description'=> $datos["descripcionWeb"],
                    'stock_quantity' => $cantidad["0"]->CANTIDAD,
                    'categories'=> [
                        [
                            'id'=> $cate["0"]->ID_WEB
                        ],
                        [
                            'id'=> $subcate["0"]->ID_WEB
                        ]

                    ],
                    'weight'=> $datos["pesoWeb"],
                    'dimensions'=> [
                        'length' => $datos["longitudWeb"],
                        'width' => $datos["anchoWeb"],
                        'height' => $datos["alturaWeb"]
                    ]
                ];

                //CARGAR EL PRODUCTO EN WOOCOMMERCE

                $woocommerce->post('products', $data);

            }
            
            /*  --------------------------------------------------------------------------------- */

        }

         //ACTUALIZACION DE LOS PRODUCTOS EN DB DETALLE_WEB

        if($datos["detalleWeb"] == true){

             //BUSCAR CODIGO INTERNO

            $interno=DB::connection('retail')->table('DETALLE_WEB')->select(DB::raw('ID'))
                    ->where('CODIGO_INTERNO', '=', $datos["codigo_interno"])
                    ->get()
                    ->toArray();

             //SI ENCUENTRA CODIGO INTERNO ACTUALIZA

            if($interno!=NULL || count($interno)>0){

                $actualizar = DB::connection('retail')->table('DETALLE_WEB')->where('ID_SUCURSAL', $user->id_sucursal)
                                ->where('CODIGO_INTERNO', $datos["codigo_interno"])
                                ->update(
                                    ['NOMBRE' => $datos["nombreWeb"]], 
                                    ['DESCRIPCION' => $datos["descripcionWeb"]], 
                                    ['CATEGORIA_1' => $datos["categoria1Web"]], 
                                    ['CATEGORIA_2' => $datos["categoria2Web"]], 
                                    ['PESO' => $datos["pesoWeb"]],
                                    ['HABILITADO' => $datos["habilitadoWeb"]],
                                    ['LONGITUD' => $datos["longitudWeb"]],
                                    ['ANCHURA' => $datos["anchoWeb"]],
                                    ['ALTURA' => $datos["alturaWeb"]],
                                    ['MARCA' => $datos["marcaWeb"]],
                                    ['ID_WEB_LINEA' => $cate["0"]->ID_WEB],
                                    ['ID_WEB_SUBLINEA' => $subcate["0"]->ID_WEB]
                                );    

            }else{

                //CARGA LOS DATOS EN LA DB

                $cargar=DB::connection('retail')->table('DETALLE_WEB')
                                ->insertGetId([
                                    'CODIGO_INTERNO' => $datos["codigo_interno"],
                                    'NOMBRE' => $datos["nombreWeb"], 
                                    'DESCRIPCION' => $datos["descripcionWeb"], 
                                    'CATEGORIA_1' => $datos["categoria1Web"], 
                                    'CATEGORIA_2' => $datos["categoria2Web"], 
                                    'PESO' => $datos["pesoWeb"],
                                    'HABILITADO' => $datos["habilitadoWeb"],
                                    'LONGITUD' => $datos["longitudWeb"],
                                    'ANCHURA' => $datos["anchoWeb"],
                                    'ALTURA' => $datos["alturaWeb"],
                                    'MARCA' => $datos["marcaWeb"],
                                    'ID_SUCURSAL' => $user->id_sucursal,
                                    'ID_WEB_LINEA' => $cate["0"]->ID_WEB,
                                    'ID_WEB_SUBLINEA' => $subcate["0"]->ID_WEB]
                                );
            }
        }

        // INSERTAR IMAGEN 
        
        if ($img !== "") { 
            Imagen::guardar_imagen_storage([
                'COD_PROD' => $datos["codigo_producto"],
                'CODIGO_INTERNO' => $datos["codigo_interno"],
                'PICTURE' => $img
            ]);
        }

        /*  --------------------------------------------------------------------------------- */
        
        // INSERTAR GONDOLA

        Gondola_tiene_Productos::modificar_asignar_gondolas($datos["codigo_producto"], $datos["gondola"]);

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

        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $datos["datos"]["imagen"]);
        $datos = $datos["datos"];
        $blob = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO INTERNO 
		
        if (Producto::existeProductoCodigoInterno($datos["codigo_interno"]) === true) {
			
            /*  --------------------------------------------------------------------------------- */

            // GENERAR NUEVAMENTE CODIGO INTERNO 

            $codigo_interno = Producto::generar_ci();
			$datos["codigo_interno"] = $codigo_interno["codigo_interno"];
			
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

        // OBTENER CANDEC

        $candec = (Parametro::candec($datos["moneda"]))['CANDEC'];

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
            'SUBLINEADET' => $datos["subCategoriaDet"],
            'COLOR' => $datos["color"],
            'TELA' => $datos["tela"],
            'TALLE' => $datos["talle"],
            'GENERO' => $datos["genero"],
            'MARCA' => $datos["marca"],
            'PROVEEDOR' => $datos["proveedor"],
            'PRESENTACION' => $datos["presentacion"],
            'IMPUESTO' => $datos["iva"],
            'DESCUENTO' => $datos["descuentoMaximo"],
            'PORCENTAJE' => 0,
            'MONEDA' => $datos["moneda"],
            'PREC_VENTA' => Common::quitar_coma($datos["precioVenta"], 2),
            'PREMAYORISTA' => Common::quitar_coma($datos["precioMayorista"], 2),
            'PREVIP' => Common::quitar_coma($datos["precioVip"], 2),
            'PRECOSTO' => Common::quitar_coma($datos["precioCosto"], 2),
            'STOCK_MIN' => $datos["stockMinimo"],
            'OBSERVACION' => $datos["observacion"],
            'BAJA' => "NO",
            'FECALTAS' => date("Y-m-d H:i:s"),
            'HORALTAS' => date("H:i:s"),
            'USER' => $user->id,
            'ID_SUCURSAL' => $user->id_sucursal,
            'GENERADO' => $datos["generado"],
            'VENCIMIENTO' => $datos["vencimiento"],
            'TEMPORADA' => $datos["temporada"],
            'PERIODO' => $datos["periodo"]
            ]
        );

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR IMAGEN 
        
        if ($img !== "") { 
            Imagen::guardar_imagen_storage([
                'COD_PROD' => $datos["codigo_producto"],
                'CODIGO_INTERNO' => $datos["codigo_interno"],
                'PICTURE' => $img
            ]);
        }

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

    public static function obtener_datos($datos){

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
                          PRODUCTOS.SUBLINEADET,
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
                          PRODUCTOS_AUX.OBSERVACION,
                          PRODUCTOS_AUX.MONEDA,
                          0 AS GONDOLAS,
                          0 AS AUTODESCRIPCION,
                          PRODUCTOS.PERIODO, 
                          PRODUCTOS.TEMPORADA,
                          PRODUCTOS_AUX.ONLINE,
                          PRODUCTOS.VENCIMIENTO'))
        ->where($filtro, '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();


        // OBTENER DATOS WEB

       

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER GONDOLAS 

            $gondola = Gondola::obtener_gondolas_por_producto($codigo);
            
            $producto[0]["GONDOLAS"] = $gondola['gondolas'];
            $producto[0]["AUTODESCRIPCION"] = false;

            /*  --------------------------------------------------------------------------------- */
                $online= DB::connection('retail')
                ->table('DETALLE_WEB')
                ->select(DB::raw('NOMBRE, DESCRIPCION, PESO, ALTURA, ANCHURA, LONGITUD'))
                ->where("CODIGO_INTERNO","=", $producto[0]["CODIGO_INTERNO"])
                ->get()->toArray();
            // IMAGEN 

            $imagen = Imagen::obtenerImagenURL($codigo);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
            if(count($online)== 0){
               // var_dump("entre 1");

                return ["response" => true, "producto" => $producto[0], "online" => 0, "imagen" => $imagen["imagen"],'existe'=>true];
            }else{
               // var_dump("entre 2");
                return ["response" => true, "producto" => $producto[0], "online" => $online[0], "imagen" => $imagen["imagen"],'existe'=>true];
            }

            

            /*  --------------------------------------------------------------------------------- */

        } else {
             $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where($filtro, '=', $codigo)
        ->limit(1)
        ->get()->toArray();

        if(count($producto)>0){
          return ["response" => false,'existe'=>true];
        }

            return ["response" => false,'existe'=>false];
        }

        /*  --------------------------------------------------------------------------------- */

    }
     public static function importar_producto($datos){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["codigo"];
      $tipo=1;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        if ($tipo === 1) {
            $filtro = 'PRODUCTOS_AUX.CODIGO';
        } 
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto_aux = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO,
                          PRODUCTOS_AUX.CODIGO_INTERNO, 
                          PRODUCTOS_AUX.CODIGO_REAL, 
                          PRODUCTOS.DESCRIPCION, 
                          PRODUCTOS.IMPUESTO AS IVA, 
                          PRODUCTOS.LINEA,
                          PRODUCTOS.SUBLINEA,
                          PRODUCTOS.SUBLINEADET,
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
                          PRODUCTOS_AUX.OBSERVACION,
                          PRODUCTOS_AUX.MONEDA,
                          0 AS GONDOLAS,
                          0 AS AUTODESCRIPCION,
                          PRODUCTOS.PERIODO, 
                          PRODUCTOS.TEMPORADA,
                          PRODUCTOS_AUX.ONLINE,
                          PRODUCTOS.VENCIMIENTO'))
        ->where($filtro, '=', $codigo)
        ->limit(1)
        ->get()->toArray();


           $producto = ProductosAux::insert(
            [
            'CODIGO' => $producto_aux[0]["CODIGO"],
            'CODIGO_INTERNO' => $producto_aux[0]["CODIGO_INTERNO"],
            'CODIGO_REAL' => $producto_aux[0]["CODIGO_REAL"],
            'PROVEEDOR' => $producto_aux[0]["PROVEEDOR"],
            'DESCUENTO' => $producto_aux[0]["DESCUENTO"],
            'MONEDA' => $producto_aux[0]["MONEDA"],
            'PREC_VENTA' => $producto_aux[0]["PREC_VENTA"],
            'PREMAYORISTA' => $producto_aux[0]["PREMAYORISTA"],
            'PREVIP' => $producto_aux[0]["PREVIP"],
            'PRECOSTO' => $producto_aux[0]["PRECOSTO"],
            'STOCK_MIN' => $producto_aux[0]["STOCK_MIN"],
            'OBSERVACION' => $producto_aux[0]["OBSERVACION"],
            'BAJA' => "NO",
            'FECALTAS' => date("Y-m-d H:i:s"),
            'HORALTAS' => date("H:i:s"),
            'USER' => $user->id,
            'ID_SUCURSAL' => $user->id_sucursal,
            ]
        );
           //OBTENER PRODUCTO IMPORTADO
           $producto = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO,
                          PRODUCTOS_AUX.CODIGO_INTERNO, 
                          PRODUCTOS_AUX.CODIGO_REAL, 
                          PRODUCTOS.DESCRIPCION, 
                          PRODUCTOS.IMPUESTO AS IVA, 
                          PRODUCTOS.LINEA,
                          PRODUCTOS.SUBLINEA,
                          PRODUCTOS.SUBLINEADET,
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
                          PRODUCTOS_AUX.OBSERVACION,
                          PRODUCTOS_AUX.MONEDA,
                          0 AS GONDOLAS,
                          0 AS AUTODESCRIPCION,
                          PRODUCTOS.PERIODO, 
                          PRODUCTOS.TEMPORADA,
                          PRODUCTOS_AUX.ONLINE,
                          PRODUCTOS.VENCIMIENTO'))
        ->where($filtro, '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();
        // OBTENER DATOS WEB

        $online= DB::connection('retail')
                ->table('DETALLE_WEB')
                ->select(DB::raw('NOMBRE, DESCRIPCION, PESO, ALTURA, ANCHURA, LONGITUD'))
                ->where("CODIGO_INTERNO","=", $producto_aux[0]["CODIGO_INTERNO"])
                ->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER GONDOLAS 

            $gondola = Gondola::obtener_gondolas_por_producto($codigo);
            
            $producto[0]["GONDOLAS"] = $gondola['gondolas'];
            $producto[0]["AUTODESCRIPCION"] = false;

            /*  --------------------------------------------------------------------------------- */

            // IMAGEN 

            $imagen = Imagen::obtenerImagen($codigo);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
            if(count($online)== 0){
               // var_dump("entre 1");

                return ["response" => true, "producto" => $producto[0], "online" => 0, "imagen" => $imagen["imagen"]];
            }else{
               // var_dump("entre 2");
                return ["response" => true, "producto" => $producto[0], "online" => $online[0], "imagen" => $imagen["imagen"]];
            }

            

            /*  --------------------------------------------------------------------------------- */

        } else {

            return ["response" => false,'statusText'=>'Ha ocurrido un error!. Comuniquese con los supervisores del sistema'];
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
                            2 => 'CATEGORIA',
                            3 => 'PREC_VENTA',
                            4 => 'PRECOSTO',
                            5 => 'PREMAYORISTA',
                            6 => 'STOCK',
                            7 => 'IMAGEN',
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

        if(empty($request->input('search.value')) && empty($request->input('columns.0.search.value')) && empty($request->input('columns.1.search.value')) && empty($request->input('columns.2.search.value')) && empty($request->input('columns.3.search.value')) && empty($request->input('columns.4.search.value')) && empty($request->input('columns.5.search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, LINEAS.DESCRIPCION AS CATEGORIA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         //->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, LINEAS.DESCRIPCION AS CATEGORIA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                            ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                            ->offset($start)
                            ->limit($limit);
                            //->orderBy($order,$dir);

            /*  ************************************************************ */

            if(!empty($request->input('search.value'))) {
                $posts->where(function ($query) use ($search) {
                            $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                            ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                });
            }

            if(!empty($request->input('columns.0.search.value'))) {
                $posts->where('PRODUCTOS_AUX.CODIGO', $request->input('columns.0.search.value'));
            }

            if(!empty($request->input('columns.1.search.value'))) {
                $posts->where('PRODUCTOS.DESCRIPCION', 'LIKE' , ''. $request->input('columns.1.search.value').'%');
            }

            if(!empty($request->input('columns.2.search.value'))) {
                $posts->where('PRODUCTOS.CATEGORIA', 'LIKE' , ''. $request->input('columns.2.search.value').'%');
            }           

            if(!empty($request->input('columns.3.search.value'))) {
                $posts->where('PRODUCTOS_AUX.PREC_VENTA', '=' , ''.$request->input('columns.3.search.value').'');
            }

            if(!empty($request->input('columns.4.search.value'))) {
                $posts->where('PRODUCTOS_AUX.PRECOSTO', '=' , ''.$request->input('columns.4.search.value').'');
            }

            if(!empty($request->input('columns.5.search.value'))) {
                $posts->where('PRODUCTOS_AUX.PREMAYORISTA', '=' , ''.$request->input('columns.5.search.value').'');
            }

            $posts = $posts->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                            ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal); 


            if(!empty($request->input('search.value'))) {
                $totalFiltered->where(function ($query) use ($search) {
                            $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                            ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                });
            }

            if(!empty($request->input('columns.0.search.value'))) {
                $totalFiltered->where('PRODUCTOS_AUX.CODIGO', $request->input('columns.0.search.value'));
            }

            if(!empty($request->input('columns.1.search.value'))) {
                $totalFiltered->where('PRODUCTOS.DESCRIPCION', 'LIKE' , ''. $request->input('columns.1.search.value').'%');
            }

            if(!empty($request->input('columns.2.search.value'))) {
                $totalFiltered->where('LINEAS.DESCRIPCION', 'LIKE' , ''.$request->input('columns.2.search.value').'%');
            }           

            if(!empty($request->input('columns.3.search.value'))) {
                $totalFiltered->where('PRODUCTOS_AUX.PREC_VENTA', '=' , ''.$request->input('columns.3.search.value').'');
            }

            if(!empty($request->input('columns.4.search.value'))) {
                $totalFiltered->where('PRODUCTOS_AUX.PRECOSTO', '=' , ''.$request->input('columns.4.search.value').'');
            }

            if(!empty($request->input('columns.5.search.value'))) {
                $totalFiltered->where('PRODUCTOS_AUX.PREMAYORISTA', '=' , ''.$request->input('columns.5.search.value').'');
            }

            $totalFiltered = $totalFiltered->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        // $path = '../storage/app/imagenes/product.png';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                // $imagen = Imagen::select(DB::raw('PICTURE'))
                // ->where('COD_PROD','=', $post->CODIGO)
                // ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CATEGORIA'] = $post->CATEGORIA;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                // if (count($imagen) > 0) {
                //    foreach ($imagen as $key => $image) {
                //         $imagen_producto = $image->PICTURE;
                //     }
                // } else {
                //     $imagen_producto = $dataDefaultImage;
                // }

                /*  --------------------------------------------------------------------------------- */

                //$nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:60px;height:60px;'>";
                $nestedData['IMAGEN'] = (Imagen::obtenerImagenURL($post->CODIGO))['imagen'];

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

        $imagen = Imagen::obtenerImagenURL($data['codigo']);

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
                PRODUCTOS_AUX.STOCK_MIN,
                PRODUCTOS_AUX.BAJA,
                PRODUCTOS.TEMPORADA,
                PRODUCTOS.PERIODO'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'),
        DB::raw('IFNULL((SELECT FECALTAS FROM VENTASDET WHERE (COD_PROD=PRODUCTOS_AUX.CODIGO and ID_SUCURSAL=PRODUCTOS_AUX.ID_SUCURSAL and ANULADO<>1) order by ID desc limit 1), 0) AS FECHAULT_V'),
        DB::raw('(SELECT FECALTAS FROM COMPRASDET WHERE (COD_PROD=PRODUCTOS_AUX.CODIGO and ID_SUCURSAL=PRODUCTOS_AUX.ID_SUCURSAL) order by ID desc limit 1) AS FECHAULT_C'))
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

            // BAJA

            if ($value->BAJA === 'NO') {
                $dato["BAJA"] = '<span class="badge badge-success">Activo</span>';
            } else if ($value->BAJA === 'SI') {
                $dato["BAJA"] = '<span class="badge badge-danger">Baja</span>';
            }

            /*  --------------------------------------------------------------------------------- */

            // TEMPORADA

            if ($value->TEMPORADA === 1) {
                $dato["TEMPORADA"] = 'PRIMAVERA';
            } else if ($value->TEMPORADA === 2) {
                $dato["TEMPORADA"] = 'VERANO';
            } else if ($value->TEMPORADA === 3) {
                $dato["TEMPORADA"] = 'OTOÑO';
            } else if ($value->TEMPORADA === 4) {
                $dato["TEMPORADA"] = 'INVIERNO';
            } else {
                $dato["TEMPORADA"] = 'N/A';
            }

            /*  --------------------------------------------------------------------------------- */

            // PERIODO

            if ($value->PERIODO === 1) {
                $dato["PERIODO"] = '1 MES';
            } else if ($value->PERIODO === 0) {
                $dato["PERIODO"] = 'N/A';
            } else {
                $dato["PERIODO"] = $value->PERIODO.' MESES';
            }

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
            $dato["FECHULT_C"] = date('Y-m-d', strtotime($value->FECHAULT_C));
            $dato["FECHULT_V"] = date('Y-m-d', strtotime($value->FECHAULT_V));
            $dato["STOCK"] = $value->STOCK;
            $dato["STOCK_MIN"] = $value->STOCK_MIN;

            /*  --------------------------------------------------------------------------------- */

            // ULTIMA VENTA

            // $fecha_v = Venta::select('FECALTAS')
            // ->where('COD_PROD', '=', $data['codigo'])
            // ->limit(1)
            // ->orderBy('ID', 'desc');

            // var_dump($fecha_v);

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
    
    public static function obtener_producto_compra($dato){

        /*  --------------------------------------------------------------------------------- */

        // USUARIO 
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $data = [];
        $valor = 0;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // CODIGO INTERNO 

        $codigo = Producto::codigoInterno(['codigo' => $dato["codigo"]]);
       
        if ($codigo["producto"] !== 0) {
            $dato["codigo"] = $codigo["producto"][0]["CODIGO"]; 
        }      

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO,
                          PRODUCTOS.DESCRIPCION, 
                          PRODUCTOS_AUX.PREC_VENTA,
                          PRODUCTOS_AUX.MONEDA,
                          PRODUCTOS_AUX.PREMAYORISTA,
                          PRODUCTOS.IMPUESTO,
                          PRODUCTOS.VENCIMIENTO'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $dato["codigo"])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        foreach ($producto as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $data["CODIGO"] = $value->CODIGO;
            $data["DESCRIPCION"] = $value->DESCRIPCION;
            $data["IMPUESTO"] = $value->IMPUESTO;
            $data["VENCIMIENTO"] = $value->VENCIMIENTO;

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOTE

            $lote = Stock::ultimo_lote($value->CODIGO);
            $data["LOTE"] = $lote + 1;

            /*  --------------------------------------------------------------------------------- */

            // COTIZAR LOS PRECIOS 
            
            $candec = Parametro::candec((int)$dato["moneda"]);

            // PRECIO VENTA 

            $valor = Cotizacion::CALMONED(['monedaProducto' => $value->MONEDA, 'monedaSistema' => (int)$dato["moneda"], 'precio' => $value->PREC_VENTA, 'decSistema' => $candec['CANDEC'], 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);

            if ($valor["response"] === false) {
                return $valor;
            } else {
                $data["PREC_VENTA"] = $valor["valor"];
            }

            // PRECIO MAYORISTA 

            $valor = Cotizacion::CALMONED(['monedaProducto' => $value->MONEDA, 'monedaSistema' => (int)$dato["moneda"], 'precio' => $value->PREMAYORISTA, 'decSistema' => $candec['CANDEC'], 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);

            if ($valor["response"] === false) {
                return $valor;
            } else {
                $data["PREMAYORISTA"] = $valor["valor"];
            }

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true, "producto" => $data];

        /*  --------------------------------------------------------------------------------- */

    }
    public static function producto_proveedor($datos)
    {

        try {
            
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["codigo"];
        $datos = array();
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = Compra::
        leftjoin('COMPRASDET', 'COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
        ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
        ->select(DB::raw('COUNT(*) AS CANTIDAD_COMPRA, COMPRAS.PROVEEDOR, PROVEEDORES.NOMBRE, SUM(COMPRASDET.CANTIDAD) AS CANTIDAD, COMPRAS.FECALTAS, COMPRAS.CODIGO'))
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
            $data[$value->PROVEEDOR]['CODIGO'] = $value->CODIGO;
            

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA ULTIMA FECHA COMPRA 

            $creacion = Compra::
            leftjoin('COMPRASDET', 'COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
            ->select(DB::raw('SUBSTR(COMPRAS.FECALTAS, 1,10) AS CREACION'))
            ->where([
                ['COMPRASDET.COD_PROD', '=', $codigo],
                ['COMPRAS.ID_SUCURSAL', '=', $user->id_sucursal],
                ['COMPRAS.PROVEEDOR', '=', $value->PROVEEDOR],
            ])
            ->orderBy('COMPRAS.ID', 'desc')
            ->limit(1)
            ->get();

            $data[$value->PROVEEDOR]['FECALTAS'] = $creacion[0]->CREACION;

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

                $datos[] = $data[$value->PROVEEDOR];
            }
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "proveedor" => $datos];

            /*  --------------------------------------------------------------------------------- */

        } else {
            return ["response" => false, "proveedor" => ""];
        }

        /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            var_dump($e);
        }

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

            /*  --------------------------------------------------------------------------------- */

            // 
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

            /*  --------------------------------------------------------------------------------- */
            
            $data[$key]['CANTIDAD_TRANSFERENCIA'] = $value->CANTIDAD_TRANSFERENCIA;
            $data[$key]['CANTIDAD'] = $value->CANTIDAD;
            $data[$key]['DESCRIPCION'] = $value->DESCRIPCION;
            $data[$key]['GUARANIES'] = 0;
            $data[$key]['DOLARES'] = 0;
            $data[$key]['PESOS'] = 0;
            $data[$key]['REALES'] = 0;

            /*  --------------------------------------------------------------------------------- */

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
            
            /*  --------------------------------------------------------------------------------- */
            
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
            return ["response" => false, "statusText" => "Este producto posee ventas !"];
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
            return ["response" => false, "statusText" => "Este producto posee compras !"];
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

    public static function minimo($request){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 =>'CODIGO', 
                            1 =>'DESCRIPCION',
                            2 => 'PREC_VENTA',
                            3 => 'PRECOSTO',
                            4 => 'PREMAYORISTA',
                            5 => 'STOCK',
                            6 => 'IMAGEN',
                            7 => 'MINIMO'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 
        
        $totalData = ProductosAux::
        whereRaw('PRODUCTOS_AUX.STOCK_MIN >= (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0))')
        ->where('PRODUCTOS_AUX.STOCK_MIN', '<>', '0')
        ->where('PRODUCTOS_AUX.BAJA', '=', 'NO')
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
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

            $posts = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
            ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.STOCK_MIN'),
                DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK')
            )
            ->where('PRODUCTOS_AUX.STOCK_MIN', '<>', '0')
            ->where('PRODUCTOS_AUX.BAJA', '=', 'NO')
            ->whereRaw('PRODUCTOS_AUX.STOCK_MIN >= (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0))')
            ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
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

            $posts = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
            ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.STOCK_MIN'),
                DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK')
            )
            ->where('PRODUCTOS_AUX.STOCK_MIN', '<>', '0')
            ->where('PRODUCTOS_AUX.BAJA', '=', 'NO')
            ->whereRaw('PRODUCTOS_AUX.STOCK_MIN >= (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0))')
            ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"{$search}%")
                ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::
            leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
            ->where('PRODUCTOS_AUX.STOCK_MIN', '<>', '0')
            ->where('PRODUCTOS_AUX.BAJA', '=', 'NO')
            ->whereRaw('PRODUCTOS_AUX.STOCK_MIN >= (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0))')
            ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)   
                             ->where(function ($query) use ($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"{$search}%")
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
                //$nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['MINIMO'] = Common::formato_precio($post->STOCK_MIN, 0);
                $nestedData['ACCION'] = "&emsp;<a href='#' id='Detalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='Baja' title='Dar de Baja'><i class='fa fa-arrow-down text-danger'  aria-hidden='true'></i></a>";

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

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:30px;height:30px;'>";

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

    public static function baja($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR STOCK 

        $comprobar = Stock::comprobar_si_hay_stock_producto($codigo["codigo"]);

        if ($comprobar === true) {
            return ["response" => false, "statusText" => "Todavia contiene stock, no se puede dar de baja !"];
        }
        
        /*  --------------------------------------------------------------------------------- */

        ProductosAux::where('ID_SUCURSAL', $user->id_sucursal)
        ->where('CODIGO', $codigo["codigo"])
        ->update(['BAJA' => 'SI']);

        /*  --------------------------------------------------------------------------------- */

        return ["response" => true, "statusText" => "Ha sido de baja !"];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtener_producto_POS($dato){
        
        /*  --------------------------------------------------------------------------------- */

        // USUARIO 
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $data = [];
        $valor = 0;
        $dia = date("Y-m-d");
        $descuento_marca = false;
        $descuento_categoria = 0;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // CODIGO INTERNO 

        $codigo = Producto::codigoInterno(['codigo' => $dato["codigo"]]);
       
        if ($codigo["producto"] !== 0) {
            $dato["codigo"] = $codigo["producto"][0]["CODIGO"]; 
        }      

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO,
                          PRODUCTOS.DESCRIPCION, 
                          PRODUCTOS_AUX.PREC_VENTA,
                          PRODUCTOS_AUX.MONEDA,
                          PRODUCTOS_AUX.PREMAYORISTA,
                          PRODUCTOS.IMPUESTO,
                          PRODUCTOS.MARCA,
                          PRODUCTOS.LINEA,
                          PRODUCTOS_AUX.FECALTAS,
                          PRODUCTOS.VENCIMIENTO,
                          PRODUCTOS_AUX.CODIGO_REAL,
                          PRODUCTOS_AUX.DESCUENTO')
        , DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $dato["codigo"])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        foreach ($producto as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $data["CODIGO"] = $value->CODIGO;
            $data["DESCRIPCION"] = $value->DESCRIPCION;
            $data["IMPUESTO"] = $value->IMPUESTO;
            $data["VENCIMIENTO"] = $value->VENCIMIENTO;
            $data["STOCK"] = $value->STOCK;
            $data["MARCA"] = $value->MARCA;
            $data["LINEA"] = $value->LINEA;
            $data["CODIGO_REAL"] = $value->CODIGO_REAL;
            $data["DESCUENTO"] = $value->DESCUENTO;
            $data["FECALTAS"]= $value->FECALTAS;

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOTE - SE QUITA PARA OBTENER MAS VELOCIDAD

            //$lote = Stock::ultimo_lote($value->CODIGO);
            $data["LOTE"] = 0;

            /*  --------------------------------------------------------------------------------- */

            // COTIZAR LOS PRECIOS 
            
            $candec = Parametro::candec((int)$dato["moneda"]);

            // PRECIO VENTA 

            $valor = Cotizacion::CALMONED(['monedaProducto' => $value->MONEDA, 'monedaSistema' => (int)$dato["moneda"], 'precio' => $value->PREC_VENTA, 'decSistema' => $candec['CANDEC'], 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);

            if ($valor["response"] === false) {
                return $valor;
            } else {
                $data["PREC_VENTA"] = $valor["valor"];
            }

            // PRECIO MAYORISTA 

            $valor = Cotizacion::CALMONED(['monedaProducto' => $value->MONEDA, 'monedaSistema' => (int)$dato["moneda"], 'precio' => $value->PREMAYORISTA, 'decSistema' => $candec['CANDEC'], 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);

            if ($valor["response"] === false) {
                return $valor;
            } else {
                $data["PREMAYORISTA"] = $valor["valor"];
            }

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO CATEGORIA 

        if(count($producto) > 0) {
            $descuento_categoria = LineasDescuento::obtener_descuento($data["LINEA"], $user->id_sucursal);
        }

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO MANUAL POR PRODUCTO

        if(count($producto) > 0) {
            if ($data["DESCUENTO"] > 0) {
                $descuento_categoria = $data["DESCUENTO"];
            }
        }

        /*  --------------------------------------------------------------------------------- */
        
        // REVISAR DESCUENTO POR MARCA 

        if(count($producto) > 0) {
            $descuento_marca = MarcaAux::
            select(DB::raw('DESCUENTO, FECHAINI, FECHAFIN'))
            ->where('CODIGO_MARCA', '=', $data["MARCA"])
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->where([
                ['FECHAINI', '<=', $dia],
                ['FECHAFIN', '>=', $dia]
                ])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // CONVERTIR EN FALSE DESCUENTO POR MARCA 
            //EN CASO DE QUE SEA SUCURSAL VILLAMORRA EL DESCUENTO POR MARCA NO SERA REALIZADO SI LOS PRODUCTOS SON DE FECHA DE IMPORTACION DEL 2020-08-17 PARA ARRIBA
            if($user->id_sucursal==11){
                        if (count($descuento_marca) > 0 && ($data["FECALTAS"]<'2020-08-18' ||  $data["FECALTAS"] >'2020-08-21') ) {
                        $descuento_marca = $descuento_marca[0];
                     }else{
                        $descuento_marca = false;
                     }
                }else{
                     if (count($descuento_marca) > 0) {
                        $descuento_marca = $descuento_marca[0];
                    } else {
                        $descuento_marca = false;
                    }
             }
           

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // IMAGEN 
        
        //$imagen = Imagen::obtenerImagen($dato["codigo"]);
        $imagen = Imagen::obtenerImagenURL($dato["codigo"]);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($producto) > 0) {
            return ["response" => true, "producto" => $data, "descuento_marca" => $descuento_marca, "descuento_categoria" => $descuento_categoria, 'imagen' => $imagen["imagen"]];
        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function ubicacion($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $data = [];

        /*  --------------------------------------------------------------------------------- */

        // CONSEGUIR LOTE 

        $ubicacion = Shelf::select(DB::raw('SHELF, LINE, POSITION, OCCUPATION, WAY, MAIN_CATEGORY'))
        ->where('COD_PROD','=', $codigo["codigo"])
        //->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR  

        if (count($ubicacion) > 0) {
            return ["ubicacion" => $ubicacion[0]];
        } else {
            return ["ubicacion" => false];
        }

        /*  --------------------------------------------------------------------------------- */
        
    } 

    public static function existe($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO

        $producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $data['codigo'])
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            return ['response' => true, 'codigo' => $data['codigo']];
        } else {
            return ['response' => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function apiListarProductos($datos){

        /*  --------------------------------------------------------------------------------- */
        
        // INICIAR VARIABLES 

        $id_sucursal = 9;
        $dir = $datos["dir"];
        $orderBy = $datos["orderBy"];
        $fromPrice = (float)$datos["fromPrice"];
        $toPrice = (float)$datos["toPrice"];
        $name = $datos["name"];
        $limit = (int)$datos["limit"];
        $offset = (int)$datos["offset"];
        $category = (int)$datos["category"];

        /*  --------------------------------------------------------------------------------- */

        // MANTENER LIMITE IGUAL 50

        if($limit > 50) {
            $limit = 50;
        }

        /*  --------------------------------------------------------------------------------- */

        // ORDEN 

        if ($dir === "1") {
            $dir = 'asc';
        } else {
            $dir = 'desc';
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER MONEDA PARAMETRO GUARANIES

        $parametro = Parametro::candec(1);
        $candec = $parametro["CANDEC"];

        /*  --------------------------------------------------------------------------------- */

        // SI LA TABLA DE MONEDAS ES UNICA 

        $tab_unica = Parametro::select(DB::raw('TAB_UNICA'))
        ->where('ID_SUCURSAL','=', $id_sucursal)
        ->get();
        $tab_unica = $tab_unica[0]->TAB_UNICA;

        if($tab_unica === 'SI') {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTAL DATA 

        $totalData = ProductosAux::where('PRODUCTOS_AUX.ID_SUCURSAL','=', $id_sucursal)
                     ->count();  

        /*  --------------------------------------------------------------------------------- */

        // FROM PRICE TO DOLAR 

        $cotizacion = Cotizacion::CALMONED(['monedaProducto' => 1, 'monedaSistema' => 2, 'precio' => Common::quitar_coma($fromPrice, 0), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $id_sucursal]);

        if ($cotizacion["response"] === false) {
            return $cotizacion;
        } else {
            $fromPrice = (float)$cotizacion["valor"];
        }

        /*  --------------------------------------------------------------------------------- */

        // TO PRICE TO DOLAR 

        $cotizacion = Cotizacion::CALMONED(['monedaProducto' => 1, 'monedaSistema' => 2, 'precio' => Common::quitar_coma($toPrice, 0), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $id_sucursal]);

        if ($cotizacion["response"] === false) {
            return $cotizacion;
        } else {
            $toPrice = (float)$cotizacion["valor"];
        }
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PRODUCTOS

        $query = ProductosAux::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('SUBLINEA_DET', 'PRODUCTOS.SUBLINEADET', '=', 'SUBLINEA_DET.CODIGO')
        //->leftjoin('IMAGENES', 'PRODUCTOS_AUX.CODIGO', '=', 'IMAGENES.COD_PROD')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO as code,
                          SUBLINEA_DET.DESCRIPCION AS name, 
                          PRODUCTOS_AUX.PREC_VENTA as price,
                          PRODUCTOS_AUX.MONEDA AS currency,
                          0 AS image,
                          0 AS inStock'
                      ),
                DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS stock'))
        //->where('PRODUCTOS.CODIGO', '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $id_sucursal);

        if($name !== null && $name !== '' && $name !== 'null') {
            $query->where('SUBLINEA_DET.DESCRIPCION', 'LIKE', '%'.$name.'%');
        }

        if($category !== null && $category !== '' && $category !== 0) {
            $query->where('PRODUCTOS.LINEA', '=', $category);
        }

        if($fromPrice < $toPrice) {
            $query->whereBetween('PRODUCTOS_AUX.PREC_VENTA', [$fromPrice, $toPrice]);
        } else if(($fromPrice === $toPrice) and $toPrice > 0) {
            $query->where('PRODUCTOS_AUX.PREC_VENTA','=', $toPrice);
        }

        if($orderBy === "1") {
            $query->orderBy('SUBLINEA_DET.DESCRIPCION', $dir);
        }
        
        if($orderBy === "2") {
            $query->orderBy('PRODUCTOS_AUX.PREC_VENTA', $dir);
        }
        
        if($orderBy === "3") {
            $query->orderBy('PRODUCTOS_AUX.PREC_VENTA', $dir);
        }

        $query->limit($limit);

        $totalFiltered =  $query->count();
        $query->offset($offset);
        $producto = $query->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        
        foreach ($producto as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */
            
            $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $value->currency, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value->price, $candec), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $id_sucursal]);
            
            /*  --------------------------------------------------------------------------------- */

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            if ($cotizacion["response"] === false) {
                return $cotizacion;
            }

            /*  --------------------------------------------------------------------------------- */

            // PRECIO VENTA A GUARANIES

            $producto[$key]->price = $cotizacion['valor'];

            /*  --------------------------------------------------------------------------------- */

            // REVISAR CNATIDAD
            
            if ($producto[$key]->stock > 0) {
                $producto[$key]->inStock = true;
            } else {
                $producto[$key]->inStock = false;
            }

            /*  --------------------------------------------------------------------------------- */

            // CAMBIAR MONEDA A GUARANIESE 

            $producto[$key]->currency = 1;

            /*  --------------------------------------------------------------------------------- */

            // IMAGEN 
            
            $producto[$key]->image =  ImagenesWeb::obtenerImagenCarpeta($producto[$key]->code)["imagen"];

            /*  --------------------------------------------------------------------------------- */

        }

        

                

        // if (count($producto) > 0) {

        //     /*  --------------------------------------------------------------------------------- */

        //     $producto[0]["AUTODESCRIPCION"] = false;

        //     /*  --------------------------------------------------------------------------------- */

        //     // IMAGEN 

             //$producto[0]["IMAGE"] =  Imagen::obtenerImagen($codigo)["imagen"];

        //     /*  --------------------------------------------------------------------------------- */

        //     // RETORNAR VALOR 

        //     return ["producto" => $producto[0]];

        //     /*  --------------------------------------------------------------------------------- */

        // } else {
        //     return ["response" => false];
        // }

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "currencyDescription" => $parametro["DESCRIPCION"],  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $producto  
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */

    }   

    public static function guardar_img(){

        $directory="C:\Users\xsinx\Desktop\gg";
        $dirint = dir($directory);
        $secundario=1;
        // $aux=0;
        // $codigo=0;
        // $c=0;
        // $woocommerce = new Client(
        //     'https://www.calbea.com.py', // Your store URL
        //     'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
        //     'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
        //     [
        //         'wp_json' => true, // Enable the WP REST API integration
        //         'wp_api' => true, // Enable the WP REST API integration
        //         'version' => 'wc/v3',// WooCommerce WP REST API version
        //         'query_string_auth' => true 
        //     ]
        // );
        while (($archivo = $dirint->read()) !== false){
        
            //echo '<img src="'.$directory."/".$archivo.'">'.strlen($archivo)."\n";
            if( strlen($archivo)>2){

                $image = "";
                $imagenBase64 = "";
                $imge = "";
                $cod_prod = substr($archivo,0,-4);
                   
                if(strlen($cod_prod) == 10 || strlen($cod_prod) == 15){
                    $codigo=substr($cod_prod,0,-2);
                    // var_dump($cod_prod);
                    $secundario=2;
                
                }else{
                    
                    $secundario=1;
                    $codigo=$cod_prod;
                        $producto = DB::connection('retail')->table('PRODUCTOS_AUX')->where('CODIGO', '=', $codigo)
                        ->update(['ONLINE' => 1]);

                         // var_dump($codigo);
                }
              
            //         if($aux!=$codigo ){


            //             $producto = Producto::select(DB::raw('PRODUCTOS.CODIGO_INTERNO'))
            //             ->where('PRODUCTOS.CODIGO', '=', $codigo)
            //             ->where('PRODUCTOS.ID_SUCURSAL', '=', 9)
            //             ->get()->toArray();
            //             //var_dump($producto["0"]["CODIGO_INTERNO"]);

            //             if(count($producto)>0){
                                 
            //                 //CONVERTIR JSON

            //                 $item=$woocommerce->get('products',$parameters=["sku"=>$producto["0"]["CODIGO_INTERNO"]]);
            //                    // var_dump($item["0"]->id);


            //                  //ACTUALIZACION DE LOS PRODUCTOS EN WEB

            //                 if(count($item)>0){
            //                     $data = [
            //                         'name'=>$item["0"]->name,
            //                         'images'=> [
            //                             [
            //                                 'name'=> $codigo, 
            //                                 'src'=>'https://www.calbea.com.py/wp-content/fotos3/'.$codigo.'.jpg'
            //                             ]

            //                         ]
            //                     ];

            //                     $woocommerce->put('products/'.$item["0"]->id.'',$data);
                                     
            //                 }
            //             }
            //         }
            //     }

            //      $aux=$codigo;
            }
        }
        
        $dirint->close();
    }

    public static function guardar_img_principal(){

        // INDICA LA DIRECCION DEL ARCHIVO

        $directory="C:\Users\alezcano\Desktop\gg";
        $dirint = dir($directory);
        $secundario=1;

        // LEE TODO LOS DATOS DEL ARCHIVO

        while (($archivo = $dirint->read()) !== false){
        
            //echo '<img src="'.$directory."/".$archivo.'">'.strlen($archivo)."\n";
            if( strlen($archivo)>2){

                $image = "";
                $imagenBase64 = "";
                $imge = "";     

                // SACA EL .JPG
    
                    // UNE LA UBICACION DEL ARCHIVO CON EL NOMBRE DE LA IMAGEN
                    
                    $image= $directory."/".$archivo;

                    //$contenidoImagen = file_get_contents("gg/45126352.jpg");

                    $imagenBase64 = chunk_split(base64_encode(file_get_contents($image)));

                    $imge = preg_replace('#^data:image/[^;]+;base64,#', '', $imagenBase64);

                    // OBTINE EL CODIGO INTERNO DEL PRODUCTO

                    /*$interno = DB::connection('retail')
                        ->table('Productos')
                        ->select(DB::raw('CODIGO_INTERNO'))
                        ->where("CODIGO","=",$codigo)
                        ->get();*/
/*                  var_dump(strlen($imge));*/
                $temp=DB::connection('retail')->table('imagenes')
            /*    ->leftjoin('PRODUCTOS_AUX', 'PRODUCTOS_AUX.CODIGO', '=', 'IMAGENES.COD_PROD')*/
   
               ->select(
                DB::raw('COUNT(COD_PROD)'))
/*             ->WHERE('PRODUCTOS_AUX.ID_SUCURSAL','=',9)*/
             ->whereRaw('length(PICTURE)= 14373')
              ->get()
              ->toArray();
              var_dump($temp);
                   /* foreach ($interno as $key => $internos) {

                            Imagen::guardar_imagen_storage([
                                'COD_PROD' => $codigo,
                                'CODIGO_INTERNO'=> $internos->CODIGO_INTERNO,
                                'PICTURE' => $imge
                            ]);
                    }*/
                
                // var_dump($imge);

                // $aleluya = chunk_split (base64_encode(file_get_contents($image)));
                // var_dump(base64_decode($aleluya));  
            }
        }

        $dirint->close();
    }

    public static function mostrar_new($datos)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $start = $datos["datos"]["offset"];
        $limit = $datos["datos"]["limite"];
        $pagina = $datos["datos"]["actual"];
        $start = ($pagina * $limit) - $limit;
        $categorias = $datos["datos"]["categorias"];
        $marcas = $datos["datos"]["marcas"];
        $proveedores = $datos["datos"]["proveedores"];
        $busqueda = $datos["datos"]["busqueda"];
        $tipo = $datos["datos"]["tipo"];
        $ordenar = $datos["datos"]["ordenar"];
        $estado = $datos["datos"]["estado"];
        $imagenes = $datos["datos"]["imagenes"];

        /*  --------------------------------------------------------------------------------- */

            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, LINEAS.DESCRIPCION AS CATEGORIA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.DESCUENTO, LINEAS.CODIGO AS LINEA, MARCA.CODIGO AS MARCA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                                    
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                         //->where('PRODUCTOS.LINEA','=', 34)
                         ->offset($start)
                         ->limit($limit);
                         //->orderBy($order,$dir)
                         

        if($imagenes === true) {
            $posts->rightjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO');
        }

        if($tipo === '1') {

            $posts->where('PRODUCTOS.CODIGO', 'LIKE' ,'%'.$busqueda.'%');

        } else if ($tipo === '2'){

            $posts->where('PRODUCTOS.DESCRIPCION', 'LIKE' ,'%'.$busqueda.'%');

        }

        if($estado === 2) {
            $posts->where('PRODUCTOS_AUX.DESCUENTO', '>' , 0);
        }

        if(!empty($categorias)) {
            $posts->whereIn('PRODUCTOS.LINEA', $categorias);
        }

        if(!empty($marcas)) {
            $posts->whereIn('PRODUCTOS.MARCA', $marcas);
        }

        if(!empty($proveedores)) {
            $posts->whereIn('PRODUCTOS.PROVEEDOR', $proveedores);
        }

        if(!empty($ordenar)) {
            if($ordenar === 2) {
                $posts->orderBy('PRODUCTOS.PREC_VENTA', 'DESC');
            } else if ($ordenar === 3) {
                $posts->orderBy('PRODUCTOS.PREC_VENTA', 'ASC');
            }
        }

        // if($imagenes === true) {
        //     $posts->whereRaw('LENGTH(IMAGENES.PICTURE) <> 14373');
        // }

        $posts = $posts->get();

        /*  --------------------------------------------------------------------------------- */

        // TOTAL FILTRADO 
        
        $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal);

        if($imagenes === true) {
            $totalFiltered->rightjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO');
        }

        if($tipo === '1') {
            $totalFiltered->where('PRODUCTOS.CODIGO', 'LIKE' ,'%'.$busqueda.'%');
        } else if ($tipo === '2'){
            $totalFiltered->where('PRODUCTOS.DESCRIPCION', 'LIKE' ,'%'.$busqueda.'%');
        }
                         
        if(!empty($categorias)) {
            $totalFiltered->whereIn('PRODUCTOS.LINEA', $categorias);
        }

        if(!empty($marcas)) {
            $totalFiltered->whereIn('PRODUCTOS.MARCA', $marcas);
        }

        if(!empty($proveedores)) {
            $totalFiltered->whereIn('PRODUCTOS.PROVEEDOR', $proveedores);
        }

        if(!empty($ordenar)) {
            if($ordenar === 2) {
                $totalFiltered->orderBy('PRODUCTOS.PREC_VENTA', 'DESC');
            } else if ($ordenar === 3) {
                $totalFiltered->orderBy('PRODUCTOS.PREC_VENTA', 'ASC');
            }
        }

        if($estado === 2) {
            $totalFiltered->where('PRODUCTOS_AUX.DESCUENTO', '>' , 0);
        }
        
        // if($imagenes === true) {
        //     $totalFiltered->whereRaw('LENGTH(IMAGENES.PICTURE) <> 14373');
        // }

        $totalFiltered = $totalFiltered->count();

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        // $path = '../storage/app/imagenes/product.png';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                // $imagen = Imagen::select(DB::raw('PICTURE'))
                // ->where('COD_PROD','=', $post->CODIGO)
                // ->get();
                
                /*  --------------------------------------------------------------------------------- */

                $filename = '../storage/app/public/imagenes/productos/'.$post->CODIGO.'.jpg';
                
                if(file_exists($filename)) {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/'.$post->CODIGO.'.jpg';
                } else {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/product.png';
                }

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CATEGORIA'] = $post->CATEGORIA;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PREC_VENTA_CRUDO'] = Common::formato_precio($post->PREC_VENTA, 2);
                $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);
                $nestedData['PREMAYORISTA_CRUDO'] = Common::formato_precio($post->PREMAYORISTA, 2);
                

                if ($post->DESCUENTO === 0) {

                    /*  --------------------------------------------------------------------------------- */

                    // DESCUENTO MANUAL POR PRODUCTO

                    $descuento_marca = MarcaAux::obtener_descuento($post->MARCA, $user->id_sucursal);

                    /*  --------------------------------------------------------------------------------- */

                    // DESCUENTO CATEGORIA 

                    $descuento_categoria = LineasDescuento::obtener_descuento($post->LINEA, $user->id_sucursal);

                    /*  --------------------------------------------------------------------------------- */

                    $post->DESCUENTO = $descuento_marca === 0 ? $descuento_categoria : $descuento_marca;

                    /*  --------------------------------------------------------------------------------- */
                    
                }
                
                $nestedData['DESCUENTO'] = $post->DESCUENTO;

                
                $nestedData['LINEA'] = $post->LINEA;
                $nestedData['MARCA'] = $post->MARCA;

                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);

                if ($nestedData['STOCK'] === '0') {
                    $nestedData['BACKGROUND'] = 'bg-danger';
                    $nestedData['ESTATUS'] = 'AGOTADO';
                } else if($post->DESCUENTO > 0) {
                    $nestedData['BACKGROUND'] = 'bg-warning';
                    $nestedData['ESTATUS'] = ''.$post->DESCUENTO.'% OFF';
                } else {
                    $nestedData['BACKGROUND'] = 'bg-success';
                    $nestedData['ESTATUS'] = 'DISPONIBLE';
                }

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                // if (count($imagen) > 0) {
                //    foreach ($imagen as $key => $image) {
                //         $imagen_producto = $image->PICTURE;
                //     }
                // } else {
                //     $imagen_producto = $dataDefaultImage;
                // }

                /*  --------------------------------------------------------------------------------- */

                //$nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."'  width='100%'>";

                $nestedData['IMAGEN'] = "<img src='".$imagen_producto."'  width='100%'>";
                
                // $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-fluid mx-auto d-block img-thumbnail' alt='Card image cap'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */



        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    //"draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval(12),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,
                    "offset"          => $start
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }  

    public static function ofertas($data){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $sucursal = $data["sucursal"];

        /*  --------------------------------------------------------------------------------- */

        // PRODUCTOS CON OFERTA 
        
        $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, LINEAS.DESCRIPCION AS CATEGORIA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.DESCUENTO, LINEAS.CODIGO AS LINEA, MARCA.CODIGO AS MARCA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->rightjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')            
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $sucursal)
                         ->where('PRODUCTOS_AUX.DESCUENTO', '>' , 0)
                         ->whereRaw('0 < (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0))')
                         ->inRandomOrder()
                         ->limit(10)
                         ->get();
                         
        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/oferta.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $imagen_oferta = file_get_contents($path);
        $imagen_oferta = "<img src='data:image/jpg;base64,".base64_encode($imagen_oferta)."'  width='50%' class='img-fluid'>";

        /*  --------------------------------------------------------------------------------- */

        $path = '../storage/app/imagenes/logo.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $imagen = file_get_contents($path);
        $imagen_logo = "<img src='data:image/jpg;base64,".base64_encode($imagen)."' >";

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
                $nestedData['CATEGORIA'] = $post->CATEGORIA;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);
                
                $nestedData['DESCUENTO'] = $post->DESCUENTO;

                
                $nestedData['LINEA'] = $post->LINEA;
                $nestedData['MARCA'] = $post->MARCA;

                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);

                if ($nestedData['STOCK'] === '0') {
                    $nestedData['BACKGROUND'] = 'bg-danger';
                    $nestedData['ESTATUS'] = 'AGOTADO';
                } else if($post->DESCUENTO > 0) {
                    $nestedData['BACKGROUND'] = 'bg-warning';
                    $nestedData['ESTATUS'] = ''.$post->DESCUENTO.'% OFF';
                } else {
                    $nestedData['BACKGROUND'] = 'bg-success';
                    $nestedData['ESTATUS'] = 'DISPONIBLE';
                }

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                $descuento_unit = (Common::quitar_coma($post->PREC_VENTA, 2) * Common::quitar_coma($post->DESCUENTO, 2)) / 100;
                $precio_unit = Common::quitar_coma($post->PREC_VENTA, 2) - $descuento_unit;
                $nestedData['PREC_DESCUENTO'] = Common::precio_candec($precio_unit, $post->MONEDA);

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

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."'  width='100%' class='img-fluid'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }


        } 

        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "data"            => $data,
                    "oferta"          => $imagen_oferta,
                    "logo"            => $imagen_logo
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */

    }

    public static function catalogo_cliente($datos)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $start = $datos["datos"]["offset"];
        $limit = $datos["datos"]["limite"];
        $pagina = $datos["datos"]["actual"];
        $start = ($pagina * $limit) - $limit;
        $categorias = $datos["datos"]["categorias"];
        $marcas = $datos["datos"]["marcas"];
        $proveedores = $datos["datos"]["proveedores"];
        $busqueda = $datos["datos"]["busqueda"];
        $tipo = $datos["datos"]["tipo"];
        $ordenar = $datos["datos"]["ordenar"];
        $estado = $datos["datos"]["estado"];
        $imagenes = $datos["datos"]["imagenes"];

        /*  --------------------------------------------------------------------------------- */

        // RESTRINGIR LIMITE 
        
        if ($limit > 100) {
            $limit = 96;
        }

        /*  --------------------------------------------------------------------------------- */

            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, LINEAS.DESCRIPCION AS CATEGORIA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA, PRODUCTOS_AUX.DESCUENTO, LINEAS.CODIGO AS LINEA, MARCA.CODIGO AS MARCA'),
                     DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                                    
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', 9)
                         //->where('PRODUCTOS.LINEA','=', 34)
                         ->offset($start)
                         ->limit($limit);
                         //->orderBy($order,$dir)
                         

        if($imagenes === true) {
            $posts->rightjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO');
        } else{
            $posts->leftjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO');
        }

        if($tipo === '1' && !empty($busqueda)) {

            $posts->where('PRODUCTOS.CODIGO', 'LIKE' ,''.$busqueda.'%');

        } else if ($tipo === '2' && !empty($busqueda)){

            $posts->where('PRODUCTOS.DESCRIPCION', 'LIKE' ,'%'.$busqueda.'%');

        }

        if($estado === 2) {
            $posts->where('PRODUCTOS_AUX.DESCUENTO', '>' , 0);
        }

        if(!empty($categorias)) {
            $posts->whereIn('PRODUCTOS.LINEA', $categorias);
        }

        if(!empty($marcas)) {
            $posts->whereIn('PRODUCTOS.MARCA', $marcas);
        }

        if(!empty($proveedores)) {
            $posts->whereIn('PRODUCTOS.PROVEEDOR', $proveedores);
        }

        if(!empty($ordenar)) {
            if($ordenar === 2) {
                $posts->orderBy('PRODUCTOS.PREC_VENTA', 'DESC');
            } else if ($ordenar === 3) {
                $posts->orderBy('PRODUCTOS.PREC_VENTA', 'ASC');
            }
        }

        // if($imagenes === true) {
        //     $posts->whereRaw('LENGTH(IMAGENES.PICTURE) <> 14373');
        // }

        $posts = $posts->get();

        /*  --------------------------------------------------------------------------------- */

        // TOTAL FILTRADO 
        
        $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', 9);

        if($imagenes === true) {
            $totalFiltered->rightjoin('IMAGENES', 'IMAGENES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO');
        }

        if($tipo === '1' && !empty($busqueda)) {
            $totalFiltered->where('PRODUCTOS.CODIGO', 'LIKE' ,''.$busqueda.'%');
        } else if ($tipo === '2' && !empty($busqueda)){
            $totalFiltered->where('PRODUCTOS.DESCRIPCION', 'LIKE' ,'%'.$busqueda.'%');
        }
                         
        if(!empty($categorias)) {
            $totalFiltered->whereIn('PRODUCTOS.LINEA', $categorias);
        }

        if(!empty($marcas)) {
            $totalFiltered->whereIn('PRODUCTOS.MARCA', $marcas);
        }

        if(!empty($proveedores)) {
            $totalFiltered->whereIn('PRODUCTOS.PROVEEDOR', $proveedores);
        }

        if(!empty($ordenar)) {
            if($ordenar === 2) {
                $totalFiltered->orderBy('PRODUCTOS.PREC_VENTA', 'DESC');
            } else if ($ordenar === 3) {
                $totalFiltered->orderBy('PRODUCTOS.PREC_VENTA', 'ASC');
            }
        }

        if($estado === 2) {
            $totalFiltered->where('PRODUCTOS_AUX.DESCUENTO', '>' , 0);
        }
        
        // if($imagenes === true) {
        //     $totalFiltered->whereRaw('LENGTH(IMAGENES.PICTURE) <> 14373');
        // }

        $totalFiltered = $totalFiltered->count();

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

         //$path = '../storage/app/imagenes/product.png';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                //$imagen = Imagen::select(DB::raw('PICTURE'))
                //->where('COD_PROD','=', $post->CODIGO)
                //->get();
                
                /*  --------------------------------------------------------------------------------- */

                $filename = '../storage/app/public/imagenes/productos/'.$post->CODIGO.'.jpg';
                
                if(file_exists($filename)) {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/'.$post->CODIGO.'.jpg';
                } else {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/product.png';
                }

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CATEGORIA'] = $post->CATEGORIA;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PREC_VENTA_CRUDO'] = Common::formato_precio($post->PREC_VENTA, 2);
                $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);
                $nestedData['PREMAYORISTA_CRUDO'] = Common::formato_precio($post->PREMAYORISTA, 2);
                

                if ($post->DESCUENTO === 0) {

                    /*  --------------------------------------------------------------------------------- */

                    // DESCUENTO MANUAL POR PRODUCTO

                    $descuento_marca = MarcaAux::obtener_descuento($post->MARCA, 9);

                    /*  --------------------------------------------------------------------------------- */

                    // DESCUENTO CATEGORIA 

                    $descuento_categoria = LineasDescuento::obtener_descuento($post->LINEA, 9);

                    /*  --------------------------------------------------------------------------------- */

                    $post->DESCUENTO = $descuento_marca === 0 ? $descuento_categoria : $descuento_marca;

                    /*  --------------------------------------------------------------------------------- */
                    
                }
                
                $nestedData['DESCUENTO'] = $post->DESCUENTO;

                
                $nestedData['LINEA'] = $post->LINEA;
                $nestedData['MARCA'] = $post->MARCA;

                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);

                if ($nestedData['STOCK'] === '0') {
                    $nestedData['BACKGROUND'] = 'bg-danger';
                    $nestedData['ESTATUS'] = 'AGOTADO';
                } else if($post->DESCUENTO > 0) {
                    $nestedData['BACKGROUND'] = 'bg-warning';
                    $nestedData['ESTATUS'] = ''.$post->DESCUENTO.'% OFF';
                } else {
                    $nestedData['BACKGROUND'] = 'bg-success';
                    $nestedData['ESTATUS'] = 'DISPONIBLE';
                }

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                // if (strlen($post->PICTURE) > 0) {
                 
                //         $imagen_producto = $post->PICTURE;
           
                // } else {
                //     $imagen_producto = $dataDefaultImage;
                // }

                /*  --------------------------------------------------------------------------------- */

                // $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."'  width='100%'>";

                $nestedData['IMAGEN'] = "<img src='".$imagen_producto."'  width='100%'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */



        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    //"draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval(12),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,
                    "offset"          => $start
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    public static function movimientoProducto($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $data = [];
        $vencimiento = '0000-00-00';
        $cod_prod = $codigo["codigo"];
        $parametro = Parametro::consultaPersonalizada('MONEDA');
        $candec = (Parametro::candec($parametro->MONEDA))['CANDEC'];
      
        /*  --------------------------------------------------------------------------------- */

        // OBTENER VENCIDOS 

        $vencidos = DB::connection('retail')->table('Lotes')
        ->select(DB::raw('LOTE,
                    IFNULL(CANTIDAD, 0) AS CANTIDAD,
                    COSTO,
                    substr(FECALTAS, 1, 10) AS ENTRADA,
                    substr(FECHA_VENC, 1, 10) AS VENCIMIENTO,
                    COSTO AS TOTAL'))
            ->where('COD_PROD', '=', $cod_prod)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->where('CANTIDAD', '>', '0')
            ->where('FECHA_VENC', '<>', $vencimiento)
            ->where('FECHA_VENC', '<=', $fecha)
            ->orderBy('FECHA_VENC', 'DESC')
            ->get();
       
        /*  --------------------------------------------------------------------------------- */

        // OBTENER VENTAS

        $vendidos = Ventas_det::select(
            DB::raw('VENTAS.ID AS ID,
                VENTASDET.PRECIO_UNIT AS PRECIO,
                IFNULL(VENTASDET.CANTIDAD, 0) AS CANTIDAD,
                VENTASDET.PRECIO AS TOTAL,
                substr(VENTASDET.FECALTAS, 1, 10) AS FECHA,
                substr(CLIENTES.NOMBRE, 1, 25) AS CLIENTE'))
            ->leftjoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                     ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL');
                            })
            ->leftjoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
            ->where('VENTASDET.COD_PROD', '=', $cod_prod)
            ->where('VENTASDET.DESCRIPCION','NOT LIKE', 'DESCUENTO%')
            ->where('VENTASDET.ID_SUCURSAL', '=', $user->id_sucursal)
            ->where('VENTASDET.ANULADO', '<>', '1')
            ->orderBy('VENTASDET.FECALTAS', 'DESC')
            ->limit(1)
            ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEVOLUCIONES A PROVEEDOR
        
        $devolucionProv = DB::connection('retail')->table('DEVOLUCION_PROV_DET')->select(
            DB::raw('DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV AS CODIGO,
                    IFNULL(DEVOLUCION_PROV_DET.CANTIDAD, 0) AS CANTIDAD,
                    DEVOLUCION_PROV_DET.COSTO AS COSTO,
                    DEVOLUCION_PROV_DET.COSTO_TOTAL AS TOTAL,
                    substr(DEVOLUCION_PROV.FECALTAS, 1, 10) AS FECHA,
                    LOTES.LOTE AS LOTE,
                    PROVEEDORES.NOMBRE AS PROVEEDOR,
                    DEVOLUCION_PROV.OBSERVACION AS MOTIVO'))
            ->join('DEVOLUCION_PROV', 'DEVOLUCION_PROV.ID', '=', 'DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV')
            ->leftjoin('LOTES', function($join){
                                $join->on('LOTES.ID', '=', 'DEVOLUCION_PROV_DET.FK_ID_LOTE')
                                     ->on('LOTES.ID_SUCURSAL', '=', 'DEVOLUCION_PROV.ID_SUCURSAL');
                            })
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'DEVOLUCION_PROV.FK_PROVEEDOR')
            ->where('DEVOLUCION_PROV_DET.COD_PROD', '=', $cod_prod)
            ->where('DEVOLUCION_PROV.ID_SUCURSAL', '=',$user->id_sucursal)
            ->orderBy('DEVOLUCION_PROV.FECALTAS', 'DESC')
            ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEVOLUCIONES DEL PRODUCTO
        
        $ventasDev =  DB::connection('retail')->table('VENTASDET_DEVOLUCIONES')->select(
            DB::raw('VENTAS.ID AS ID,
                VENTASDET_DEVOLUCIONES.PRECIO_UNIT AS PRECIO,
                IFNULL(VENTASDET_DEVOLUCIONES.CANTIDAD, 0) AS CANTIDAD,
                VENTASDET_DEVOLUCIONES.PRECIO AS TOTAL,
                substr(VENTASDET_DEVOLUCIONES.FECALTAS, 1, 10) AS FECHA,
                substr(CLIENTES.NOMBRE, 1, 25) AS CLIENTE'))
            ->leftjoin('VENTASDET', function($join){
                                $join->on('VENTASDET.ID', '=', 'VENTASDET_DEVOLUCIONES.FK_VENTASDET')
                                     ->on('VENTASDET.CAJA', '=', 'VENTASDET_DEVOLUCIONES.CAJA')
                                     ->on('VENTASDET.ID_SUCURSAL', '=', 'VENTASDET_DEVOLUCIONES.ID_SUCURSAL');
                            })
            ->leftjoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                     ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL');
                            })
            ->leftjoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
            ->where('VENTASDET_DEVOLUCIONES.COD_PROD', '=', $cod_prod)
            ->where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL', '=',$user->id_sucursal)
            ->orderBy('VENTASDET_DEVOLUCIONES.FECALTAS', 'DESC')
            ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER NOTAS DE CREDITO
        
        $credito = DB::connection('retail')->table('NOTA_CREDITO_DET')->select(
            DB::raw('NOTA_CREDITO_DET.FK_NOTA_CREDITO AS CODIGO,
                NOTA_CREDITO_DET.PRECIO AS PRECIO,
                IFNULL(NOTA_CREDITO_DET.CANTIDAD, 0) AS CANTIDAD,
                NOTA_CREDITO_DET.TOTAL AS TOTAL,
                substr(NOTA_CREDITO_DET.FECALTAS, 1, 10) AS FECHA,
                substr(CLIENTES.NOMBRE, 1, 25) AS CLIENTE'))
            ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
            ->leftjoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
                            })
            ->where('NOTA_CREDITO_DET.CODIGO_PROD', '=', $cod_prod)
            ->where('NOTA_CREDITO_DET.ID_SUCURSAL', '=',$user->id_sucursal)
            ->orderBy('NOTA_CREDITO_DET.FECALTAS', 'DESC')
            ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SALIDAS

        $salida = DB::connection('retail')->table('SALIDA_PRODUCTOS_DET')->select(
            DB::raw('SALIDA_PRODUCTOS.ID AS ID, 
                SALIDA_PRODUCTOS_DET.CANTIDAD AS CANTIDAD, 
                SALIDA_PRODUCTOS_DET.COSTO AS COSTO,
                SALIDA_PRODUCTOS_DET.COSTO_TOTAL AS TOTAL,
                SALIDA_PRODUCTOS.OBSERVACION AS MOTIVO,
                substr(SALIDA_PRODUCTOS.FECALTAS, 1, 10) AS FECHA,
                LOTES.LOTE AS LOTE'))
            ->leftjoin('SALIDA_PRODUCTOS', 'SALIDA_PRODUCTOS.ID','=', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
            ->where('SALIDA_PRODUCTOS_DET.COD_PROD', '=', $cod_prod)
            ->where('SALIDA_PRODUCTOS.ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('SALIDA_PRODUCTOS.FECALTAS', 'DESC')
            ->get();

        /*  --------------------------------------------------------------------------------- */

        //DAR FORMATO

        foreach ($vencidos as $key => $value) {
            $value->COSTO = Common::formato_precio($value->COSTO, $candec);
            $value->TOTAL = Common::formato_precio(($value->CANTIDAD *$value->COSTO), $candec);
        }

        foreach ($devolucionProv as $key => $value) {
            $value->COSTO = Common::formato_precio($value->COSTO, $candec);
            $value->TOTAL = Common::formato_precio($value->TOTAL, $candec);
            $value->PROVEEDOR = utf8_decode(utf8_encode(ucwords(strtolower($value->PROVEEDOR))));
            $value->MOTIVO = utf8_decode(utf8_encode(ucfirst(strtolower($value->MOTIVO))));
        }

        foreach ($ventasDev as $key => $value) {
            $value->PRECIO = Common::formato_precio($value->PRECIO, $candec);
            $value->TOTAL = Common::formato_precio($value->TOTAL, $candec);
            $value->CLIENTE = utf8_decode(utf8_encode(ucwords(strtolower($value->CLIENTE))));
        }

        foreach ($credito as $key => $value) {
            $value->PRECIO = Common::formato_precio($value->PRECIO, $candec);
            $value->TOTAL = Common::formato_precio($value->TOTAL, $candec);
            $value->CLIENTE = utf8_decode(utf8_encode(ucwords(strtolower($value->CLIENTE))));
        }   

        foreach ($salida as $key => $value) {
            $value->COSTO = Common::formato_precio($value->COSTO, $candec);
            $value->TOTAL = Common::formato_precio($value->TOTAL, $candec);
            $value->MOTIVO = utf8_encode(utf8_decode(ucfirst(strtolower($value->MOTIVO))));
        }
        /*  --------------------------------------------------------------------------------- */

        $movimientos = array(
            'ventas' => $vendidos,
            'vencidos' => $vencidos,
            'devolucionesProv' => $devolucionProv,
            'devolucionProd' => $ventasDev,
            'notaCredito' => $credito,
            'salida' => $salida
        );
        
        // RETORNAR VALOR  

        if (count($movimientos) > 0) {
            return ['movimientos' => $movimientos];
        } else {
            return ["movimientos" => false];
        }

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function ventaProducto($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'VENTASDET.FECALTAS', 
                            1 => 'VENTAS.ID',
                            2 => 'CLIENTES.NOMBRE',
                            3 => 'VENTASDET.CANTIDAD',
                            4 => 'VENTASDET.PRECIO_UNIT',
                            5 => 'VENTASDET.PRECIO',
                            6 => 'VENTASDET.FECALTAS',
                            7 => 'VENTASDET.HORALTAS'
                        );
        

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;
        $codigoP = $request->input('codigo');
        $parametro = Parametro::consultaPersonalizada('MONEDA');
        $candec = (Parametro::candec($parametro->MONEDA))['CANDEC'];

        $totalData = Ventas_det::where('VENTASDET.COD_PROD', '=', $codigoP)
                    ->where('VENTASDET.DESCRIPCION','NOT LIKE', 'DESCUENTO%')
                    ->where('VENTASDET.ID_SUCURSAL', '=', $user->id_sucursal)
                    ->where('VENTASDET.ANULADO', '<>', '1')
                    ->count();

        $totalFiltered = $totalData;

        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS DATOS ENCONTRADOS 

        $posts = Ventas_det::select(DB::raw('VENTAS.ID AS ID,
                        VENTASDET.PRECIO_UNIT AS PRECIO,
                        IFNULL(VENTASDET.CANTIDAD, 0) AS CANTIDAD,
                        VENTASDET.PRECIO AS TOTAL,
                        substr(VENTASDET.FECALTAS, 1, 10) AS FECHA,
                        VENTASDET.HORALTAS AS HORA,
                        substr(CLIENTES.NOMBRE, 1, 25) AS CLIENTE'))
                    ->leftjoin('VENTAS', function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                 ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL');
                            })
                    ->leftjoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
                ->where('VENTASDET.COD_PROD', '=', $codigoP)
                ->where('VENTASDET.DESCRIPCION','NOT LIKE', 'DESCUENTO%')
                ->where('VENTASDET.ID_SUCURSAL', '=', $user->id_sucursal)
                ->where('VENTASDET.ANULADO', '<>', '1')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

        $data = array();
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){
            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = strtolower($post->CLIENTE);
                $nestedData['ITEM'] = $item;
                $nestedData['ID'] = $post->ID;
                $nestedData['CLIENTE'] = utf8_decode(utf8_encode(ucwords($cliente)));
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['PRECIO'] = Common::formato_precio($post->PRECIO, $candec);
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, $candec);
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['HORA'] = $post->HORA;
                $data[] = $nestedData;
                $item = $item +1;

                /*  --------------------------------------------------------------------------------- */
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
}

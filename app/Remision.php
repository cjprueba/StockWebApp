<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Stock;
use App\Producto;
use App\Parametro;
use App\Sucursal;
use App\Common;
use Fpdf\Fpdf;
define('EURO', chr(128) );
define('EURO_VAL', 6.55957 );
class Remision extends Model
{
   
    protected $connection = 'retail';
    protected $table = 'remision';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    var $colonnes;
    var $format;
    var $angle=0;


    public static function guardarRemisiones($datos){
        
        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        // INICIAR VARIABLES 

        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        $c = 0;
        $filas = count($datos["productos"]);
        $cod_prod = '';
        $cantidad = 0;
        $cantidad_guardada = 0;
        $cantidad_total = 0;
        $precio = 0;
        $iva = 0;
        $base5 = 0;
        $base10 = 0;
        $exentas = 0;
        $total = 0;
        $sin_stock = [];
        $porcentaje = 0;
        $todos_guardados = true;

        $total_total = 0;
        $total_iva = 0;
        $total_subtotal = 0;

        /*  --------------------------------------------------------------------------------- */
        
        // PARAMETRO 
        
        $parametro = Parametro::mostrarParametro();
        $candec = $parametro['parametros'][0]->CANDEC;

        // SOLICITA ULTIMO CODIGO SI ES GUARDAR, SI ES MODIFICACION AGARRAR EL CODIGO ENVIADO

        if ($datos['data']['existe'] == false){

            $codigo = Remision::select(DB::raw('(CODIGO + 1) AS CODIGO'))
                    ->orderBy('CODIGO', 'desc')
                    ->limit(1)
                    ->get()->toArray();

            $codigo = $codigo[0]["CODIGO"];
            $opcion = 1;

        } else if ($datos['data']['existe'] == true) {

            $codigo = $datos['data']["codigo"];
            $opcion = 2;
        }

        try {

            // CONTROLA QUE NO EXISTA PARA INSERTAR

            if ($opcion == 1){

                //INSERTA LOS DATOS EN LA TABLA REMISION

                $remision=Remision::insertGetId(
                ['CODIGO'=> $codigo, 
                'CLIENTE'=> $datos['data']['usuario'],
                'PEDIDO' => 0,
                'IVA' => 0,
                'SUB_TOTAL' => 0,
                'TOTAL' => 0,
                'MONEDA' => $datos['data']['monedaSistema'],
                'FECHA' => $dia,
                'HORA' => $hora,
                'ENVIADO' => 'SI',
                'DEVUELTO' => 'NO',
                'USERALTAS'=> $user->name,
                'ID_SUCURSAL' => $user->id_sucursal,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora]);

            }

            /*  --------------------------------------------------------------------------------- */

            // RECORRER TODAS LAS FILAS DEL DATATABLE

            while($c < $filas) {


                /*  --------------------------------------------------------------------------------- */

                // INICIAR DATOS 

                $cod_prod = $datos["productos"][$c]["CODIGO"];
                $cantidad = Common::quitar_coma($datos["productos"][$c]["CANTIDAD"], $candec);
                $cantidad_total = $cantidad;
                $precio = Common::quitar_coma($datos["productos"][$c]["PRECIO"], $candec);
                $porcentaje = $datos["productos"][$c]["IVA_PORCENTAJE"];

                $total = Common::quitar_coma($datos["productos"][$c]["TOTAL"], $candec);

                /*  --------------------------------------------------------------------------------- */

                // OBTENER DATOS FALTANTES 

                $producto = Producto::datosVariosProducto($cod_prod);

                /*  --------------------------------------------------------------------------------- */ 

                // RESTAR STOCK DEL PRODUCTO

                $respuesta_resta = Stock::restar_stock_producto($cod_prod, $cantidad);
                        
                /*  --------------------------------------------------------------------------------- */ 

                // SI LA RESPUESTA TIENE DATOS GUARDA EL REGISTRO 

                if ($respuesta_resta["datos"]) {

                    /*  --------------------------------------------------------------------------------- */

                    // SUMAR LA CANTIDAD DEVUELTA POR EL ARRAY 

                    $cantidad_guardada = 0;

                    foreach ($respuesta_resta["datos"] as $key => $value) {
                        $cantidad_guardada = $cantidad_guardada + $value["cantidad"];
                    }

                    /*  --------------------------------------------------------------------------------- */

                    // SI LA CANTIDAD GUARDADA DIFIERE CON LA CANTIDAD A ENVIAR SE RECALCULA EL TOTAL

                    if ($cantidad !== $cantidad_guardada) {
                        $total = $precio * $cantidad_guardada;
                    }

                    /*  --------------------------------------------------------------------------------- */

                    // CALCULAR IVA

                    $impuesto = Common::calculo_iva($porcentaje, $total, $candec);

                            
                     /*  --------------------------------------------------------------------------------- */

                    // TOTALES 

                    $total_total = $total_total + $total;
                    $total_iva = $total_iva + $impuesto["impuesto"];

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR REMISION DET 

                    $remision_det = DB::connection('retail')
                            ->table('remision_det')
                            ->insertGetId([
                                'CODIGO' => $codigo, 
                                'ITEM' => $c + 1,
                                'CODIGO_PROD' => $cod_prod,
                                'DESCRIPCION' => $datos["productos"][$c]["DESCRIPCION"],
                                'TIPO' => $datos["productos"][$c]["ITEM"],
                                'CANTIDAD' => $cantidad_guardada,
                                'PRECIO' => $precio,
                                'EXENTAS' => round($impuesto["exentas"],$candec),
                                'GRABADAS' => round($impuesto["gravadas"], $candec),
                                'IVA' => round($impuesto["impuesto"], $candec),
                                'AVERIAS' => 0,
                                'TOTAL' => round($total, $candec),
                                'DESCUENTO' => $datos["productos"][$c]["DESCUENTO"],
                                'DEVUELTO' => 'NO',
                                'BASE5' => $impuesto["base5"],
                                'BASE10' => $impuesto["base10"],
                                'USERALTAS' => $user->name,
                                'FECALTAS' =>  $dia,
                                'HORALTAS' =>  $hora,
                                'ID_SUCURSAL' => $user->id_sucursal
                                ]
                            );

                    /*  --------------------------------------------------------------------------------- */

                }
                    
                /*  --------------------------------------------------------------------------------- */

                // CARGAR LOS PRODUCTOS CON LAS CANTIDADES QUE NO SE GUARDARON 

                if ($respuesta_resta["response"] === false){
                    $sin_stock[] = (array)['cod_prod' => $cod_prod, 'guardado' => (float)$cantidad - (float)$respuesta_resta["restante"], "restante" => $respuesta_resta["restante"], "cantidad" => $cantidad];
                }

                /*  --------------------------------------------------------------------------------- */
                        
                // AUMENTAR CONTADOR 

                $c++;

                /*  --------------------------------------------------------------------------------- */
            }

             if ($opcion === 1) {

                /*  --------------------------------------------------------------------------------- */ 

                // MODIFICAR LA NOTA DE REMISION RECIEN GUARDADA 

                $remision = Remision::where('CODIGO','=', $codigo)
                ->where('ID_SUCURSAL','=',$user->id_sucursal)
                ->update([
                    'IVA' => round($total_iva, $candec),
                    'SUB_TOTAL' => round(($total_total - $total_iva), $candec),
                    'TOTAL' => $total_total
                    ]);
                
                /*  --------------------------------------------------------------------------------- */ 

            }else{

                // SI EXISTE ACTUALIZA LOS DATOS EN LA TABLA REMISION

                $remision=Remision::Where('CODIGO','=',$codigo)
                    ->Where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->update(['CLIENTE'=> $datos['data']['usuario'],
                    'IVA' => round($total_iva, $candec),
                    'SUB_TOTAL' => round(($total_total - $total_iva), $candec),
                    'TOTAL' => $total_total,
                    'PEDIDO' => 0,
                    'ENVIADO' => 'SI',
                    'DEVUELTO' => 'NO',
                    'MONEDA' => $datos['data']['monedaSistema'],
                    'USERMODIF'=>$user->name,
                    'FECMODIF'=>$dia,
                    'HORMODIF'=>$hora]);
             
            }


        }catch(Exception $ex){ 

            return ["response"=>false,'statusText'=>$ex->getMessage()];
        }

        // REVISAR SI HAY PRODUCTOS QUE NO SE GUARDARON TOTALMENTE 

            if ($opcion === 1) {
                if (count($sin_stock) > 0) {
                    return ["response" => false, "productos" => $sin_stock];
                } else {
                    return ["response" => true];
                }
            }

            /*  --------------------------------------------------------------------------------- */

            if ($opcion === 2) {
                return true; 
            }
        
            /*  --------------------------------------------------------------------------------- */

    }

    public static function modificar($data){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data['data']["codigo"];

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR REMISION 

        $eliminar = Remision::eliminarRemisiones($data);

        /*  --------------------------------------------------------------------------------- */

        // SI ELIMINAR RETORNA FALSE - SE DETIENE LA MODIFICACION
         
        if ($eliminar["response"] === false) {
            return ["response" => false, "statusText" => $eliminar["statusText"]];
        }

        $data['data']["existe"] = true;

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR Remision 

        Remision::guardarRemisiones($data);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cabecera($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
       
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data;

        /*  --------------------------------------------------------------------------------- */

        $remision = Remision::select(DB::raw(
                        'CODIGO,
                        FECALTAS,
                        CLIENTE, 
                        IVA,
                        TOTAL,
                        SUB_TOTAL,
                        DIRECCION,
                        FECALTAS,
                        HORALTAS,
                        MONEDA'
                    ))->where('ID_SUCURSAL','=',  $user->id_sucursal)
                ->where('CODIGO','=', $codigo)
                ->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        return $remision;

    }

    public static function mostrar_cuerpo($data){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // SI ENVIA CODIGO ORIGEN 

         
        /*  --------------------------------------------------------------------------------- */
        // INICIAR VARIABLES

        $codigo = $data;

        /*  ------------------------------OBTENER DATOS--------------------------------------------------- */

        $remision = DB::connection('retail')
        ->table('REMISION_DET')
        ->select(DB::raw(
                        'REMISION_DET.ITEM, 
                        REMISION_DET.CODIGO_PROD, 
                        REMISION_DET.DESCRIPCION, 
                        REMISION_DET.CANTIDAD, 
                        REMISION_DET.PRECIO,
                        REMISION_DET.IVA,
                        REMISION_DET.EXENTAS,
                        REMISION_DET.BASE5,
                        REMISION_DET.BASE10,
                        REMISION_DET.TOTAL,
                        REMISION_DET.DESCUENTO,
                        REMISION.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ),
                 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = REMISION_DET.CODIGO_PROD) AND (l.ID_SUCURSAL = REMISION_DET.ID_SUCURSAL))),0) AS STOCK'))
        ->leftJoin('REMISION', function($join){
                                $join->on('REMISION.CODIGO', '=', 'REMISION_DET.CODIGO')
                                     ->on('REMISION.ID_SUCURSAL', '=', 'REMISION_DET.ID_SUCURSAL');
                            })
        ->where('REMISION_DET.ID_SUCURSAL','=',  $user->id_sucursal)
        ->where('REMISION_DET.CODIGO','=', $codigo)
        ->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        foreach ($remision as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // BUSCAR IVA PRODUCTO

            $producto = DB::connection('retail')
            ->table('PRODUCTOS')
            ->select(DB::raw('IMPUESTO'))
            ->where('CODIGO', '=', $value->CODIGO_PROD)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // CARGAR PORCENTAJE IVA IVA 

            $remision[$key]->IVA_PORCENTAJE = $producto[0]->IMPUESTO;

            /*  --------------------------------------------------------------------------------- */

            $remision[$key]->CANTIDAD = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $remision[$key]->PRECIO = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $remision[$key]->IVA = Common::precio_candec_sin_letra($value->IVA, $value->MONEDA);
            $remision[$key]->TOTAL = Common::precio_candec_sin_letra($value->TOTAL, $value->MONEDA);

        }

        return $remision;

        /*  --------------------------------------------------------------------------------- */

    }


    /*  --------------------------------HOJA A4 PDF------------------------------------------------- */

    public static function remision_pdf($datos){

        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $pag=1;
        $codigo = $datos['codigo']['codigo'];
        $local = Parametro::consultaPersonalizada('EMPRESA, DIRECCION, TIMBRADO');

        $parametro = Parametro::mostrarParametro();
        $candec = $parametro['parametros'][0]->CANDEC;

        $cabecera = Remision::mostrar_cabecera($codigo);
        $cuerpo = Remision::mostrar_cuerpo($codigo);
        $nombreUser = Remision::obtenerNombre($cabecera[0]["CLIENTE"]);
        $cantidad = count($cuerpo);
        $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
        $pdf->AddPage();
        $pdf->addSociete($local->EMPRESA,
                          "$local->DIRECCION\n" .
                          "CLIENTE: ". $nombreUser[0]->name
                          // "R.C.S. PARIS B 000 000 007\n" ."Capital : 18000 " . EURO 
                          );
        $pdf->fact_dev(utf8_decode("Cotización"), $hora);
        // $pdf->temporaire(utf8_decode("Cotización temporal"));
        $pdf->addDate($dia);
        $pdf->addClient($cabecera[0]["CLIENTE"]);
        $pdf->addPageNumber($pag);
        // $pdf->addClientAdresse(utf8_decode("Av.\nDr. Luis Maria Argaña\n2° Piso\n33, rue d'ailleurs\n75000 PARIS"));
        $pdf->addReglement("Verificar los datos");
        $pdf->addEcheance('2025-12-30');
        $pdf->addNumIVA("FR888777666");
        $pdf->addReferencia(utf8_decode("Cotización ... de ...."));
        $cols=array( "REFERENCIA"    => 25,
                     "DESCRIPCION"  => 76,
                     "CANTIDAD"     => 22,
                     "P. UNITARIO"      => 26,
                     "TOTAL" => 24,
                     "IVA"          => 17 );
        $pdf->addCols( $cols);
        $cols=array( "REFERENCIA"    => "L",
                     "DESCRIPCION"  => "L",
                     "CANTIDAD"     => "C",
                     "P. UNITARIO"      => "C",
                     "TOTAL" => "C",
                     "IVA"          => "C" );
        $pdf->addLineFormat($cols);
        $y = 59;
        foreach ($cuerpo as $key => $value) {

            if($y > 232){
                $pag=$pag+1;
                $pdf->AddPage();
                $pdf->addSociete($local->EMPRESA,
                                  "$local->DIRECCION\n" .
                                  "CLIENTE: ". $nombreUser[0]->name
                                  // "R.C.S. PARIS B 000 000 007\n" ."Capital : 18000 " . EURO 
                                  );
                $pdf->fact_dev(utf8_decode("Cotización"), $hora);
                // $pdf->temporaire(utf8_decode("Cotización temporal"));
                $pdf->addDate($dia);
                $pdf->addClient($cabecera[0]["CLIENTE"]);
                $pdf->addPageNumber($pag);
                // $pdf->addClientAdresse(utf8_decode("Av.\nDr. Luis Maria Argaña\n2° Piso\n33, rue d'ailleurs\n75000 PARIS"));
                $pdf->addReglement("Verificar al recibir la factura");
                $pdf->addEcheance('2025-12-30');
                $pdf->addNumIVA("FR888777666");
                $pdf->addReferencia(utf8_decode("Cotización ... de ...."));
                $cols=array( "REFERENCIA"    => 23,
                             "DESCRIPCION"  => 78,
                             "CANTIDAD"     => 22,
                             "P. UNITARIO"      => 26,
                             "TOTAL" => 24,
                             "IVA"          => 17 );
                $pdf->addCols( $cols);
                $cols=array( "REFERENCIA"    => "L",
                             "DESCRIPCION"  => "L",
                             "CANTIDAD"     => "C",
                             "P. UNITARIO"      => "R",
                             "TOTAL" => "R",
                             "IVA"          => "C" );
                $pdf->addLineFormat( $cols);
                $y = 59;
            }

            $line = array( "REFERENCIA"    => $value->CODIGO_PROD,
                       "DESCRIPCION"  => utf8_decode($value->DESCRIPCION),
                       "CANTIDAD"     => $value->CANTIDAD,
                       "P. UNITARIO"      => $value->PRECIO,
                       "TOTAL" => $value->TOTAL,
                       "IVA"          => $value->IVA);
            $size = $pdf->addLine( $y, $line );
            $y   += $size + 3;
           
            // $pdf->addCadreIVAs();

            // $tot_prods = array( array ( "px_unit" => 600, "qte" => 1, "IVA" => 1 ),
            //                 array ( "px_unit" =>  10, "qte" => 1, "IVA" => 1 ));
            // $tab_IVA = array( "1"       => 19.6,
            //               "2"       => 5.5);
            // $params  = array( "RemiseGlobale" => 1,
            //                   "remise_IVA"     => 1,       // {la remise s'applique sur ce code IVA}
            //                   "DESCUENTO"         => 0,       // {montant de la remise}
            //                   "remise_percent" => 10,      // {pourcentage de remise sur ce montant de IVA}
            //               "FraisPort"     => 1,
            //                   "portTTC"        => 10,      // montant des frais de ports TTC
            //                                                // par defaut la IVA = 19.6 %
            //                   "portHT"         => 0,       // montant des frais de ports HT
            //                   "portIVA"        => 19.6,    // valeur de la IVA a appliquer sur le montant HT
            //               "AccompteExige" => 1,
            //                   "accompte"         => 0,     // montant de l'acompte (TTC)
            //                   "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
            //               "Nota" => "Avec un acompte, svp..." );

            // $tot_prods = array( array ( "px_unit" => $value->PRECIO, "qte" => $value->CANTIDAD, "IVA" => $value->IVA));
            // $params  = array("DESCUENTO" => $value->DESCUENTO,"RemiseGlobale" => 1,
            //                   "remise_IVA"     => 1,       // {montant de la remise}
            //                   "remise_percent" => 10,      // {pourcentage de remise sur ce montant de IVA}
            //                   "FraisPort"     => 1,
            //                   "portTTC"        => 10,      // montant des frais de ports TTC
            //                                                // par defaut la IVA = 19.6 %
            //                   "portHT"         => 0,       // montant des frais de ports HT
            //                   "portIVA"  => $cabecera[0]["IVA"],    // valeur de la IVA a appliquer sur le montant HT
            //               "AccompteExige" => 1,
            //                   "accompte"         => 0,     // montant de l'acompte (TTC)
            //                   "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
            //                  "Nota" => utf8_decode("Con un depósito, por favor...")
                      // );
        }
        $pdf->addCadreIVAs();
        $pdf->addIVAs($cuerpo, $cabecera, $candec);
        $pdf->addCadreEurosFrancs();
        $pdf->Output();
        
        // $line = array( "REFERENCIA"    => "REF2",
        //                "DESCRIPCION"  => "Câble RS232",
        //                "CANTIDAD"     => "1",
        //                "P. UNITARIO"      => "10.00",
        //                "TOTAL" => "60.00",
        //                "IVA"          => "1" );
        // $size = $pdf->addLine( $y, $line );
        // $y   += $size + 2;

        
                
        // invoice = array( "px_unit" => value,
        //                  "qte"     => qte,
        //                  "IVA"     => code_IVA );
        // tab_IVA = array( "1"       => 19.6,
        //                  "2"       => 5.5, ... );
        // params  = array( "RemiseGlobale" => [0|1],
        //                      "remise_IVA"     => [1|2...],  // {la remise s'applique sur ce code IVA}
        //                      "DESCUENTO"         => value,     // {montant de la remise}
        //                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de IVA}
        //                  "FraisPort"     => [0|1],
        //                      "portTTC"        => value,     // montant des frais de ports TTC
        //                                                     // par defaut la IVA = 19.6 %
        //                      "portHT"         => value,     // montant des frais de ports HT
        //                      "portIVA"        => IVA_value, // valeur de la IVA a appliquer sur le montant HT
        //                  "AccompteExige" => [0|1],
        //                      "accompte"         => value    // montant de l'acompte (TTC)
        //                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
        //                  "Nota" => "texte"              // texte
        
    }

    public static function ultimo_codigo() {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */ 

        // OBTENER CODIGO 

        $remision = Remision::select(DB::raw('(CODIGO + 1) AS CODIGO'))
                ->orderBy('CODIGO', 'desc')
                ->limit(1)
                ->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        // ULTIMO CODIGO 

        if (count($remision) > 0) {
           return ["remision" => $remision];
        } else {
            return 1;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtenerNombre($datos){

        $user = auth()->user();


        // OBTENER LOS DATOS

        $nombreUser = DB::connection('retail')->table('users')
        ->select(DB::raw('name'))
        ->where('id', '=', $datos)->limit(1)->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

       return $nombreUser;
    }

    public static function filtrarRemisiones($datos){

        $user = auth()->user();


        // OBTENER LOS DATOS DE LA TABLA REMISION

        $remision = Remision::mostrar_cabecera($datos['data']);

        $nombreUser = Remision::obtenerNombre($remision[0]["CLIENTE"]);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

       return ["response" => true, "remision"=>$remision, "usuario" => $nombreUser];
    }

    public static function eliminarRemisiones($datos){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["data"]['codigo'];
        $estatus = '';

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE
        
        if (Remision::verificar_existencia($codigo) === false) {
            return ["response" => false, "statusText" => "¡No existe Nota de Remision!"];
        }


        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO Y LA CANTIDA DEL PRODUCTO 

        $remision_det = DB::connection('retail')
        ->table('remision_det')
        ->select(DB::raw(
                        'ID,
                        CODIGO_PROD, 
                        CANTIDAD'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER FILAS DE REMISION DET 

        foreach ($remision_det as $key => $td) {

            /*  --------------------------------------------------------------------------------- */

            // ELIMINAR CLAVES FORANEAS DE REMISION TIENE LOTES 
            
            $tdtl = DB::connection('retail')
                        ->table('lotes')
                        ->select(DB::raw('LOTE'))
                        ->where('ID_SUCURSAL','=', $user->id_sucursal)
                        ->where('COD_PROD','=', $td->CODIGO_PROD)
                        ->orderBy('LOTE', 'desc')
                        ->limit(1)
                        ->get()->toArray();
            
            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER STOCK 

            foreach ($tdtl as $key => $value) {

                Stock::sumar_stock_producto($td->CODIGO_PROD, $td->CANTIDAD, $value->LOTE);

            }

            /*  --------------------------------------------------------------------------------- */

        }
   
        /*  --------------------------------------------------------------------------------- */

        //ELIMINA DE LA TABLA REMISION Y REMISION_DET


        $remision_det = DB::connection('retail')->table('REMISION_DET')
            ->where('CODIGO','=',$codigo)
            ->Where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->delete();
 

        if ($datos['data']['existe'] == true) {

           $remision = Remision::where('CODIGO','=',$codigo)
                    ->Where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->delete();
        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

    }
     
    public static function verificar_existencia($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $remision = Remision::select(DB::raw(
                        'CODIGO'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 

        if(count($remision) > 0){
            return true;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

    }
    public static function remisionDatatable($request){

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 

                        0 => 'id',
                        1 => 'codigo',
                        2 => 'cliente',
                        3 => 'iva',
                        4 => 'sub_total',
                        5 => 'total',
                        6 => 'moneda'
                        
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE NOTAS ENCONTRADAS 

        $totalData = Remision::select('id')->where('ID_SUCURSAL', '=', $user->id_sucursal)->count();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */


        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Remision::select(DB::raw('ID, CODIGO, CLIENTE, IVA, SUB_TOTAL, TOTAL, MONEDA'))
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LAS NOTAS FILTRADAS EN DATATABLE

            $posts =Remision::select(DB::raw('ID, CODIGO, CLIENTE, IVA, SUB_TOTAL, TOTAL, MONEDA'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                            })
                            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();


            // CARGAR LA CANTIDAD DE NOTAS FILTRADOS 

            $totalFiltered = Remision::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                            })->where('ID_SUCURSAL', '=', $user->id_sucursal)
                             ->count();

            /*  ************************************************************ */  

        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

             /*  --------------------------------------------------------------------------------- */

             // CARGA EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CLIENTE'] = $post->CLIENTE;
                $nestedData['IVA'] = Common::precio_candec_sin_letra($post->IVA, $post->MONEDA);
                $nestedData['SUB_TOTAL'] = Common::precio_candec_sin_letra($post->SUB_TOTAL, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);
                $nestedData['MONEDA'] = $post->MONEDA;


                $data[] = $nestedData;

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

    }

    public static function ticket_pdf($dato) {

        //$dato = ['codigo' => 820671264471, 'caja' => 1];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $remision = Remision::mostrar_cabecera($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $remision_det = Remision::mostrar_cuerpo($dato);

        $nombreUser = Remision::obtenerNombre($remision[0]["CLIENTE"]);
        
        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $parametro = Parametro::mostrarParametro();
        $candec = $parametro['parametros'][0]->CANDEC;
        $moneda = $remision[0]["MONEDA"];

        /*  --------------------------------------------------------------------------------- */

        // PARAMETROS 

        $mensaje = Parametro::select(DB::raw('MENSAJE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        $sucursal = Sucursal::encontrarSucursal(['codigoOrigen' => $user->id_sucursal]);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $nombre_sucursal = $sucursal['sucursal'][0]['DESCRIPCION'];
        $codigo = $remision[0]["CODIGO"];
        $cliente = $remision[0]["CLIENTE"];
        $direccion = $remision[0]["DIRECCION"];
        // $cien = 100;
        // $ruc = $remision->RUC;
        // $tipo = $remision->TIPO;
        $fecha = substr($remision[0]["FECALTAS"],0,10);
        $hora = $remision[0]["HORALTAS"];
        // $caja = $remision->CAJA;
        // $vendedor = $remision->VENDEDOR;
        // $cajero = $remision->CAJERO;
        // $pago = $remision->PAGO;
        // $descuento = $remision->DESCUENTO;
        // $vuelto = $remision->VUELTO;
        // $ticket = $remision->CODIGO_CA;
        // $tipo = $remision->TIPO;
        // $documento = $remision[0]["RUC"];
        $nombre = 'Remision_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $switch_hojas = false;
        $namefile = 'Boleta_de_Nota_de_Remision'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */
        
        $pdf = new FPDF('P','mm',array(75,150));
        $pdf->AddPage();
         
        // CABECERA

        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(55,4, $nombre_sucursal ,0,1,'C');
        $pdf->Ln(1);
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(26,4, 'Ticket: 00'.$codigo,0,0,'L');
        $pdf->Cell(30,4, 'Fecha: '.$fecha ,0,1,'R');
        $pdf->Cell(26,4, 'Caja: '."1" ,0,0,'L');
        $pdf->Cell(30,4, 'Hora: '.$hora ,0,1,'R');

        // $pdf->Cell(20, 4, 'Vendedor:', 0);   
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(10, 4, " --",0,0,'R');
        // $pdf->Cell(20, 4, 'Cajero:'." --", 0);   
        // $pdf->Ln(4);

        // DATOS FACTURA
        // $pdf->Cell(55,4,'Factura Simple: F2020-000001',0,1,'');
        // $pdf->Cell(55,4,'Fecha: 28/10/2025',0,1,'');
        // $pdf->Cell(55,4,'Metodo de pago: Tarjeta',0,1,'');
         
        // COLUMNAS

        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(15, 10, 'Articulo', 0);
        $pdf->Cell(15, 10, 'Cant.',0,0,'R');
        $pdf->Cell(13, 10, 'Precio',0,0,'R');
        $pdf->Cell(13, 10, 'Total',0,0,'R');
        $pdf->Ln(7);
        $pdf->Cell(55,0,'','T');
        $pdf->Ln(2);

        /*  --------------------------------------------------------------------------------- */

        // PRODUCTOS
        
        foreach ($remision_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */
                
            $articulos["precio"] = $value->PRECIO;
            $articulos["total"] = $value->TOTAL;
            $exentas = $exentas + Common::quitar_coma($value->EXENTAS, $candec);
            $total = $total + Common::quitar_coma($value->TOTAL, $candec);
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos["cantidad"] = $value->CANTIDAD;
            $articulos["cod_prod"] = $value->CODIGO_PROD;
            $articulos["descripcion"] = utf8_decode(substr($value->DESCRIPCION, 0,38));
            $cantidad = $cantidad + $value->CANTIDAD;

            $articulos["descuento"] = $value->DESCUENTO;
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 

            if ($value->EXENTAS > 0) {
                $articulos["exentas"] = $articulos["total"];
            } else {
                $articulos["exentas"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE BASE5 O BASE10

            if ($value->BASE5 > 0) {
                $articulos["base10"] = '';
                $articulos["base5"] = $articulos["total"];
                $base5 = $base5 + Common::quitar_coma($articulos["total"], $candec);
            } else if ($value->BASE10 > 0) {
                $articulos["base5"] = '';
                $articulos["base10"] = $articulos["total"];
                $base10 = $base10 + Common::quitar_coma($articulos["total"], $candec);
            } else {
                $articulos["base5"] = '';
                $articulos["base10"] = '';
            }
                
            

            /*  --------------------------------------------------------------------------------- */

            // BUSCAR DESCUENTOS 

            // $descuento_producto = Ventas_det_Descuento::select(DB::raw('PORCENTAJE, TOTAL'))
            // ->WHERE('FK_VENTASDET', '=', $value["ID"])
            // ->WHERE('FK_COD_PROD', '=', $value["COD_PROD"])
            // ->get();

            /*  --------------------------------------------------------------------------------- */

            if ($articulos["descuento"] > 0) {

                /*  --------------------------------------------------------------------------------- */

                $pdf->SetFont('Helvetica', '', 7);
                $pdf->Cell(10,4,$articulos["cod_prod"],0,'L'); 
                $pdf->Cell(20, 4, $articulos["cantidad"],0,0,'R');
                $pdf->Cell(13, 4, $articulos["precio"],0,0,'R');
                $pdf->Cell(13, 4, $articulos["total"],0,0,'R');
                $pdf->Ln(3);
                $pdf->SetFont('Helvetica', '', 6);
                $pdf->Cell(0,4,$articulos["descripcion"].".",0,1,'L');
                $pdf->Cell(0,2,'DESCUENTO '.$articulos["descuento"].'%',0,1,'L');
                $pdf->Ln(2); 
                $descuento = 0;


                /*  --------------------------------------------------------------------------------- */

            }else{

                /*  --------------------------------------------------------------------------------- */

                    // CARGAR PRODUCTOS 

                    $pdf->SetFont('Helvetica', '', 7);
                    $pdf->Cell(10,4,$articulos["cod_prod"],0,'L'); 
                    $pdf->Cell(20, 4, $articulos["cantidad"],0,0,'R');
                    $pdf->Cell(13, 4, $articulos["precio"],0,0,'R');
                    $pdf->Cell(13, 4, $articulos["total"],0,0,'R');
                    $pdf->Ln(3);
                    $pdf->SetFont('Helvetica', '', 6);
                    $pdf->Cell(0,4,$articulos["descripcion"].".",0,1,'L');
                    $pdf->Ln(2);
            }
        }
         
        // SUMATORIO DE LOS PRODUCTOS Y EL IVA

        $pdf->Cell(55,0,'','T');
        $pdf->SetFont('Helvetica', 'B', 7);  
        $pdf->Ln(1);  
        $pdf->Cell(15, 10, 'I.V.A.:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, Common::precio_candec_sin_letra($remision[0]["IVA"], $moneda),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(15, 10, 'SUBTOTAL:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, Common::precio_candec_sin_letra($remision[0]["SUB_TOTAL"], $moneda),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(15, 10, 'TOTAL A PAGAR:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, Common::precio_candec_sin_letra($total, $moneda),0,0,'R');
        
        $pdf->Ln(7);
        $pdf->Cell(55,0,'','T');
        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(15, 10, 'PAGO:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, Common::precio_candec($total, $moneda),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(15, 10, 'VUELTO:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, Common::precio_candec(0, $moneda),0,0,'R');

        $pdf->Ln(7);
        $pdf->Cell(55,0,'','T');
        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(15, 10, 'CLIENTE:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, $cliente,0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(15, 10, 'R.U.C./C.I.:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, "--" ,0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);     
        $pdf->Cell(15, 10, 'TIPO VENTA:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(41, 10, utf8_decode("Remisión"),0,0,'R');

        // PIE DE PAGINA

        $pdf->Ln(7);
        $pdf->Cell(55,0,'','T');
        $pdf->Ln(7);
        $pdf->Cell(55,0,$mensaje[0]->MENSAJE,0,1,'C');
         
        $pdf->Output('ticket.pdf','i');

    }

}

class PDF_Invoice extends FPDF{
        // private variables
        var $colonnes;
        var $format;
        var $angle=0;

        // private functions
        function RoundedRect($x, $y, $w, $h, $r, $style = '')
        {
            $k = $this->k;
            $hp = $this->h;
            if($style=='F')
                $op='f';
            elseif($style=='FD' || $style=='DF')
                $op='B';
            else
                $op='S';
            $MyArc = 4/3 * (sqrt(2) - 1);
            $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
            $xc = $x+$w-$r ;
            $yc = $y+$r;
            $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
            $xc = $x+$w-$r ;
            $yc = $y+$h-$r;
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
            $xc = $x+$r ;
            $yc = $y+$h-$r;
            $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
            $xc = $x+$r ;
            $yc = $y+$r;
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
            $this->_out($op);
        }

        function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
        {
            $h = $this->h;
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
                                $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
        }

        function Rotate($angle, $x=-1, $y=-1)
        {
            if($x==-1)
                $x=$this->x;
            if($y==-1)
                $y=$this->y;
            if($this->angle!=0)
                $this->_out('Q');
            $this->angle=$angle;
            if($angle!=0)
            {
                $angle*=M_PI/180;
                $c=cos($angle);
                $s=sin($angle);
                $cx=$x*$this->k;
                $cy=($this->h-$y)*$this->k;
                $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
            }
        }

        function _endpage()
        {
            if($this->angle!=0)
            {
                $this->angle=0;
                $this->_out('Q');
            }
            parent::_endpage();
        }

        // public functions
        function sizeOfText( $texte, $largeur )
        {
            $index    = 0;
            $nb_lines = 0;
            $loop     = TRUE;
            while ( $loop )
            {
                $pos = strpos($texte, "\n");
                if (!$pos)
                {
                    $loop  = FALSE;
                    $ligne = $texte;
                }
                else
                {
                    $ligne  = substr( $texte, $index, $pos);
                    $texte = substr( $texte, $pos+1 );
                }
                $length = floor( $this->GetStringWidth( $ligne ) );
                $res = 1 + floor( $length / $largeur) ;
                $nb_lines += $res;
            }
            return $nb_lines;
        }

        // Company
        function addSociete( $nom, $adresse )
        {
            $x1 = 10;
            $y1 = 8;
            //Positionnement en bas
            $this->SetXY( $x1, $y1 );
            $this->SetFont('Arial','B',12);
            $length = $this->GetStringWidth( $nom );
            $this->Cell( $length, 2, $nom);
            $this->SetXY( $x1, $y1 + 5 );
            $this->SetFont('Arial','',10);
            $length = $this->GetStringWidth( $adresse );
            //Coordonnées de la société
            $lignes = $this->sizeOfText( $adresse, $length) ;
            $this->MultiCell($length, 5, $adresse);
        }

        // Label and number of invoice/estimate
        function fact_dev( $libelle, $num )
        {
            $r1  = $this->w - 80;
            $r2  = $r1 + 68;
            $y1  = 6;
            $y2  = $y1 + 2;
            $mid = ($r1 + $r2 ) / 2;
            
            $texte  = $libelle . " en " . 'gs' . utf8_decode(" n°: ") . $num;    
            $szfont = 11;
            $loop   = 0;
            
            while ( $loop == 0 )
            {
               $this->SetFont( "Arial", "B", $szfont );
               $sz = $this->GetStringWidth( $texte );
               if ( ($r1+$sz) > $r2 )
                  $szfont --;
               else
                  $loop ++;
            }

            $this->SetLineWidth(0.1);
            $this->SetFillColor(255);
            $this->RoundedRect($r1, $y1, ($r2 - $r1+2), $y2, 2.5, 'DF');
            $this->SetXY( $r1+1, $y1+2);
            $this->Cell($r2-$r1 -1,5, $texte, 0, 0, "C" );
        }

        // Estimate
        function addDevis( $numdev )
        {
            $string = sprintf("DEV%04d",$numdev);
            $this->fact_dev( "Devis", $string );
        }

        // Invoice
        function addFacture( $numfact )
        {
            $string = sprintf("FA%04d",$numfact);
            $this->fact_dev( "Factura", $string );
        }

        function addDate( $date )
        {
            $r1  = $this->w - 61;

            //anchor del cuadro
            $r2  = $r1 + 30;

            $y1  = 15;
            // primera linea horizontal
            $y2  = $y1 ;
            // linea del medio
            $mid = $y1 + (6);
            $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2-2, 3.5, 'D');
            // ------------1    
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+1 );
            $this->SetFont( "Arial", "B", 8);
            $this->Cell(10,5, "DIA", 0, 0, "C");
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 7 );
            $this->SetFont( "Arial", "", 8);
            $this->Cell(10,5,$date, 0,0, "C");
        }

        function addClient( $ref )
        {
            $r1  = $this->w - 31;
            $r2  = $r1 + 19;
            $y1  = 15;
            $y2  = $y1;
            $mid = $y1 + (6);
            $this->RoundedRect($r1, $y1, ($r2 - $r1+2), $y2-2, 3.5, 'D');
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+1 );
            $this->SetFont( "Arial", "B", 8);
            $this->Cell(10,5, "CLIENTE", 0, 0, "C");
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 7 );
            $this->SetFont( "Arial", "", 8);
            $this->Cell(10,5,$ref, 0,0, "C");
        }

        function addPageNumber( $page )
        {
            $r1  = $this->w - 80;
            $r2  = $r1 + 19;
            $y1  = 15;
            $y2  = $y1;
            $mid = $y1 + (6);
            $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2-2, 3.5, 'D');
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+1 );
            $this->SetFont( "Arial", "B", 8);
            $this->Cell(10,5, "PAGINA", 0, 0, "C");
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 7 );
            $this->SetFont( "Arial", "", 8);
            $this->Cell(10,5,$page, 0,0, "C");
        }

        // Client address
        function addClientAdresse( $adresse )
        {
            $r1     = $this->w - 80;
            $r2     = $r1 + 68;
            $y1     = 40;
            $this->SetXY( $r1, $y1);
            $this->MultiCell( 60, 4, $adresse);
        }

        // Mode of payment
        function addReglement( $mode )
        {
            $r1  = 10;
            $r2  = $r1 + 65;
            $y1  = 30;
            $y2  = $y1+10;
            $mid = $y1 + (($y2-$y1) / 2);
            $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
            $this->SetFont( "Arial", "B", 9);
            $this->Cell(10,4, "MODO DE PAGO", 0, 0, "C");
            $this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 5 );
            $this->SetFont( "Arial", "", 9);
            $this->Cell(10,5,$mode, 0,0, "C");
        }

        // Expiry date
        function addEcheance( $date )
        {
            $r1  = 80;
            $r2  = $r1 + 50;
            $y1  = 30;
            $y2  = $y1+10;
            $mid = $y1 + (($y2-$y1) / 2);
            $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 3, 'D');
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + ($r2 - $r1)/2 - 5 , $y1+1 );
            $this->SetFont( "Arial", "B", 9);
            $this->Cell(10,4, "FECHA DE VENCIMIENTO", 0, 0, "C");
            $this->SetXY( $r1 + ($r2-$r1)/2 - 5 , $y1 + 5 );
            $this->SetFont( "Arial", "", 9);
            $this->Cell(10,5,$date, 0,0, "C");
        }

        // VAT number
        function addNumIVA($IVA)
        {
            $this->SetFont( "Arial", "B", 9);
            $r1  = $this->w - 75;
            $r2  = $r1 + 65;
            $y1  = 30;
            $y2  = $y1+10;
            $mid = $y1 + (($y2-$y1) / 2);
            $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 3, 'D');
            $this->Line( $r1, $mid, $r2, $mid);
            $this->SetXY( $r1 + 16 , $y1+1 );
            $this->Cell(30, 4, "IVA INTRACOMUNITARIO", 0, 0, "C");
            $this->SetFont( "Arial", "", 9);
            $this->SetXY( $r1 + 16 , $y1+5 );
            $this->Cell(45, 5, $IVA, '', '', "C");
        }

        function addReferencia($ref)
        {
            $this->SetFont( "Arial", "", 10);
            $length = $this->GetStringWidth( "Referencias: " . $ref );
            $r1  = 10;
            $r2  = $r1 + $length;
            $y1  = 43;
            $y2  = $y1+5;
            $this->SetXY( $r1 , $y1 );
            $this->Cell($length,4, "Referencias: " . $ref);
        }

        function addCols( $tab )
        {
            global $colonnes;
            // R1 ES ANCHO
            $r1  = 10;
            $r2  = $this->w - ($r1 * 2) ;
            //Y1 ES ALTURA
            $y1  = 49;
            $y2  = $this->h - 50 - $y1;
            $this->SetXY( $r1, $y1 );
            $this->Rect( $r1, $y1, $r2, $y2, "D");
            $this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
            $colX = $r1;
            $colonnes = $tab;

            foreach ($tab as $lib => $pos) {
                $this->SetXY( $colX, $y1+3 );
                $this->Cell( $pos, 1, $lib, 0, 0, "C");
                $colX += $pos;
                $this->Line( $colX, $y1, $colX, $y1+$y2);
            }
        }

        function addLineFormat( $tab )
        {
            global $format, $colonnes;

           foreach ($colonnes as $lib => $pos) {

                if ( isset( $tab["$lib"] ) )
                    $format[ $lib ] = $tab["$lib"];
            }
        }

        function lineVert( $tab )
        {
            global $colonnes;

            reset( $colonnes );
            $maxSize=0;
            foreach ($colonnes as $lib => $pos) {

                $texte = $tab[ $lib ];
                $longCell  = $pos -2;
                $size = $this->sizeOfText( $texte, $longCell );
                if ($size > $maxSize)
                    $maxSize = $size;
            }
            return $maxSize;
        }

        // add a line to the invoice/estimate
        /*    $ligne = array( "REFERENCIA"    => $prod["ref"],
                              "DESCRIPCION"  => $libelle,
                              "CANTIDAD"     => sprintf( "%.2F", $prod["qte"]) ,
                              "P. UNITARIO"      => sprintf( "%.2F", $prod["px_unit"]),
                              "TOTAL" => sprintf ( "%.2F", $prod["qte"] * $prod["px_unit"]) ,
                              "IVA"          => $prod["IVA"] );
        */
        function addLine( $ligne, $tab )
        {
            global $colonnes, $format;

            $ordonnee     = 10;
            $maxSize      = $ligne;

            reset( $colonnes );

            foreach ($colonnes as $lib => $pos) 
            {
                $longCell  = $pos -2;
                $texte     = $tab[ $lib ];
                $length    = $this->GetStringWidth( $texte );
                $tailleTexte = $this->sizeOfText( $texte, $length );
                $formText  = $format[ $lib ];
                $this->SetXY( $ordonnee, $ligne-1);
                $this->MultiCell( $longCell, 4 , $texte, 0, $formText);
                if ( $maxSize < ($this->GetY()  ) )
                    $maxSize = $this->GetY() ;
                $ordonnee += $pos;
            }
            return ( $maxSize - $ligne );
        }

        function addNota($Nota)
        {
            $this->SetFont( "Arial", "", 9);
            $length = $this->GetStringWidth( "Nota : " . $Nota );
            $r1  = 10;
            $r2  = $r1 + $length;
            $y1  = $this->h - 45.5;
            $y2  = $y1+5;
            $this->SetXY( $r1 , $y1 );
            $this->Cell($length,4, "Nota : " . $Nota);
        }

        function addCadreIVAs()
        {
            $this->SetFont( "Arial", "B", 8);
            $r1  = 10;
            $r2  = $r1 + 120;
            $y1  = $this->h - 40;
            $y2  = $y1+20;
            $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
            $this->Line( $r1, $y1+4, $r2, $y1+4);
            $this->Line( $r1+25, $y1, $r1+25, $y2);  // avant MT IVA
            $this->Line( $r1+49, $y1, $r1+49, $y2);  // avant PORT
            $this->Line( $r1+70, $y1, $r1+70, $y2);  // avant TOTAUX
            $this->Line( $r1+91, $y1, $r1+91, $y2);  // avant TOTAUX
            $this->SetXY( $r1+6, $y1);
            $this->Cell(10,4, "TOTAL");
            $this->SetX( $r1+28 );
            $this->Cell(10,4, "SUBTOTAL");
            $this->SetX( $r1+51 );
            $this->Cell(10,4, "TOTAL IVA");
            $this->SetX( $r1+75 );
            $this->Cell(10,4, "IVA %");
            $this->SetX( $r1+96 );
            $this->Cell(10,4, "DESCUENTO %");
            $this->SetFont( "Arial", "B", 6);
            $this->SetXY( $r1+93, $y2 - 8 );
           // $this->Cell(6,0, "H.T.   :");
            // $this->SetXY( $r1+93, $y2 - 3 );
            // $this->Cell(6,0, "I.V.A. :");
        }

        function addCadreEurosFrancs()
        {
            $r1  = $this->w - 70;
            $r2  = $r1 + 60;
            $y1  = $this->h - 40;
            $y2  = $y1+20;
            $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
            $this->Line( $r1+20,  $y1, $r1+20, $y2); // avant EUROS
            $this->Line( $r1+20, $y1+4, $r2, $y1+4); // Sous Euros & Francs
            $this->Line( $r1+42,  $y1, $r1+42, $y2); // Entre Euros & Francs
            $this->SetFont( "Arial", "B", 8);
            $this->SetXY( $r1+23, $y1 );
            $this->Cell(15,4, "GUARANIES", 0, 0, "C");
            $this->SetFont( "Arial", "", 8);
            $this->SetXY( $r1+43, $y1 );
            $this->Cell(15,4, "DOLAR", 0, 0, "C");
            $this->SetFont( "Arial", "B", 7);
            $this->SetXY( $r1, $y1+5 );
            $this->Cell(20,4, "TOTAL", 0, 0, "C");
            $this->SetXY( $r1, $y1+10 );
            $this->Cell(20,4, "SUBTOTAL", 0, 0, "C");
            $this->SetXY( $r1, $y1+15 );
            $this->Cell(20,4, "IVA", 0, 0, "C");
        }

        // remplit les cadres IVA / Totaux et la Nota
        // params  = array( "RemiseGlobale" => [0|1],
        //                      "remise_IVA"     => [1|2...],  // {la remise s'applique sur ce code IVA}
        //                      "DESCUENTO"         => value,     // {montant de la remise}
        //                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de IVA}
        //                  "FraisPort"     => [0|1],
        //                      "portTTC"        => value,     // montant des frais de ports TTC
        //                                                     // par defaut la IVA = 19.6 %
        //                      "portHT"         => value,     // montant des frais de ports HT
        //                      "portIVA"        => IVA_value, // valeur de la IVA a appliquer sur le montant HT
        //                  "AccompteExige" => [0|1],
        //                      "accompte"         => value    // montant de l'acompte (TTC)
        //                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
        //                  "Nota" => "texte"              // texte
        // tab_IVA = array( "1"       => 19.6,
        //                  "2"       => 5.5, ... );
        // invoice = array( "px_unit" => value,
        //                  "qte"     => qte,
        //                  "IVA"     => code_IVA );
        function addIVAs( $params, $invoice, $candec)
        {
            $this->SetFont('Arial','',8);
            $y = 263;

            $this->SetXY(15, $y);
                $this->Cell( 5,4, $invoice[0]["TOTAL"]);
                $this->SetXY(38, $y);
                $this->Cell( 19,4, $invoice[0]["SUB_TOTAL"]);
                $this->SetXY(63, $y);
                $this->Cell( 19,4, $invoice[0]["IVA"]);
                $this->SetXY(88, $y);
                $this->Cell( 10,4,$params[0]->IVA_PORCENTAJE);
                $this->SetXY(113, $y);
                $this->Cell(15,4, $params[0]->DESCUENTO);
                $re  = $this->w - 50;
                $rf  = $this->w - 29;
                $y1  = $this->h - 40;
                $this->SetFont( "Arial", "", 8);
                $this->SetXY( $re, $y1+5 );

                if($invoice[0]["MONEDA"] == "1"){

                    $this->SetXY( $re+3, $y1+5);                                                                                         
                    $this->Cell( 21,4, $invoice[0]["TOTAL"]);
                    $this->SetXY( $re+3, $y1+10 );
                    $this->Cell( 21,4, $invoice[0]["SUB_TOTAL"]);
                    $this->SetXY( $re+3, $y1+14.8);
                    $this->Cell( 21,4, $invoice[0]["IVA"]);

                }else{

                    $this->SetXY( $rf+3, $y1+5 );
                    $this->Cell( 17,4, $invoice[0]["TOTAL"]);
                    $this->SetXY( $rf+3, $y1+10 );
                    $this->Cell( 17,4, $invoice[0]["SUB_TOTAL"]);
                    $this->SetXY( $rf+3, $y1+14.8 );
                    $this->Cell( 17,4,  $invoice[0]["IVA"]);

                }
            // reset ($invoice);
            // $px = array();

            // foreach ($invoice as $k => $prod) {
            //     $IVA = $prod["IVA"];
            //     @ $px[$IVA] += $prod["cantidad"] * $prod["precio"];
            // }

            // $prix     = array();
            // $totalHT  = 0;
            // $totalTTC = 0;
            // $totalIVA = 0;
            
            // reset ($px);
            // natsort( $px );

            
            // foreach ($px as $code_IVA => $articleHT) {
            //     $IVA = $code_IVA;
                // $this->SetXY(17, $y);
                // $this->Cell( 19,4, sprintf("%0.2F", $params[0]->IVA_PORCENTAJE),'', '','R' );
            //     if ( $params["RemiseGlobale"]==1 )
            //     {
            //         if ( $params["remise_IVA"] == $code_IVA )
            //         {
                        // $this->SetXY( 37.5, $y );
            //             if ($params["DESCUENTO"] > 0 )
            //             {
            //                 if ( is_int( $params["DESCUENTO"] ) )
            //                     $l_remise = $param["DESCUENTO"];
            //                 else
            //                     $l_remise = sprintf ("%0.2F", $params["DESCUENTO"]);
                            // $this->Cell( 14.5,4, $invoice[0]["IVA"], '', '', 'R' );
            //                 $articleHT -= $params["DESCUENTO"];
            //             }
            //             else if ( $params["remise_percent"] > 0 )
            //             {
            //                 $rp = $params["remise_percent"];
            //                 if ( $rp > 1 )
            //                     $rp /= 100;
            //                 $rabais = $articleHT * $rp;
            //                 $articleHT -= $rabais;
            //                 if ( is_int($rabais) )
            //                     $l_remise = $rabais;
            //                 else
            //                     $l_remise = sprintf ("%0.2F", $rabais);
            //                 $this->Cell( 14.5,4, $l_remise, '', '', 'R' );
            //             }
            //             else
            //                 $this->Cell( 14.5,4, "ErrorRem", '', '', 'R' );
            //         }
            //     }
            //     $totalHT += $articleHT;
            //     $totalTTC += $articleHT * ( 1 + $IVA/100 );
            //     $tmp_IVA = $articleHT * $IVA/100;
            //     $a_IVA[ $code_IVA ] = $tmp_IVA;
            //     $totalIVA += $tmp_IVA;
                
            // $this->SetXY(114,271.4);
            //     $y+=4;
            // }

            // if ( $params["FraisPort"] == 1 )
            // {
            //     if ( $params["portTTC"] > 0 )
            //     {
            //         $pTTC = sprintf("%0.2F", $params["portTTC"]);
            //         $pHT  = sprintf("%0.2F", $pTTC / 1.196);
            //         $pIVA = sprintf("%0.2F", $pHT * 0.196);
                    // $this->SetFont('Arial','',6);
                    // $this->SetXY(55, 261 );
                    // $this->Cell( 4 ,4, "HT : ", '', '', '');
                    // $this->SetXY(92, 261 );
            //         $this->Cell( 9 ,4, $pHT, '', '', 'R');
                    // $this->SetXY(55, 265 );
                    // $this->Cell( 4 ,4, "IVA : ", '', '', '');
                    // $this->SetXY(62, 265 );
                    // $this->Cell( 9 ,4, $invoice[0]["IVA"], '', '', 'R');
                    // $this->SetXY(55, 269 );
                    // $this->Cell( 4 ,4, "TTC : ", '', '', '');
                    // $this->SetXY(62, 255 );
                    // $this->Cell( 9 ,4,  $invoice[0]["SUB_TOTAL"], '', '', 'R');
                    // $this->SetFont('Arial','',8);
            //         $totalHT += $pHT;
            //         $totalIVA += $pIVA;
            //         $totalTTC += $pTTC;
            //     }
            //     else if ( $params["portHT"] > 0 )
            //     {
            //         $pHT  = sprintf("%0.2F", $params["portHT"]);
            //         $pIVA = sprintf("%0.2F", $params["portIVA"] * $pHT / 100 );
            //         $pTTC = sprintf("%0.2F", $pHT + $pIVA);
            //         $this->SetFont('Arial','',6);
            //         $this->SetXY(85, 261 );
            //         $this->Cell( 6 ,4, "HT : ", '', '', '');
            //         $this->SetXY(92, 261 );
            //         $this->Cell( 9 ,4, $pHT, '', '', 'R');
            //         $this->SetXY(85, 265 );
            //         $this->Cell( 6 ,4, "IVA : ", '', '', '');
            //         $this->SetXY(92, 265 );
            //         $this->Cell( 9 ,4, $pIVA, '', '', 'R');
            //         $this->SetXY(85, 269 );
            //         $this->Cell( 6 ,4, "TTC : ", '', '', '');
            //         $this->SetXY(92, 269 );
                    // $this->Cell( 9 ,4, $pTTC, '', '', 'R');
            //         $this->SetFont('Arial','',8);
            //         $totalHT += $pHT;
            //         $totalIVA += $pIVA;
            //         $totalTTC += $pTTC;
            //     }
            // }

            
            // $this->Cell(15,4, sprintf("%0.2F", $invoice[0]["IVA"]), '', '', 'R' );

            // $params["totalHT"] = $totalHT;
            // $params["IVA"] = $totalIVA;
            // $accompteTTC=0;
            // if ( $params["AccompteExige"] == 1 )
            // {
            //     if ( $params["accompte"] > 0 )
            //     {
            //         $accompteTTC=sprintf ("%.2F", $params["accompte"]);
            //         if ( strlen ($params["Nota"]) == 0 )
            //             $this->addNota( "Accompte de $accompteTTC Euros exigé à la commande.");
            //         else
            //             $this->addNota( $params["Nota"] );
            //     }
            //     else if ( $params["accompte_percent"] > 0 )
            //     {
            //         $percent = $params["accompte_percent"];
            //         if ( $percent > 1 )
            //             $percent /= 100;
            //         $accompteTTC=sprintf("%.2F", $totalTTC * $percent);
            //         $percent100 = $percent * 100;
            //         if ( strlen ($params["Nota"]) == 0 )
            //             $this->addNota( "Accompte de $percent100 % (soit $accompteTTC Euros) exigé à la commande." );
            //         else
            //             $this->addNota( $params["Nota"] );
            //     }
            //     else
            //         $this->addNota( "Drôle d'acompte !!! " . $params["Nota"]);
            // }
            // else
            // {
            //     if ( strlen ($params["Nota"]) > 0 )
            //         $this->addNota( $params["Nota"] );
            // }
            
            // $this->SetXY( $rf, $y1+5 );
            // $this->Cell( 17,4, sprintf("%0.2F", $totalTTC * EURO_VAL), '', '', 'R');
            // $this->SetXY( $rf, $y1+10 );
            // $this->Cell( 17,4, sprintf("%0.2F", $accompteTTC * EURO_VAL), '', '', 'R');
            // $this->SetXY( $rf, $y1+14.8 );
            // $this->Cell( 17,4, sprintf("%0.2F", ($totalTTC - $accompteTTC) * EURO_VAL), '', '', 'R');
        }

        // add a watermark (temporary estimate, DUPLICATA...)
        // call this method first
        
        function temporaire( $texte )
        {
            $this->SetFont('Arial','B',50);
            $this->SetTextColor(203,203,203);
            $this->Rotate(45,55,190);
            $this->Text(55,190,utf8_decode($texte));
            $this->Rotate(0);
            $this->SetTextColor(0,0,0);
        }

}

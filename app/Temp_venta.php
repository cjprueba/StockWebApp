<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Temp_venta extends Model
{
    //
    protected $connection = 'retail';
	protected $table = 'temp_ventas';
    public $timestamps = false;

    public static function insertar_reporte($datos)
    {
     		$user = auth()->user();
     		Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$datos["sucursal"])->delete();
       
     	    $reporte=DB::connection('retail')->table('VENTASDET')
           
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('PRODUCTOS_AUX',function($join){
             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
            })
           
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
           

          
           ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
            ->leftjoin('VENTAS_CUPON','VENTAS_CUPON.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTASDET.COD_PROD AS COD_PROD,
            	VENTASDET.CODIGO,
             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
             MARCA.DESCRIPCION AS MARCA,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             VENTAS.ID AS ID,
             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
            DB::raw('IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE,0) AS CUPON_PORCENTAJE'),
            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))
	         ->Where('VENTASDET.ANULADO','<>',1)
	      
	         ->whereBetween('VENTASDET.FECALTAS', [$datos["inicio"], $datos["final"] ])
	         ->Where('VENTASDET.ID_SUCURSAL','=',$datos["sucursal"]) 
	         ->orderby('VENTASDET.COD_PROD');
	        if(isset($datos ["checkedMarca"])){
	        	if(!$datos ["checkedMarca"]){
	        	$reporte->whereIn('PRODUCTOS.MARCA', $datos["marcas"]);
	       		}
	        }
	        if(isset($datos["checkedCategoria"])){
	        	if(!$datos["checkedCategoria"]){
	        	$reporte->whereIn('PRODUCTOS.LINEA',$datos["linea"]);
	        	}
	        }
	        
	        if(isset($datos["checkedProveedor"])){
	        	if(!$datos["checkedProveedor"]){
	        		$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["proveedores"]);
	            }
	        }
	        if(isset($datos["tipos"])){
	        
	        	$reporte->whereIn('VENTAS.TIPO',$datos["tipos"]);
	            
	        }
	        if(isset($datos["gondolas"])){
	        	$reporte->leftjoin('gondola_tiene_productos','gondola_tiene_productos.FK_PRODUCTOS_AUX','=','PRODUCTOS_AUX.ID') 
	        	->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
           		->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
           		->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')->addSelect(
            	DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
            			 IFNULL(GONDOLAS.ID,0)AS GONDOLA,
            			 GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
             			 SECCIONES.DESCRIPCION AS SECCION,
             			 VENTAS.VENDEDOR AS VENDEDOR'))->where('VENTAS.TIPO','<>','CR');
           		if(!$datos["checkedGondola"]){
           			$reporte->whereIn('gondola_tiene_productos.ID_GONDOLA',$datos["gondolas"]);
           		}

           		if(!$datos["checkedSeccion"]){
           			$reporte->whereIn('GONDOLA_TIENE_SECCION.ID_SECCION',$datos["seccion"]);
           		}
	        }

	        $reporte=$reporte->get()->toArray();
	        	

			$precio=100;
			$descuento_precio=0;
			$precio_descontado=0;
			$costo=0;
			$descuento_cupon=0;
			$precio_descontado_cupon=0;
			$total_dev=0;
			$total_des=0;
			$descuento=0;
			$descuento_general=0;
			$precio_descontado_general=0;
			$precio_descontado_total=0;
			$descuento_real=0;
			$venta_in = array();
		     

		   foreach ($reporte as $key=>$value ) {


				            if($value->VENDIDO>0){
				            	// SI TIENE DESCUENTO GENERAL Y DESCUENTO POR CUPON
				              if ($value->PORCENTAJE_GENERAL>0 && $value->CUPON_PORCENTAJE>0 ) {
				              	 	//DESCUENTO GENERAL
						             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
						             	$total_des=$total_des+$descuento_precio;
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);
					             	//CUPON
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             	$total_des=$total_des+$descuento_precio;
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->CUPON_PORCENTAJE)/100),2);

						                $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
						                $precio_descontado=$precio-$descuento;
						                $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
						                $precio_descontado_general=$precio_descontado-$descuento_general;
						                $precio_descontado_total=$descuento+$descuento_general;
					                //CUPON
		                                $descuento_cupon=($precio_descontado_general*$value->CUPON_PORCENTAJE)/100;
		                                $precio_descontado_total=$precio_descontado_total+$descuento_cupon;
	                               		//---------------------------------------------------------

							              $descuento_real=($precio_descontado_total*100)/$precio;

							              $value->DESCUENTO_PORCENTAJE=$descuento_real;
				                
				             }else{
				             	    //SI TIENE SOLO CUPON
	                             if ($value->CUPON_PORCENTAJE>0 ) {
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             	$total_des=$total_des+$descuento_precio;
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

						               $descuento=($precio*$value->CUPON_PORCENTAJE)/100;
						               $precio_descontado=$precio-$descuento;
						               $descuento_general=($precio_descontado*$value->CUPON_PORCENTAJE)/100;
						               $precio_descontado_general=$precio_descontado-$descuento_general;
						               $precio_descontado_total=$descuento+$descuento_general;
						               $descuento_real=($precio_descontado_total*100)/$precio;
						               $value->DESCUENTO_PORCENTAJE=$descuento_real;
					                
					             }else{
							             	 //SI TIENE SOLO DESCUENTO GENERAL
						             if ($value->PORCENTAJE_GENERAL>0 ) {
							             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
							             	$total_des=$total_des+$descuento_precio;

							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

							               $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
							               $precio_descontado=$precio-$descuento;
							               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
							               $precio_descontado_general=$precio_descontado-$descuento_general;
							               $precio_descontado_total=$descuento+$descuento_general;
							               $descuento_real=($precio_descontado_total*100)/$precio;
							               $value->DESCUENTO_PORCENTAJE=$descuento_real;
						                
						             }
					             }
				             }
		
                            


				               
				             if($value->DESCUENTO_PORCENTAJE==0){
				             	$value->DESCUENTO_PORCENTAJE='0';
				             }

				            if($value->NOMBRE==NULL){
				                $value->NOMBRE='';
				             }
				            if($value->MARCA==NULL){
				                $value->MARCA='';
				             }

				                       
							$nestedData['COD_PROD'] = $value->COD_PROD;
						    $nestedData['VENDIDO'] =$value->VENDIDO;
				            $nestedData['CATEGORIA'] =$value->CATEGORIA;
				            $nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
				            $nestedData['NOMBRE']=$value->NOMBRE;
				            $nestedData['DESCUENTO_PORCENTAJE']=$value->PORCENTAJE_GENERAL;
				            $nestedData['DESCUENTO_PRODUCTO']=$value->DESCUENTO_PORCENTAJE;
				            $nestedData['PRECIO']= $value->PRECIO;
				            $nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
				            $nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
				            $nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
				            $nestedData['DESCUENTO']= $value->DESCUENTO;
				            $nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				            $nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
				            $nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				            $nestedData['PROVEEDOR']= $value->PROVEEDOR;
				            $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				            $nestedData['MARCA']= $value->MARCA;
				            $nestedData['LOTE']= $value->LOTE;
				            $nestedData['ID_SUCURSAL']=$datos["sucursal"];
				    	    $nestedData['USER_ID']=$user->id;
				    	    if(isset($datos["utilidad"])){
				             	$nestedData["UTILIDAD"]=$value->PRECIO-$value->COSTO_TOTAL;
				             }
				    	    if(isset($datos["gondolas"])){
				    			$nestedData['VENDEDOR'] =$value->VENDEDOR;
  								$nestedData['GONDOLA']= $value->GONDOLA;
				           		$nestedData['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
				            	$nestedData['SECCION']= $value->SECCION;
				            	$nestedData['SECCION_CODIGO']= $value->SECCION_CODIGO;
				            	$nestedData['CREDITO_COBRADO']= 0;
				    	    }
							    

				             

                             $venta_in[]=$nestedData;
				              	
							}
		              
		           		# code...

		        };
		      
		         log::error(["des"=>$total_des]);
		           foreach (array_chunk($venta_in,1000) as $t) {

                              	Temp_venta::insert($t);


                            }
                      $venta_in = array();
                      $venta_nc = array();
                    if($datos["inicio"]<='2020-10-26'){
			             $reporte=DB::connection('retail')->table('VENTASDET_DEVOLUCIONES')
			           
			            	 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET_DEVOLUCIONES.COD_PROD')
			            	 ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
			            	 ->leftjoin('PRODUCTOS_AUX',function($join){
			             	 $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET_DEVOLUCIONES.COD_PROD')
			              	 	  ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET_DEVOLUCIONES.ID_SUCURSAL');
			         		 })
			           
			                 ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
			                 ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'VENTASDET_DEVOLUCIONES.FK_VENTASDET')
			                 ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
			                 ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
			                 ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
			                 ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
			                 ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
			                  ->leftjoin('VENTAS',function($join){
			                  $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
			                  		->on('VENTAS.CAJA','=','VENTASDET.CAJA')
			               			->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
			         			})
			           			/* ->leftjoin('VENTASDET_DEVOLUCIONES','VENTASDET_DEVOLUCIONES.FK_VENTASDET','=','VENTASDET.ID')*/

			            		 ->select(
			            		 DB::raw('VENTASDET_DEVOLUCIONES.COD_PROD AS COD_PROD,
			            			VENTASDET.CODIGO,
			            		 VENTASDET_DEVOLUCIONES.CANTIDAD AS VENDIDO,
			            		 MARCA.DESCRIPCION AS MARCA,
			            		 LOTES.LOTE AS LOTE,
			            		 LOTES.COSTO AS COSTO_UNIT,
			            		 (VENTASDET_DEVOLUCIONES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
			            		 LINEAS.DESCRIPCION AS CATEGORIA,
			            		 SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
			            		 SUBLINEA_DET.DESCRIPCION AS NOMBRE,
			            		 PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
			            		 PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
			            		 (VENTASDET_DEVOLUCIONES.PRECIO_UNIT*VENTASDET_DEVOLUCIONES.CANTIDAD) AS PRECIO,
			            		 VENTASDET_DEVOLUCIONES.PRECIO_UNIT AS PRECIO_UNIT'),
			            		 DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
			            		 DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
			            		 DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

			        			 ->Where('VENTASDET.ANULADO','<>',1)
			        			 /*->where('VENTAS.TIPO','<>','CR')*/
			      					 //->Where('VENTASdet.FECALTAS','like',$dia.'%')
			        			 ->whereBetween('VENTASDET_DEVOLUCIONES.FECALTAS', [$datos["inicio"], $datos["final"] ])
			        			 ->Where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=',$datos["sucursal"]) 
			    
			        			 ->orderby('VENTASDET.COD_PROD');
							        if(isset($datos ["checkedMarca"])){
							        	if(!$datos ["checkedMarca"]){
							        	$reporte->whereIn('PRODUCTOS.MARCA', $datos["marcas"]);
							       		}
							        }
							        if(isset($datos["checkedCategoria"])){
							        	if(!$datos["checkedCategoria"]){
							        	$reporte->whereIn('PRODUCTOS.LINEA',$datos["linea"]);
							        	}
							        }
							        if(isset($datos["checkedProveedor"])){
							        	if(!$datos["checkedProveedor"]){
							        	$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["proveedores"]);
							            }
							        }
							        if(isset($datos["tipos"])){
							        	
							        	$reporte->whereIn('VENTAS.TIPO',$datos["tipos"]);
							            
							        }
							        if(isset($datos["gondolas"])){
							        	$reporte->leftjoin('gondola_tiene_productos','gondola_tiene_productos.FK_PRODUCTOS_AUX','=','PRODUCTOS_AUX.ID') 
							        	->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
						           		->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
						           		->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')->addSelect(
						            	DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
						            			 IFNULL(GONDOLAS.ID,0)AS GONDOLA,
						            			 GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
						             			 SECCIONES.DESCRIPCION AS SECCION,
						             			 VENTAS.VENDEDOR AS VENDEDOR'))->where('VENTAS.TIPO','<>','CR');
						           		if(!$datos["checkedGondola"]){
						           			$reporte->whereIn('gondola_tiene_productos.ID_GONDOLA',$datos["gondolas"]);
						           		}

						           		if(!$datos["checkedSeccion"]){
						           			$reporte->whereIn('GONDOLA_TIENE_SECCION.ID_SECCION',$datos["seccion"]);
						           		}
							        }

				        
				         $reporte=$reporte->get()->toArray();

			         foreach ($reporte as $key => $value) {	
			         	         	if($value->NOMBRE==NULL){
							                $value->NOMBRE='';
							             }

							              if($value->MARCA==NULL){

							                $value->MARCA=' ';
							             }
			         	  		 $nestedDataS['COD_PROD'] = $value->COD_PROD;
								 $nestedDataS['VENDIDO'] =-$value->VENDIDO;
							     $nestedDataS['CATEGORIA'] =$value->CATEGORIA;
			             		 $nestedDataS['SUBCATEGORIA']=$value->SUBCATEGORIA;
							     $nestedDataS['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
							     $nestedDataS['DESCUENTO_PORCENTAJE']=0;
							     $nestedDataS['DESCUENTO_PRODUCTO']=0;
							     $nestedDataS['PRECIO']= $value->PRECIO;
							     $nestedDataS['PRECIO_UNIT']= $value->PRECIO_UNIT;
							     $nestedDataS['COSTO_UNIT']= $value->COSTO_UNIT*-1;
							     $nestedDataS['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
							     $nestedDataS['DESCUENTO']= 0;
							     $nestedDataS['MARCAS_CODIGO']= $value->MARCA_CODIGO;
							     $nestedDataS['LINEA_CODIGO']=$value->LINEA_CODIGO;
							     $nestedDataS['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
							     $nestedDataS['PROVEEDOR']= $value->PROVEEDOR;
							     $nestedDataS['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
							     $nestedDataS['MARCA']= $value->MARCA;
							     $nestedDataS['LOTE']= $value->LOTE;
							     $nestedDataS['ID_SUCURSAL']=$datos["sucursal"];
							     $nestedDataS['USER_ID']=$user->id;
							     if(isset($datos["utilidad"])){
				             		$nestedData["UTILIDAD"]=($value->PRECIO)-($value->COSTO_TOTAL);
				             		}
							     if(isset($datos["gondolas"])){
							    	 $nestedDataS['VENDEDOR'] =$value->VENDEDOR;
			  						 $nestedDataS['GONDOLA']= $value->GONDOLA;
							         $nestedDataS['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
							         $nestedDataS['SECCION']= $value->SECCION;
							         $nestedDataS['SECCION_CODIGO']= $value->SECCION_CODIGO;
							         $nestedDataS['CREDITO_COBRADO']= 0;
							      }
							     $venta_in[]=$nestedDataS;
			         		# code...
			         }
                      foreach (array_chunk($venta_in,1000) as $t) {

                              	 Temp_venta::insert($t);
                            } 
                }

	   
                         $reporte=DB::connection('retail')->table('NOTA_CREDITO_DET')
           
					            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
					            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
					            ->leftjoin('PRODUCTOS_AUX',function($join){
					             $join->on('PRODUCTOS_AUX.CODIGO','=','NOTA_CREDITO_DET.CODIGO_PROD')
					               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
					         	})
					           
					            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
					             ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'NOTA_CREDITO_DET.FK_VENTASDET')
					            ->leftjoin('nota_credito_tiene_lote', 'nota_credito_tiene_lote.FK_VENTA_DET', '=', 'VENTASDET.ID')
					            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
					            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
					            ->leftjoin('LOTES', 'LOTES.ID', '=', 'nota_credito_tiene_lote.ID_LOTE')
					            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
					            ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
					                         ->leftjoin('VENTAS',function($join){
					             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
					             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
					               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
					         })
       

						            ->select(
						            DB::raw('NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
						            
						             nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
						             MARCA.DESCRIPCION AS MARCA,
						             LOTES.LOTE AS LOTE,
						             LOTES.COSTO AS COSTO_UNIT,
						             (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
						             LINEAS.DESCRIPCION AS CATEGORIA,
						             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
						             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
						             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
						             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
						            (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
						             NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
						            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
						            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
						            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

						        ->Where('VENTASDET.ANULADO','<>',1)
						        ->Where('VENTAS.TIPO','<>','CR')
						        ->Where('NOTA_CREDITO.PROCESADO','=',1)
						       //->Where('VENTASdet.FECALTAS','like',$dia.'%')
						        ->whereBetween('NOTA_CREDITO.FECMODIF', [$datos["inicio"], $datos["final"] ])
						        ->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',$datos["sucursal"]) 
						        ->orderby('VENTASDET.COD_PROD');
							          if(isset($datos ["checkedMarca"])){
							        	if(!$datos ["checkedMarca"]){
							        	$reporte->whereIn('PRODUCTOS.MARCA', $datos["marcas"]);
							       		}
							        }
							        if(isset($datos["checkedCategoria"])){
							        	if(!$datos["checkedCategoria"]){
							        	$reporte->whereIn('PRODUCTOS.LINEA',$datos["linea"]);
							        	}
							        }
							        if(isset($datos["checkedProveedor"])){
							        	if(!$datos["checkedProveedor"]){
							        	$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["proveedores"]);
							            }
							        }
							        if(isset($datos["tipos"])){
							        	
							        	$reporte->whereIn('VENTAS.TIPO',$datos["tipos"]);
							            
							        }
							        if(isset($datos["gondolas"])){
							        	$reporte->leftjoin('gondola_tiene_productos','gondola_tiene_productos.FK_PRODUCTOS_AUX','=','PRODUCTOS_AUX.ID') 
							        	->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
						           		->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
						           		->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')->addSelect(
						            	DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
						            			 IFNULL(GONDOLAS.ID,0)AS GONDOLA,
						            			 GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
						             			 SECCIONES.DESCRIPCION AS SECCION,
						             			 VENTAS.VENDEDOR AS VENDEDOR'))->where('VENTAS.TIPO','<>','CR');
						           		if(!$datos["checkedGondola"]){
						           			$reporte->whereIn('gondola_tiene_productos.ID_GONDOLA',$datos["gondolas"]);
						           		}

						           		if(!$datos["checkedSeccion"]){
						           			$reporte->whereIn('GONDOLA_TIENE_SECCION.ID_SECCION',$datos["seccion"]);
						           		}
							        }
					     $reporte=$reporte->get()->toArray();

          foreach ($reporte as $key => $value) {
         	          	if($value->NOMBRE==NULL){
				                $value->NOMBRE='';
				             }

				        if($value->MARCA==NULL){
				                $value->MARCA=' ';
				             }

	         	  		$nestedDataNC['COD_PROD'] = $value->COD_PROD;
						$nestedDataNC['VENDIDO'] =-$value->VENDIDO;
					    $nestedDataNC['CATEGORIA'] =$value->CATEGORIA;
	             		$nestedDataNC['SUBCATEGORIA']=$value->SUBCATEGORIA;
					    $nestedDataNC['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
					    $nestedDataNC['DESCUENTO_PORCENTAJE']=0;
					    $nestedDataNC['DESCUENTO_PRODUCTO']=0;
					    $nestedDataNC['PRECIO']= $value->PRECIO*-1;
					    $nestedDataNC['PRECIO_UNIT']= $value->PRECIO_UNIT*-1;
					    $nestedDataNC['COSTO_UNIT']= $value->COSTO_UNIT*-1;
					    $nestedDataNC['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
					    $nestedDataNC['DESCUENTO']= 0;
					    $nestedDataNC['MARCAS_CODIGO']= $value->MARCA_CODIGO;
					    $nestedDataNC['LINEA_CODIGO']=$value->LINEA_CODIGO;
					    $nestedDataNC['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
					    $nestedDataNC['PROVEEDOR']= $value->PROVEEDOR;
					    $nestedDataNC['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
					    $nestedDataNC['MARCA']= $value->MARCA;
					    $nestedDataNC['LOTE']= $value->LOTE;
					    $nestedDataNC['ID_SUCURSAL']=$datos["sucursal"];
					    $nestedDataNC['USER_ID']=$user->id;
					    if(isset($datos["utilidad"])){
				             $nestedData["UTILIDAD"]=($value->PRECIO*-1)-($value->COSTO_TOTAL);
				          }
					     if(isset($datos["gondolas"])){
					    	 $nestedDataNC['VENDEDOR'] =$value->VENDEDOR;
	  						 $nestedDataNC['GONDOLA']= $value->GONDOLA;
					         $nestedDataNC['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
					         $nestedDataNC['SECCION']= $value->SECCION;
					         $nestedDataNC['SECCION_CODIGO']= $value->SECCION_CODIGO;
					         $nestedDataNC['CREDITO_COBRADO']= 0;
					      }
					     $venta_nc[]=$nestedDataNC;
         	
         }
          foreach (array_chunk($venta_nc,1000) as $t) {
                     Temp_venta::insert($t);
           } 

           	     		if(isset($datos["gondolas"])){
	            		Temp_venta::nota_credito_venta_credito($datos);
	            		if($datos["mayoristaCredito"]){
	            			Temp_venta::venta_credito_cobrados($datos);
	            		}
	            		
            		}

    }
    public static function insertar_transferencia_reporte($datos)
    {
     	
				     	$user = auth()->user();
				      Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$datos["sucursal"])->delete();

					   $reporte=DB::connection('retail')->table('VENTASDET')
				           
				            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
				            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
				            ->leftjoin('PRODUCTOS_AUX',function($join){
				             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
				               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
				         })
				           
				            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
				            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
				            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
				            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
				            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
				            ->leftjoin('lote_tiene_transferenciadet','lote_tiene_transferenciadet.ID_LOTE','=','LOTES.ID')
							->leftjoin('transferencias_det','transferencias_det.ID','=','lote_tiene_transferenciadet.ID_TRANSFERENCIA_DET')
							 ->leftjoin('TRANSFERENCIAS',function($join){
				             $join->on('TRANSFERENCIAS.CODIGO','=','transferencias_det.CODIGO')
				             ->on('TRANSFERENCIAS.SUCURSAL_DESTINO','=','VENTASDET.ID_SUCURSAL')
				             ->on('TRANSFERENCIAS.SUCURSAL_ORIGEN','=','transferencias_det.ID_SUCURSAL');
				         })
							  ->leftjoin('SUCURSALES','SUCURSALES.CODIGO','TRANSFERENCIAS.SUCURSAL_ORIGEN')

				            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
				            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
				           /* ->leftjoin('VENTAS_CUPON','VENTAS_CUPON.FK_VENTA','=','VENTAS.ID')*/
				           /* ->leftjoin('VENTASDET_DEVOLUCIONES','VENTASDET_DEVOLUCIONES.FK_VENTASDET','=','VENTASDET.ID')*/
				          
				           ->leftjoin('VENTAS',function($join){
				             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
				             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
				               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
				         })
				             ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
				            ->select(
				            DB::raw('VENTASDET.COD_PROD AS COD_PROD,
				            	VENTASDET.CODIGO,
				             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
				             MARCA.DESCRIPCION AS MARCA,
				             LOTES.LOTE AS LOTE,
				             LOTES.COSTO AS COSTO_UNIT,
				             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
				             LINEAS.DESCRIPCION AS CATEGORIA,
				             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
				             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
				             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
				              SUCURSALES.CODIGO AS SUCURSAL_ORIGEN,
				             SUCURSALES.DESCRIPCION AS SUCURSAL_NOMBRE,
				             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
				             VENTAS.ID AS ID,
				             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
				             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
				            DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
				            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
				            /*  DB::raw('IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE,0) AS CUPON_PORCENTAJE'),*/
				            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
				            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
				            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
				            /*DB::raw('IFNULL(VENTASDET_DEVOLUCIONES.CANTIDAD,0) AS CANTIDAD_DEVUELTA'),
				            DB::raw('IFNULL(VENTASDET_DEVOLUCIONES.PRECIO,0) AS PRECIO_DEVUELTO'),
				            DB::raw('IFNULL(VENTASDET_DEVOLUCIONES.PRECIO_UNIT,0) AS PRECIO_UNIT_DEVUELTO'),*/
				            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))

					        ->Where('VENTASDET.ANULADO','=',0)
					       //->Where('VENTASdet.FECALTAS','like',$dia.'%')
					        ->whereBetween('VENTASDET.FECALTAS', [$datos["inicio"], $datos["final"] ])
					        ->Where('VENTASDET.ID_SUCURSAL','=',$datos["sucursal"]) 
					         ->WHERE('TRANSFERENCIAS.CONSIGNACION','=',1)
					        ->orderby('VENTASDET.COD_PROD');
					        if(!$datos ["checkedProveedor"]){
					        	$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR', $datos["proveedores"]);
					        }
					        if(!$datos["checkedSucursal"]){
					        	$reporte->whereIn('TRANSFERENCIAS.SUCURSAL_ORIGEN',$datos["sucursales"]);
					        }

					    $reporte=$reporte->get()->toArray();
						         $precio=100;
						        $descuento_precio=0;
						        $precio_descontado=0;
						        $costo=0;
						        $total_dev=0;
						        $total_des=0;
						        $descuento=0;
						        $descuento_general=0;
						        $precio_descontado_general=0;
						      	$precio_descontado_total=0;
						      	$descuento_real=0;
						     $venta_in = array();
						     

							foreach ($reporte as $key=>$value ) {

							           /* if($value->CANTIDAD_DEVUELTA>0){
				                                $total_dev=$total_dev+$value->PRECIO_DEVUELTO;
								                $value->VENDIDO=$value->VENDIDO-$value->CANTIDAD_DEVUELTA;
								                $value->PRECIO=$value->PRECIO-$value->PRECIO_DEVUELTO;
								                $value->PRECIO_UNIT=$value->PRECIO_UNIT-$value->PRECIO_UNIT_DEVUELTO;
								                $costo=$value->COSTO_UNIT*$value->CANTIDAD_DEVUELTA;
								                $value->COSTO_TOTAL=$value->COSTO_TOTAL-$costo;

								            }*/
								            if($value->VENDIDO>0){


								             if ($value->PORCENTAJE_GENERAL>0 ) {
								             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
								             	$total_des=$total_des+$descuento_precio;
								             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
								             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
								             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

								               $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
								               $precio_descontado=$precio-$descuento;
								               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
								               $precio_descontado_general=$precio_descontado-$descuento_general;
								               $precio_descontado_total=$descuento+$descuento_general;
								               $descuento_real=($precio_descontado_total*100)/$precio;
								               $value->DESCUENTO_PORCENTAJE=$descuento_real;
								                
								             }
								               
								             if($value->DESCUENTO_PORCENTAJE==0){
								             	$value->DESCUENTO_PORCENTAJE='0';
								             }

								            if($value->NOMBRE==NULL){
								                $value->NOMBRE='';
								             }
								              if($value->MARCA==NULL){
								                $value->MARCA='';
								             }

								                       
											        $nestedData['COD_PROD'] = $value->COD_PROD;
													$nestedData['VENDIDO'] =$value->VENDIDO;
								              		$nestedData['CATEGORIA'] =$value->CATEGORIA;
								              		$nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
								              		$nestedData['NOMBRE']=$value->NOMBRE;
								              		$nestedData['DESCUENTO_PORCENTAJE']=$value->PORCENTAJE_GENERAL;
								              		$nestedData['DESCUENTO_PRODUCTO']=$value->DESCUENTO_PORCENTAJE;
								              		$nestedData['PRECIO']= $value->PRECIO;
								              		$nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
								              		$nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
								              		$nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
								              		$nestedData['DESCUENTO']= $value->DESCUENTO;
								              		$nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
								              		$nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
								              		$nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
								              		$nestedData['PROVEEDOR']= $value->PROVEEDOR;
								              		$nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
								              		$nestedData['SUCURSAL_ORIGEN']= $value->SUCURSAL_ORIGEN;
								              		$nestedData['SUCURSAL_NOMBRE']= $value->SUCURSAL_NOMBRE;
								              		$nestedData['MARCA']= $value->MARCA;
								              		$nestedData['LOTE']= $value->LOTE;
								              		$nestedData['ID_SUCURSAL']=$datos["sucursal"];
								    			    $nestedData['USER_ID']=$user->id;
											    

								             

				                             $venta_in[]=$nestedData;
								              	
											}
						              
						           # code...

						        };
						       // log::error(["dev"=>$total_dev]);
						      /*   log::error(["des"=>$total_des]);*/
						           foreach (array_chunk($venta_in,1000) as $t) {

				                              	Temp_venta::insert($t);


				                            }
				                      $venta_in = array();
				                       $venta_nc = array();


					   $reporte=DB::connection('retail')->table('VENTASDET')
				           ->leftjoin('VENTASDET_DEVOLUCIONES','VENTASDET_DEVOLUCIONES.FK_VENTASDET','=','VENTASDET.ID')
				            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET_DEVOLUCIONES.COD_PROD')
				            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
				            ->leftjoin('PRODUCTOS_AUX',function($join){
				             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET_DEVOLUCIONES.COD_PROD')
				               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET_DEVOLUCIONES.ID_SUCURSAL');
				         })
				           
				            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
				            
				            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
				            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
				            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
				            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
				            ->leftjoin('lote_tiene_transferenciadet','lote_tiene_transferenciadet.ID_LOTE','=','LOTES.ID')
							->leftjoin('transferencias_det','transferencias_det.ID','=','lote_tiene_transferenciadet.ID_TRANSFERENCIA_DET')
							 ->leftjoin('TRANSFERENCIAS',function($join){
				             $join->on('TRANSFERENCIAS.CODIGO','=','transferencias_det.CODIGO')
				             ->on('TRANSFERENCIAS.SUCURSAL_DESTINO','=','VENTASDET.ID_SUCURSAL')
				             ->on('TRANSFERENCIAS.SUCURSAL_ORIGEN','=','transferencias_det.ID_SUCURSAL');
				         })
							 ->leftjoin('SUCURSALES','SUCURSALES.CODIGO','TRANSFERENCIAS.SUCURSAL_ORIGEN')
				            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
				           /* ->leftjoin('VENTASDET_DEVOLUCIONES','VENTASDET_DEVOLUCIONES.FK_VENTASDET','=','VENTASDET.ID')*/

				            ->select(
				            DB::raw('VENTASDET_DEVOLUCIONES.COD_PROD AS COD_PROD,
				            	VENTASDET.CODIGO,
				            	VENTASDET.ID,
				            	lotes.id as id_LOTE,
				             VENTASDET_DEVOLUCIONES.CANTIDAD AS VENDIDO,
				             MARCA.DESCRIPCION AS MARCA,
				             LOTES.LOTE AS LOTE,
				             LOTES.COSTO AS COSTO_UNIT,
				             (VENTASDET_DEVOLUCIONES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
				             LINEAS.DESCRIPCION AS CATEGORIA,
				             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
				             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
				             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
				             SUCURSALES.CODIGO AS SUCURSAL_ORIGEN,
				             SUCURSALES.DESCRIPCION AS SUCURSAL_NOMBRE,
				             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
				             (VENTASDET_DEVOLUCIONES.PRECIO_UNIT*VENTASDET_DEVOLUCIONES.CANTIDAD) AS PRECIO,
				             VENTASDET_DEVOLUCIONES.PRECIO_UNIT AS PRECIO_UNIT'),
				            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
				            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
				            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

				        ->Where('VENTASDET.ANULADO','=',0)
				        ->WHERE('TRANSFERENCIAS.CONSIGNACION','=',1)
				       //->Where('VENTASdet.FECALTAS','like',$dia.'%')
				        ->whereBetween('VENTASDET_DEVOLUCIONES.FECALTAS', [$datos["inicio"], $datos["final"] ])
				        ->Where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=',$datos["sucursal"]) 
				        ->orderby('VENTASDET.COD_PROD');

					        if(!$datos["checkedProveedor"]){
					        	$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR', $datos["proveedores"]);
					        }
					        if(!$datos["checkedSucursal"]){ 
					        	$reporte->whereIn('TRANSFERENCIAS.SUCURSAL_ORIGEN',$datos["sucursales"]);
					        }
					        
					        $reporte=$reporte->get()->toArray();
				 
				         foreach ($reporte as $key => $value) {
				         	         	if($value->NOMBRE==NULL){
								                $value->NOMBRE='';
								             }

								              if($value->MARCA==NULL){

								                $value->MARCA=' ';
								             }
				                
				         	  		$nestedDataS['COD_PROD'] = $value->COD_PROD;
									$nestedDataS['VENDIDO'] =-$value->VENDIDO;
								    $nestedDataS['CATEGORIA'] =$value->CATEGORIA;
				             		$nestedDataS['SUBCATEGORIA']=$value->SUBCATEGORIA;
								    $nestedDataS['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
								    $nestedDataS['DESCUENTO_PORCENTAJE']=0;
								    $nestedDataS['DESCUENTO_PRODUCTO']=0;
								    $nestedDataS['PRECIO']= $value->PRECIO;
								    $nestedDataS['PRECIO_UNIT']= $value->PRECIO_UNIT;
								    $nestedDataS['COSTO_UNIT']= $value->COSTO_UNIT*-1;
								    $nestedDataS['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
								    $nestedDataS['DESCUENTO']= 0;
								    $nestedDataS['MARCAS_CODIGO']= $value->MARCA_CODIGO;
								    $nestedDataS['LINEA_CODIGO']=$value->LINEA_CODIGO;
								    $nestedDataS['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
								    $nestedDataS['PROVEEDOR']= $value->PROVEEDOR;
								    $nestedDataS['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
								    $nestedDataS['SUCURSAL_ORIGEN']= $value->SUCURSAL_ORIGEN;
								    $nestedDataS['SUCURSAL_NOMBRE']= $value->SUCURSAL_NOMBRE;
								    $nestedDataS['MARCA']= $value->MARCA;
								    $nestedDataS['LOTE']= $value->LOTE;
								    $nestedDataS['ID_SUCURSAL']=$datos["sucursal"];
								    $nestedDataS['USER_ID']=$user->id;
								     $venta_in[]=$nestedDataS;
				         	# code...

				         }

				                       foreach (array_chunk($venta_in,1000) as $t) {

				                              	Temp_venta::insert($t);


				                            } 
				                            $reporte=DB::connection('retail')->table('NOTA_CREDITO_DET')
				           
				            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
				            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
				            ->leftjoin('PRODUCTOS_AUX',function($join){
				             $join->on('PRODUCTOS_AUX.CODIGO','=','NOTA_CREDITO_DET.CODIGO_PROD')
				               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
				         })
				           
				            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
				             ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'NOTA_CREDITO_DET.FK_VENTASDET')
				            ->leftjoin('nota_credito_tiene_lote', 'nota_credito_tiene_lote.FK_VENTA_DET', '=', 'VENTASDET.ID')
				            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
				            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
				            ->leftjoin('LOTES', 'LOTES.ID', '=', 'nota_credito_tiene_lote.ID_LOTE')
				            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
				            ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
				                ->leftjoin('lote_tiene_transferenciadet','lote_tiene_transferenciadet.ID_LOTE','=','LOTES.ID')
							->leftjoin('transferencias_det','transferencias_det.ID','=','lote_tiene_transferenciadet.ID_TRANSFERENCIA_DET')
							 ->leftjoin('TRANSFERENCIAS',function($join){
				             $join->on('TRANSFERENCIAS.CODIGO','=','transferencias_det.CODIGO')
				             ->on('TRANSFERENCIAS.SUCURSAL_DESTINO','=','VENTASDET.ID_SUCURSAL')
				             ->on('TRANSFERENCIAS.SUCURSAL_ORIGEN','=','transferencias_det.ID_SUCURSAL');
				         })
							 ->leftjoin('SUCURSALES','SUCURSALES.CODIGO','TRANSFERENCIAS.SUCURSAL_ORIGEN')
				            
				           /* ->leftjoin('NOTA_CREDITO_DET','NOTA_CREDITO_DET.FK_VENTASDET','=','VENTASDET.ID')*/

				            ->select(
				            DB::raw('NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
				            
				             nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
				             MARCA.DESCRIPCION AS MARCA,
				             LOTES.LOTE AS LOTE,
				             LOTES.COSTO AS COSTO_UNIT,
				             (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
				             LINEAS.DESCRIPCION AS CATEGORIA,
				             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
				             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
				             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
				             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
				               SUCURSALES.CODIGO AS SUCURSAL_ORIGEN,
				             SUCURSALES.DESCRIPCION AS SUCURSAL_NOMBRE,
				            (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
				             NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
				            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
				            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
				            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

				        ->Where('VENTASDET.ANULADO','<>',1)
				            ->WHERE('TRANSFERENCIAS.CONSIGNACION','=',1)
				        ->Where('NOTA_CREDITO.PROCESADO','=',1)
				       //->Where('VENTASdet.FECALTAS','like',$dia.'%')
				        ->whereBetween('NOTA_CREDITO_DET.FECALTAS', [$datos["inicio"], $datos["final"] ])
				        ->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',$datos["sucursal"]) 
				        ->orderby('VENTASDET.COD_PROD');
					        if(!$datos["checkedProveedor"]){
					        	$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR', $datos["proveedores"]);
					        }
					        if(!$datos["checkedSucursal"]){ 
					        	$reporte->whereIn('TRANSFERENCIAS.SUCURSAL_ORIGEN',$datos["sucursales"]);
					        }
					        
					        $reporte=$reporte->get()->toArray();
				         /*	     Log::error(['VENTA NOTA CREDITO REVISAR' => $reporte]);  */ 
				         foreach ($reporte as $key => $value) {
				         	         	if($value->NOMBRE==NULL){
								                $value->NOMBRE='';
								             }

								              if($value->MARCA==NULL){

								                $value->MARCA=' ';
								             }
				         	  		$nestedDataNC['COD_PROD'] = $value->COD_PROD;
									$nestedDataNC['VENDIDO'] =-$value->VENDIDO;
								    $nestedDataNC['CATEGORIA'] =$value->CATEGORIA;
				             		$nestedDataNC['SUBCATEGORIA']=$value->SUBCATEGORIA;
								    $nestedDataNC['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
								    $nestedDataNC['DESCUENTO_PORCENTAJE']=0;
								    $nestedDataNC['DESCUENTO_PRODUCTO']=0;
								    $nestedDataNC['PRECIO']= $value->PRECIO*-1;
								    $nestedDataNC['PRECIO_UNIT']= $value->PRECIO_UNIT*-1;
								    $nestedDataNC['COSTO_UNIT']= $value->COSTO_UNIT*-1;
								    $nestedDataNC['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
								    $nestedDataNC['DESCUENTO']= 0;
								    $nestedDataNC['MARCAS_CODIGO']= $value->MARCA_CODIGO;
								    $nestedDataNC['LINEA_CODIGO']=$value->LINEA_CODIGO;
								    $nestedDataNC['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
								    $nestedDataNC['PROVEEDOR']= $value->PROVEEDOR;
								    $nestedDataNC['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
								      $nestedDataNC['SUCURSAL_ORIGEN']= $value->SUCURSAL_ORIGEN;
								    $nestedDataNC['SUCURSAL_NOMBRE']= $value->SUCURSAL_NOMBRE;
								    $nestedDataNC['MARCA']= $value->MARCA;
								    $nestedDataNC['LOTE']= $value->LOTE;
								    $nestedDataNC['ID_SUCURSAL']=$datos["sucursal"];
								    $nestedDataNC['USER_ID']=$user->id;
								     $venta_nc[]=$nestedDataNC;
				         	# code...
				         }
				                       foreach (array_chunk($venta_nc,1000) as $t) {

				                              	Temp_venta::insert($t);


				                            } 

    }
    public static function venta_credito_cobrados($datos)
    {
	     		 $user = auth()->user();
	 
			     $reporte=DB::connection('retail')->table('VENTAS_CREDITO')
				    ->leftjoin('VENTAS','VENTAS.ID','=','VENTAS_CREDITO.FK_VENTA')

	     	        ->leftjoin('VENTASDET',function($join){
					 $join->on('VENTASDET.CODIGO','=','VENTAS.CODIGO')
						 ->on('VENTASDET.CAJA','=','VENTAS.CAJA')
						 ->on('VENTASDET.ID_SUCURSAL','=','VENTAS.ID_SUCURSAL');
					 })
	           
		            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
		            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
		            ->leftjoin('PRODUCTOS_AUX',function($join){
		             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
		               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
		            })
		           
		            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
		            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
		            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
		            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
		            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
		            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
		            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
	           

	            	->leftjoin('VENTAS_CUPON','VENTAS_CUPON.FK_VENTA','=','VENTAS.ID')
	                ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
		            ->select(
		            DB::raw('VENTASDET.COD_PROD AS COD_PROD,
		            	VENTASDET.CODIGO,
		             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
		             MARCA.DESCRIPCION AS MARCA,
		             LOTES.LOTE AS LOTE,
		             LOTES.COSTO AS COSTO_UNIT,
		             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
		             LINEAS.DESCRIPCION AS CATEGORIA,
		             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
		             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
		             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
		             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
		             VENTAS.ID AS ID,
		             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
		             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
		            DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
		            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
		            DB::raw('IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE,0) AS CUPON_PORCENTAJE'),
		            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
		            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
		            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
		            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))
		            ->whereBetween('VENTAS_CREDITO.FECHA_CANCELACION', [$datos["inicio"], $datos["final"] ])
					->where('VENTAS_CREDITO.SALDO','=',0)
					->Where('VENTAS.ID_SUCURSAL','=',$datos["sucursal"]) 
			        ->Where('VENTASDET.ANULADO','<>',1)
			        ->orderby('VENTASDET.COD_PROD')->get()->toArray();
		           /*if(isset($datos ["checkedMarca"])){
			        	if(!$datos ["checkedMarca"]){
			        	$reporte->whereIn('PRODUCTOS.MARCA', $datos["marcas"]);
			       		}
			        }
			        if(isset($datos["checkedCategoria"])){
			        	if(!$datos["checkedCategoria"]){
			        	$reporte->whereIn('PRODUCTOS.LINEA',$datos["linea"]);
			        	}
			        }
			        
			        if(isset($datos["checkedProveedor"])){
			        	if(!$datos["checkedProveedor"]){
			        		$reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["proveedores"]);
			            }
			        }
			        if(isset($datos["tipos"])){
			        
			        	$reporte->whereIn('VENTAS.TIPO',$datos["tipos"]);
			            
			        }
			        if(isset($datos["gondolas"])){
			        	$reporte->leftjoin('gondola_tiene_productos','gondola_tiene_productos.FK_PRODUCTOS_AUX','=','PRODUCTOS_AUX.ID') 
			        	->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
		           		->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
		           		->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')->addSelect(
		            	DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
		            			 IFNULL(GONDOLAS.ID,0)AS GONDOLA,
		            			 GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
		             			 SECCIONES.DESCRIPCION AS SECCION,
		             			 VENTAS.VENDEDOR AS VENDEDOR'))->where('VENTAS.TIPO','<>','CR');
		           		if(!$datos["checkedGondola"]){
		           			$reporte->whereIn('gondola_tiene_productos.ID_GONDOLA',$datos["gondolas"]);
		           		}

		           		if(!$datos["checkedSeccion"]){
		           			$reporte->whereIn('GONDOLA_TIENE_SECCION.ID_SECCION',$datos["seccion"]);
		           		}
			        }*/
			        $precio=100;
					$descuento_precio=0;
					$precio_descontado=0;
					$costo=0;
					$descuento_cupon=0;
					$precio_descontado_cupon=0;
					$total_dev=0;
					$total_des=0;
					$descuento=0;
					$descuento_general=0;
					$precio_descontado_general=0;
					$precio_descontado_total=0;
					$descuento_real=0;
					$venta_in = array();
			     

			   		foreach ($reporte as $key=>$value ) {


					            if($value->VENDIDO>0){
					            	// SI TIENE DESCUENTO GENERAL Y DESCUENTO POR CUPON
					              if ($value->PORCENTAJE_GENERAL>0 && $value->CUPON_PORCENTAJE>0 ) {
					              	 	//DESCUENTO GENERAL
							             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
							             	$total_des=$total_des+$descuento_precio;
							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);
						             	//CUPON
							             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
							             	$total_des=$total_des+$descuento_precio;
							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->CUPON_PORCENTAJE)/100),2);

							                $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
							                $precio_descontado=$precio-$descuento;
							                $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
							                $precio_descontado_general=$precio_descontado-$descuento_general;
							                $precio_descontado_total=$descuento+$descuento_general;
						                //CUPON
			                                $descuento_cupon=($precio_descontado_general*$value->CUPON_PORCENTAJE)/100;
			                                $precio_descontado_total=$precio_descontado_total+$descuento_cupon;
		                               		//---------------------------------------------------------

								              $descuento_real=($precio_descontado_total*100)/$precio;

								              $value->DESCUENTO_PORCENTAJE=$descuento_real;
					                
					             }else{
					             	    //SI TIENE SOLO CUPON
		                             if ($value->CUPON_PORCENTAJE>0 ) {
							             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
							             	$total_des=$total_des+$descuento_precio;
							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

							               $descuento=($precio*$value->CUPON_PORCENTAJE)/100;
							               $precio_descontado=$precio-$descuento;
							               $descuento_general=($precio_descontado*$value->CUPON_PORCENTAJE)/100;
							               $precio_descontado_general=$precio_descontado-$descuento_general;
							               $precio_descontado_total=$descuento+$descuento_general;
							               $descuento_real=($precio_descontado_total*100)/$precio;
							               $value->DESCUENTO_PORCENTAJE=$descuento_real;
						                
						             }else{
								             	 //SI TIENE SOLO DESCUENTO GENERAL
							             if ($value->PORCENTAJE_GENERAL>0 ) {
								             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
								             	$total_des=$total_des+$descuento_precio;

								             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
								             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
								             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

								               $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
								               $precio_descontado=$precio-$descuento;
								               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
								               $precio_descontado_general=$precio_descontado-$descuento_general;
								               $precio_descontado_total=$descuento+$descuento_general;
								               $descuento_real=($precio_descontado_total*100)/$precio;
								               $value->DESCUENTO_PORCENTAJE=$descuento_real;
							                
							             }
						             }
					             }
			
	                            


					               
					             if($value->DESCUENTO_PORCENTAJE==0){
					             	$value->DESCUENTO_PORCENTAJE='0';
					             }

					            if($value->NOMBRE==NULL){
					                $value->NOMBRE='';
					             }
					            if($value->MARCA==NULL){
					                $value->MARCA='';
					             }

					                       
								$nestedData['COD_PROD'] = $value->COD_PROD;
							    $nestedData['VENDIDO'] =$value->VENDIDO;
					            $nestedData['CATEGORIA'] =$value->CATEGORIA;
					            $nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
					            $nestedData['NOMBRE']=$value->NOMBRE;
					            $nestedData['DESCUENTO_PORCENTAJE']=$value->PORCENTAJE_GENERAL;
					            $nestedData['DESCUENTO_PRODUCTO']=$value->DESCUENTO_PORCENTAJE;
					            $nestedData['PRECIO']= $value->PRECIO;
					            $nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
					            $nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
					            $nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
					            $nestedData['DESCUENTO']= $value->DESCUENTO;
					            $nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
					            $nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
					            $nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
					            $nestedData['PROVEEDOR']= $value->PROVEEDOR;
					            $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
					            $nestedData['MARCA']= $value->MARCA;
					            $nestedData['LOTE']= $value->LOTE;
					            $nestedData['ID_SUCURSAL']=$datos["sucursal"];
					    	    $nestedData['USER_ID']=$user->id;
					    	    $nestedData['CREDITO_COBRADO']=1;
					    	    if(isset($datos["utilidad"])){
				             		$nestedData["UTILIDAD"]=($value->PRECIO)-($value->COSTO_TOTAL);
				             		}
								    

					             

	                             $venta_in[]=$nestedData;
					              	
								}
			              
			           		# code...

			        };
			      
			         log::error(["des"=>$total_des]);
			           foreach (array_chunk($venta_in,1000) as $t) {

	                              	Temp_venta::insert($t);


	                            }

    }
    public static function nota_credito_venta_credito($datos)
    {
     	    $user = auth()->user();
	     	$reporte=DB::connection('retail')->table('NOTA_CREDITO_DET')
	           
			      ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
			      ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
			      ->leftjoin('PRODUCTOS_AUX',function($join){
			       $join->on('PRODUCTOS_AUX.CODIGO','=','NOTA_CREDITO_DET.CODIGO_PROD')
			         ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
			      })
			      
			      ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
			      ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'NOTA_CREDITO_DET.FK_VENTASDET')
			      ->leftjoin('nota_credito_tiene_lote', 'nota_credito_tiene_lote.FK_VENTA_DET', '=', 'VENTASDET.ID')
			      ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
			      ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
			      ->leftjoin('LOTES', 'LOTES.ID', '=', 'nota_credito_tiene_lote.ID_LOTE')
			      ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
			      ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
			      ->leftjoin('VENTAS',function($join){
					   $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
					    ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
					    ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
			        })
			      
	       

				    ->select(
					    DB::raw('NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
					     nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
					     MARCA.DESCRIPCION AS MARCA,
					     LOTES.LOTE AS LOTE,
					     LOTES.COSTO AS COSTO_UNIT,
					     (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
					     LINEAS.DESCRIPCION AS CATEGORIA,
					     SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
					     SUBLINEA_DET.DESCRIPCION AS NOMBRE,
					     PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
					     PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
					     (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
					     NOTA_CREDITO.TIPO AS TIPO_NOTA,
					     NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
					    DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
					    DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
					    DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))
					->Where('VENTASDET.ANULADO','<>',1)
					->Where('NOTA_CREDITO.PROCESADO','=',1)
					->WHERE('VENTAS.TIPO','=','CR')
					->whereBetween('NOTA_CREDITO.FECMODIF', [$datos["inicio"], $datos["final"] ])
					->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',$datos["sucursal"]) 
					->orderby('VENTASDET.COD_PROD')->get()->toArray();
					$venta_nc=array();

			

	          foreach ($reporte as $key => $value) 
	          {
	         	        if($value->NOMBRE==NULL){
					           $value->NOMBRE='';
					    }

					    if($value->MARCA==NULL){
					           $value->MARCA=' ';
					    }

		         	  		$nestedDataNC['COD_PROD'] = $value->COD_PROD;
		         	  		if($value->TIPO_NOTA===1){
		         	  			$nestedDataNC['VENDIDO'] =-$value->VENDIDO;
		         	  		}elseif ($value->TIPO_NOTA===3) {
		         	  				$nestedDataNC['VENDIDO'] =0;
		         	  		}
							
						    $nestedDataNC['CATEGORIA'] =$value->CATEGORIA;
		             		$nestedDataNC['SUBCATEGORIA']=$value->SUBCATEGORIA;
						    $nestedDataNC['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
						    $nestedDataNC['DESCUENTO_PORCENTAJE']=0;
						    $nestedDataNC['DESCUENTO_PRODUCTO']=0;
						    $nestedDataNC['PRECIO']= $value->PRECIO*-1;
						    $nestedDataNC['PRECIO_UNIT']= $value->PRECIO_UNIT*-1;
						    $nestedDataNC['COSTO_UNIT']= $value->COSTO_UNIT*-1;
						    $nestedDataNC['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
						    $nestedDataNC['DESCUENTO']= 0;
						    $nestedDataNC['MARCAS_CODIGO']= $value->MARCA_CODIGO;
						    $nestedDataNC['LINEA_CODIGO']=$value->LINEA_CODIGO;
						    $nestedDataNC['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
						    $nestedDataNC['PROVEEDOR']= $value->PROVEEDOR;
						    $nestedDataNC['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
						    $nestedDataNC['MARCA']= $value->MARCA;
						    $nestedDataNC['LOTE']= $value->LOTE;
						    $nestedDataNC['ID_SUCURSAL']=$datos["sucursal"];
						    $nestedDataNC['USER_ID']=$user->id;
						    $nestedDataNC['CREDITO_COBRADO']=1;
						    if(isset($datos["utilidad"])){
				             		$nestedData["UTILIDAD"]=($value->PRECIO*-1)-($value->COSTO_TOTAL);
				             		}
						
						     $venta_nc[]=$nestedDataNC;
	         	
	         }

	          foreach (array_chunk($venta_nc,1000) as $t) {
	                Temp_venta::insert($t);
	           } 

    }
    public static function insertar_reporte_Entrada_Seccion($datos)
    {
     		$user = auth()->user();
     		Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$datos["sucursal"])->delete();
       
     	    $reporte=DB::connection('retail')->table('COMPRAS')
             
            
            ->leftjoin('COMPRASDET',function($join){
             $join->on('COMPRASDET.CODIGO','=','COMPRAS.CODIGO')
                  ->on('COMPRASDET.ID_SUCURSAL','=','COMPRAS.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS_AUX',function($join){
             $join->on('PRODUCTOS_AUX.CODIGO','=','COMPRASDET.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
             $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','COMPRASDET.COD_PROD')
                  ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
          	->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
            ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
            ->select(
            DB::raw(
             'COMPRASDET.COD_PROD AS COD_PROD,
             SUM(COMPRASDET.CANTIDAD) AS VENDIDO,
             IFNULL(MARCA.DESCRIPCION,"INDEFINIDO") AS MARCA,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             SUM(COMPRASDET.COSTO_TOTAL) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             IFNULL(SUBLINEAS.DESCRIPCION,"INDEFINIDO") AS SUBCATEGORIA,
             IFNULL(SUBLINEA_DET.DESCRIPCION,"INDEFINIDO") AS NOMBRE,
             PROVEEDORES.CODIGO AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO,
             IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO,
             IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO,
             IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
             IFNULL(SECCIONES.DESCRIPCION,"INDEFINIDO") AS SECCION'))
	      
	        ->whereBetween('COMPRAS.FECALTAS', [$datos["inicio"], $datos["final"] ])
	        ->Where('COMPRAS.ID_SUCURSAL','=',$datos["sucursal"]) 
	        ->orderby('COMPRASDET.COD_PROD')
	        ->GroupBy('COMPRAS.PROVEEDOR','COMPRASDET.COD_PROD','LOTES.ID');
	        
	        if(isset($datos["checkedProveedor"])){
	        	if(!$datos["checkedProveedor"]){
	        		$reporte->whereIn('COMPRAS.PROVEEDOR',$datos["proveedores"]);
	            }
	        }
	          if(isset($datos["checkedSeccion"])){
		           	if(!$datos["checkedSeccion"]){
		           			$reporte->whereIn('SECCIONES.ID',$datos["secciones"]);
		           	}
           	}
	        

	        $reporte=$reporte->get()->toArray();
	        	

			
			$compra_in = array();
		     

		   foreach ($reporte as $key=>$value ) {

				             

				           

				                       
							$nestedData['COD_PROD'] = $value->COD_PROD;
						    $nestedData['VENDIDO'] =$value->VENDIDO;
				            $nestedData['CATEGORIA'] =$value->CATEGORIA;
				            $nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
				            $nestedData['NOMBRE']=$value->NOMBRE;
				            $nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
				            $nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
				            $nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				            $nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
				            $nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				            $nestedData['PROVEEDOR']= $value->PROVEEDOR;
				            $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				            $nestedData['MARCA']= $value->MARCA;
				            $nestedData['LOTE']= $value->LOTE;
				            $nestedData['ID_SUCURSAL']=$datos["sucursal"];
				    	    $nestedData['USER_ID']=$user->id;
				            $nestedData['SECCION']= $value->SECCION;
				            $nestedData['SECCION_CODIGO']= $value->SECCION_CODIGO;
				    	    
							    

				             

                             $compra_in[]=$nestedData;
				              	
							
		              
		           		# code...

		        };
		      
		       
		           foreach (array_chunk($compra_in,1000) as $t) {

                              	Temp_venta::insert($t);


                            }
                            
       
     	    $reporte=DB::connection('retail')->table('COMPRAS')
             
            
            ->leftjoin('COMPRASDET',function($join){
             $join->on('COMPRASDET.CODIGO','=','COMPRAS.CODIGO')
                  ->on('COMPRASDET.ID_SUCURSAL','=','COMPRAS.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS_AUX',function($join){
             $join->on('PRODUCTOS_AUX.CODIGO','=','COMPRASDET.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
             $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','COMPRASDET.COD_PROD')
                  ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
          	->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
            ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
            ->leftjoin('VENTASDET_TIENE_LOTES','VENTASDET_TIENE_LOTES.ID_LOTE','=','LOTES.ID')
            ->leftjoin('VENTASDET','VENTASDET.ID','=','VENTASDET_TIENE_LOTES.ID_VENTAS_DET')
            ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
                  ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
            })
           
            ->leftjoin('VENTAS_CUPON','VENTAS_CUPON.FK_VENTA','=','VENTAS.ID')
	        ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(
            DB::raw(
             'COMPRASDET.COD_PROD AS COD_PROD,
             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
             IFNULL(MARCA.DESCRIPCION,"INDEFINIDO") AS MARCA,
             LOTES.LOTE AS LOTE,
             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
	         VENTASDET.PRECIO_UNIT AS PRECIO_UNIT,
             LINEAS.DESCRIPCION AS CATEGORIA,
             IFNULL(SUBLINEAS.DESCRIPCION,"INDEFINIDO") AS SUBCATEGORIA,
             IFNULL(SUBLINEA_DET.DESCRIPCION,"INDEFINIDO") AS NOMBRE,
             PROVEEDORES.CODIGO AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO,
             IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO,
             IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO,
             IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
             IFNULL(SECCIONES.DESCRIPCION,"INDEFINIDO") AS SECCION,
             IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL,
			 IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE,0) AS CUPON_PORCENTAJE'))
	      
	        ->whereBetween('COMPRAS.FECALTAS', [$datos["inicio"], $datos["final"] ])
	        ->Where('COMPRAS.ID_SUCURSAL','=',$datos["sucursal"]) 
	       
	        ->orderby('COMPRASDET.COD_PROD')
	        ->GroupBy('COMPRAS.PROVEEDOR','COMPRASDET.COD_PROD','LOTES.ID');
	        
	        if(isset($datos["checkedProveedor"])){
	        	if(!$datos["checkedProveedor"]){
	        		$reporte->whereIn('COMPRAS.PROVEEDOR',$datos["proveedores"]);
	            }
	        }
	          if(isset($datos["checkedSeccion"])){
		           	if(!$datos["checkedSeccion"]){
		           			$reporte->whereIn('SECCIONES.ID',$datos["secciones"]);
		           	}
           	}
	        

	        $reporte=$reporte->get()->toArray();
	        	

			
			$venta_in = array();
		     

		   foreach ($reporte as $key=>$value ) {

				             		if ($value->PORCENTAJE_GENERAL>0 && $value->CUPON_PORCENTAJE>0 ) {
				              	 	   //DESCUENTO GENERAL
						             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
						           
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$/*value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));*/
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);
					            
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             /*	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));*/
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->CUPON_PORCENTAJE)/100),2);

						              
	                               		//---------------------------------------------------------

							             /* $descuento_real=($precio_descontado_total*100)/$precio;

							              $value->DESCUENTO_PORCENTAJE=$descuento_real;*/
				                
				             }else{
				             	    //SI TIENE SOLO CUPON
	                             if ($value->CUPON_PORCENTAJE>0 ) {
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	/*$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));*/
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

					                
					             }else{
							            //SI TIENE SOLO DESCUENTO GENERAL
						             if ($value->PORCENTAJE_GENERAL>0 ) {
							             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
							      

							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	/*$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));*/
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

							             
						                
						             }
						         }
						      }

				           

				                       
							$nestedData_venta['COD_PROD'] = $value->COD_PROD;
						    $nestedData_venta['VENDIDO'] =$value->VENDIDO;
				            $nestedData_venta['CATEGORIA'] =$value->CATEGORIA;
				            $nestedData_venta['SUBCATEGORIA']=$value->SUBCATEGORIA;
				            $nestedData_venta['NOMBRE']=$value->NOMBRE;
				            $nestedData_venta['PRECIO_UNIT']= $value->PRECIO_UNIT;
				            $nestedData_venta['PRECIO']= $value->PRECIO;
				            $nestedData_venta['COSTO_TOTAL']= 0;
				            $nestedData_venta['COSTO_UNIT']= 0;
				            $nestedData_venta['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				            $nestedData_venta['LINEA_CODIGO']=$value->LINEA_CODIGO;
				            $nestedData_venta['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				            $nestedData_venta['PROVEEDOR']= $value->PROVEEDOR;
				            $nestedData_venta['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				            $nestedData_venta['MARCA']= $value->MARCA;
				            $nestedData_venta['LOTE']= $value->LOTE;
				            $nestedData_venta['ID_SUCURSAL']=$datos["sucursal"];
				    	    $nestedData_venta['USER_ID']=$user->id;
				            $nestedData_venta['SECCION']= $value->SECCION;
				            $nestedData_venta['SECCION_CODIGO']= $value->SECCION_CODIGO;
				    	    $nestedData_venta['CREDITO_COBRADO']= 1;
							    

				             

                             $venta_in[]=$nestedData_venta;
				              	
							
		              
		           		# code...

		        };
		      
		       
		           foreach (array_chunk($venta_in,1000) as $t) {

                              	Temp_venta::insert($t);


                            }

    
    }
    public static function insertar_reporte_Compra_Venta_Seccion($datos)
    {
     		$user = auth()->user();
     		Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$datos["sucursal"])->delete();
       
     	    $reporte=DB::connection('retail')->table('VENTASDET')
             
            
	            /*->leftjoin('COMPRASDET',function($join){
	             $join->on('COMPRASDET.CODIGO','=','COMPRAS.CODIGO')
	                  ->on('COMPRASDET.ID_SUCURSAL','=','COMPRAS.ID_SUCURSAL');
	            })*/
	            //UNIR LA TABLA VENTAS PARA LUEGO CONSEGUIR LOS DESCUENTOS TANTO POR CUPON O POR DESCUENTO GENERAL
	            ->leftjoin('VENTAS',function($join){
	             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
	             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
	               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
	            })
	            //----------------------------------------------------------------------------------------------------------------
	            //TRAER LA TABLA DE DESCUENTO POR PRODUCTO Y EL DESCUENTO DE CUPON Y GENERAL
	            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
	            ->leftjoin('VENTAS_CUPON','VENTAS_CUPON.FK_VENTA','=','VENTAS.ID')
	            ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
	            //----------------------------------------------------------------------------------------------------------------
	            //UNIR PRODUCTOS Y PAUX Y LAS SECCIONES DE LOS PRODUCTOS
	            ->leftjoin('PRODUCTOS_AUX',function($join){
	             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
	               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
	            })
	             ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
	            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
	             $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','PRODUCTOS.CODIGO')
	                  ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
	            })
	           
	            //----------------------------------------------------------------------------------------------------------------
	            //TRAER A QUE LOTE CORRESPONDE, QUE COMPRA SI ES POR COMPRA Y DEMAS

	            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
	            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
	           /* ->leftjoin('LOTE_TIENE_COMPRASDET','LOTE_TIENE_COMPRASDET.ID_LOTE','=','LOTES.ID')
	            ->leftjoin('COMPRASDET','COMPRASDET.ID','=','LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET')

	            ->leftjoin('COMPRAS',function($join){
	             $join->on('COMPRAS.CODIGO','=','COMPRASDET.CODIGO')
	                  ->on('COMPRAS.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
	            })*/
	            //----------------------------------------------------------------------------------------------------------------
	            //TODOS LOS ATRIBUTOS DEL PRODUCTO, TANTO PROVEEDOR,SECCION,MARCA,CATAGORIA,SUBCATEGORIA Y NOMBRE.
	        
	            ->leftjoin('PROVEEDORES AS P2', 'P2.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
	            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
	          	->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
	            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
	            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
	            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
	            //-----------------------------------------------------------------------------------------------------------------
	           /* ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')*/

	            ->select(
	            DB::raw(
	             'VENTASDET.COD_PROD AS COD_PROD,
	             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
	             IFNULL(MARCA.DESCRIPCION,"INDEFINIDO") AS MARCA,
	             LOTES.LOTE AS LOTE,
	             LOTES.COSTO AS COSTO_UNIT,
	             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
	             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
	             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT,
	             IFNULL(LINEAS.DESCRIPCION,"INDEFINIDO") AS CATEGORIA,
	             IFNULL(SUBLINEAS.DESCRIPCION,"INDEFINIDO") AS SUBCATEGORIA,
	             IFNULL(SUBLINEA_DET.DESCRIPCION,"INDEFINIDO") AS NOMBRE,
	             
	             IFNULL(P2.CODIGO,0) AS PROVEEDOR,
	             IFNULL(P2.NOMBRE,"INDEFINIDO") AS PROVEEDOR_NOMBRE,
	             IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO,
	             IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO,
	             IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO,
	             IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO,
	             IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO,
				 IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL,
				 IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE,0) AS CUPON_PORCENTAJE,
				 IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE,
	             IFNULL(SECCIONES.DESCRIPCION,"INDEFINIDO") AS SECCION'))
		        ->whereBetween('VENTASDET.FECALTAS', [$datos["inicio"], $datos["final"] ])
		        ->Where('VENTAS.ID_SUCURSAL','=',$datos["sucursal"]) 
		        ->Where('VENTASDET.ANULADO','<>',1) ;
		       /* ->orderby('VENTASDET.COD_PROD')*/
		      
		        if(isset($datos["checkedProveedor"])){
		        	if(!$datos["checkedProveedor"]){
		        		$reporte->whereIn('P2.CODIGO',$datos["proveedores"]);
					    /*->orWhere(function ($query) {
					        $query->whereIn('P2.PROVEEDOR',$datos["proveedores"]);
					    });*/
		            }
		        }
		          if(isset($datos["checkedSeccion"])){
			           	if(!$datos["checkedSeccion"]){
			           			$reporte->whereIn('SECCIONES.ID',$datos["secciones"]);
			           	}
	           	}
		        

		        $reporte=$reporte->get()->toArray();
	        	

			$precio=100;
			$descuento_precio=0;
			$precio_descontado=0;
			$costo=0;
			$descuento_cupon=0;
			$precio_descontado_cupon=0;
			$total_dev=0;
			$total_des=0;
			$descuento=0;
			$descuento_general=0;
			$precio_descontado_general=0;
			$precio_descontado_total=0;
			$descuento_real=0;
			$compra_in = array();
		     

		   foreach ($reporte as $key=>$value ) {

				             
		   			 if ($value->PORCENTAJE_GENERAL>0 && $value->CUPON_PORCENTAJE>0 ) {
				              	 	//DESCUENTO GENERAL
						             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
						             /*	$total_des=$total_des+$descuento_precio;*/
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);
					             	//CUPON
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             /*	$total_des=$total_des+$descuento_precio;*/
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->CUPON_PORCENTAJE)/100),2);

						               /* $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
						                $precio_descontado=$precio-$descuento;
						                $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
						                $precio_descontado_general=$precio_descontado-$descuento_general;
						                $precio_descontado_total=$descuento+$descuento_general;
					                //CUPON
		                                $descuento_cupon=($precio_descontado_general*$value->CUPON_PORCENTAJE)/100;
		                                $precio_descontado_total=$precio_descontado_total+$descuento_cupon;
	                               		//---------------------------------------------------------

							              $descuento_real=($precio_descontado_total*100)/$precio;

							              $value->DESCUENTO_PORCENTAJE=$descuento_real;*/
				                
				             }else{
				             	    //SI TIENE SOLO CUPON
	                             if ($value->CUPON_PORCENTAJE>0 ) {
						             	$descuento_precio=round((($value->PRECIO*$value->CUPON_PORCENTAJE)/100),2);
						             	$total_des=$total_des+$descuento_precio;
						             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
						             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
						             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

						              /* $descuento=($precio*$value->CUPON_PORCENTAJE)/100;
						               $precio_descontado=$precio-$descuento;
						               $descuento_general=($precio_descontado*$value->CUPON_PORCENTAJE)/100;
						               $precio_descontado_general=$precio_descontado-$descuento_general;
						               $precio_descontado_total=$descuento+$descuento_general;
						               $descuento_real=($precio_descontado_total*100)/$precio;
						               $value->DESCUENTO_PORCENTAJE=$descuento_real;*/
					                
					             }else{
							             	 //SI TIENE SOLO DESCUENTO GENERAL
						             if ($value->PORCENTAJE_GENERAL>0 ) {
							             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
							             	$total_des=$total_des+$descuento_precio;

							             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
							             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
							             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

							              /* $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
							               $precio_descontado=$precio-$descuento;
							               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
							               $precio_descontado_general=$precio_descontado-$descuento_general;
							               $precio_descontado_total=$descuento+$descuento_general;
							               $descuento_real=($precio_descontado_total*100)/$precio;
							               $value->DESCUENTO_PORCENTAJE=$descuento_real;*/
						                
						             }
						         }
						      }
				           

				                       
							$nestedData['COD_PROD'] = $value->COD_PROD;
						    $nestedData['VENDIDO'] =$value->VENDIDO;
				            $nestedData['CATEGORIA'] =$value->CATEGORIA;
				            $nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
				            $nestedData['NOMBRE']=$value->NOMBRE;
				            $nestedData['PRECIO']= $value->PRECIO;
				            $nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
 							$nestedData['DESCUENTO']= $value->DESCUENTO;
				            $nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
				            $nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
				            $nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				            $nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
				            $nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				            $nestedData['PROVEEDOR']= $value->PROVEEDOR;
				            	 $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				            /*if($value->PROVEEDOR===0){
				            	$nestedData['PROVEEDOR']= $value->PROVEEDOR_AUX;
				            	 $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE_AUX;
				            }else{
				            	 $nestedData['PROVEEDOR']= $value->PROVEEDOR;
				            	 $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				            }*/
				           
				           
				            $nestedData['MARCA']= $value->MARCA;
				            $nestedData['LOTE']= $value->LOTE;
				            $nestedData['ID_SUCURSAL']=$datos["sucursal"];
				    	    $nestedData['USER_ID']=$user->id;
				            $nestedData['SECCION']= $value->SECCION;
				            $nestedData['SECCION_CODIGO']= $value->SECCION_CODIGO;
				    	    
							    

				             

                             $compra_in[]=$nestedData;
				              	
							
		              
		           		# code...

		        };
		      
		       
		           foreach (array_chunk($compra_in,1000) as $t) {

                              	Temp_venta::insert($t);


                            }
    
    }
}

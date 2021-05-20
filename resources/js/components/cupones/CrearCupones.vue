<template>

	<div v-if="$can('cupones.crear') && $can('cupones')" class="container">


		  <div >

				<div class="offset-md-2 col-7">
				
				       <input class="form-control mt-3" type="text" name="post_title" size="30" v-model="codigo_cupon" value="" id="title" placeholder="Código de cupón" spellcheck="true" autocomplete="off" disabled="true" v-bind:class="{ 'is-invalid': validarCupon }"/> 
			      
			    </div>
             
		  </div>
                   <div class="offset-md-2 col-7">
                   
                   	  <button v-on:click="Obtener_Cupon"  class="btn btn-primary mt-3 mb-3">Generar un código de cupón </button>

				    </div>

				    <div class="offset-md-2 col-7">
				    	<textarea class="form-control" v-model="descripcion" name="excerpt" placeholder="Descripción (opcional)"></textarea>
				    </div>
				

		 <div class="offset-md-2 col-7">
		 	

		 	  <div class="card mt-3 shadow-sm">
		 	  	
                 <h5 class="card-header">Cupones</h5>
                 <div class="card-body">
                 	

                 	 <div class="row">
                 	    <div class="col-3">
						    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
						      <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">General</a>
						      <a class="nav-link" id="v- pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Restricción de uso</a>
						      <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Limites de uso</a>
						    </div>
                        </div>
					  <div class="col-9">
					    <div class="tab-content" id="v-pills-tabContent">
					      <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
					      	    <p class=" form-field discount_type_field">

							      	<div class="row">

							      		<div class="col-md-4">
							      			<label for="discount_type">Tipo de descuento</label>
							      		</div>	

										<div class="col-md-8">
							      			<select v-on:change="cambiar_descri" v-model="opcion_cupon" style=""  name="discount_type" class="select short form-control">
												<option  value="1">Descuento en porcentaje</option>
												<option  value="2">Descuento en monto</option>
											</select>
							      		</div>	
												
									</div>
			                    </p>
			                 <p class="form-field coupon_amount_field ">
				                	<div class="row">
				                		<div class="col-md-4">
				                			 <label for="validationTooltip01">Importe del cupón</label>
				                		</div>
				                		<div class="col-md-8">

				                			

												<div class="input-group input-group-sm mb-3" >
													<div class="input-group-prepend">
														<span class="input-group-text" id="inputGroup-sizing-sm">{{mostrar_descri}}</span>
													</div>
										    		<input class="form-control form-control-sm" v-model="importe_cupon" style="" name="coupon_amount" id="coupon_amount"  placeholder="0" v-on:blur="formatoImporte" v-bind:class="{ 'is-invalid': validarImporte }"/>
												</div>
				                		</div>
				                	</div> 
					         </p>
						        <div class="row">
						        	<div class="col-md-4">
						        		<label >Fecha de caducidad</label>
						        	</div>
						        	<div class="col-md-8">
						               <div id="sandbox-container">
	                  						<div class="input-daterange input-group">
	                       						<input type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" data-date-format="YYYY-MM-DD" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
	  
	                 						</div>
							                <div class="invalid-feedback">
							                        {{messageInvalidFecha}}
							                </div>
	                			        </div>  
						        	</div>
						        </div>
					      </div>
					      <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
					      	 <div>

					      		 <div class="row">
					      		 	<div class="col-md-4">
					      		 		<label class="mt-1">Gasto mínimo</label>
					      		 	</div>
					      		 	<div class="col-md-8">

					      		 		<div class="input-group input-group-sm mb-3" >
													<div class="input-group-prepend">
														<span class="input-group-text" id="inputGroup-sizing-sm">{{monedas_descripcion}}</span>
													</div>
										    		<input type="text"  v-model="gasto_minimo"  class="form-control form-control-sm"  value="" placeholder="Sin mínimo" v-on:blur="formatoMinimo" v-bind:class="{ 'is-invalid': validarGastoMin }"/>
												</div>
					      		 	</div>
					      		 </div>

					      		 <div class="row">
					      		 	<div class="col-md-4">
					      		 		<label class="mt-2">Gasto máximo</label>
					      		 	</div>
					      		 	<div class="col-md-8">
					      		 	
					      		 		<div class="input-group input-group-sm mb-3" >
													<div class="input-group-prepend">
														<span class="input-group-text" id="inputGroup-sizing-sm">{{monedas_descripcion}}</span>
													</div>
										    		<input type="text" v-model="gasto_maximo" class="form-control form-control-sm"  value="" placeholder="No hay máximo" v-on:blur="formatoMaximo" v-bind:class="{ 'is-invalid': validarGastoMax }"/>
												</div>
					      		 	</div>
					      		 </div>
					      		 <div class="row">
					      		 	<div class="col-md-4">
					      		 		<label class="mt-2">Excluir los artículos con descuento</label>
					      		 	</div>
					      		 	<div class="col-md-8">
					      		 		<input type="checkbox" v-model="excluir_articulos_con_descuento" class=" checkbox mt-3" style=""  id="exclude_sale_items" > <span class="description">Marca esta casilla si el cupón no debe aplicarse a artículos rebajados. Los cupones para artículos concretos sólo funcionarán si el artículo no está rebajado. Los cupones para el pos completo solo funcionarán si no hay artículos rebajados dentro del pos.</span>
					      		 	</div>
					      		 </div>
					      		 <hr>
					      	 </div>
					      </div>
					      <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
					      	 <div class="row">
					      		<div class="col-md-4">
					      			<label class="mt-2">Límite de uso por cupón</label>
					      		</div>
					      		<div class="col-md-8">
					      			<input type="number" class="mt-2 form-control short" v-model="limite_uso_cupon" style="" name="usage_limit" id="usage_limit" value="" placeholder="Uso ilimitado" step="1" min="0" v-bind:class="{ 'is-invalid': validarLimiteUso }"/>
					      		</div>	
					      	 </div>
					      	 <div class="row">
					      	 	<div class="col-md-4">
					      	 		<label class="mt-2" for="usage_limit_per_user">Límite de uso por cliente</label>
					      	 	</div>

					      	 	<div class="col-md-8">
					      	 		<input type="number" v-model="limite_uso_cliente" class="form-control mt-2 short" style="" name="usage_limit_per_user" id="usage_limit_per_user" value="" placeholder="Uso ilimitado" step="1" min="0" v-bind:class="{ 'is-invalid': validarLimiteCliente }"/>
					      	 	</div>
					      	 </div>
					      </div>
					    </div>
					  </div>

                 </div>
                      


                 	 </div>
 		 	         
                   
                   	 

				   
                 </div>

		 	  </div>
                        <div v-if="existe" class="offset-md-8 col-0">
                        	<button v-on:click="guardar"  class=" btn btn-primary mt-3 mb-3">Guardar</button>
                        </div>
                        <div v-else class="offset-md-8 col-0">
                        	<button v-on:click="modificar"  class=" btn btn-warning mt-3 mb-3">Modificar</button>
                        </div>
                       		
  

		 </div>

</template>>
<script >
	export default{
		props:[],
		data(){
			return{
				codigo_cupon:'',
				selectedInicialFecha:'',
				gasto_minimo:'',
				limite_uso_cliente:'',
				descripcion:'',
				monedas_descripcion:'',
				mostrar_descri:'',
				validarGastoMax:false,
				validarGastoMin:false,
				control:false,
				candec:'',
				validarLimiteCliente:false,
				validarLimiteUso:false,
				validarCupon:false,
				existe:true,
				validarImporte:false,
				limite_uso_cupon:'',
				excluir_articulos_con_descuento:false,
				gasto_maximo:'',
				importe_cupon:'',
				opcion_cupon:"1",
				validarInicialFecha:false,
				messageInvalidFecha:'Debe completar el campo o introducir una fecha valida'
			}

		},
			methods:{
				limpiar(){
					let me=this;
					me.codigo_cupon='';
                    me.descripcion='';
                    me.opcion_cupon= 1;
                               
                    me.importe_cupon='';
                    me.selectedInicialFecha='';
                    me.gasto_maximo='';
                    me.gasto_minimo='';
                    me.excluir_articulos_con_descuento='';
                    me.limite_uso_cupon='';
                    me.limite_uso_cliente='';
				},
			Obtener_Cupon(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;
	            me.limpiar();
                me.existe=true
                	
	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO
	            Common.obtenerCuponCommon().then(data => {
              me.codigo_cupon =data;
            
              });

	          

	            // ------------------------------------------------------------------------
	        },
	      cambiar_descri(){

                // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;
	            if(me.opcion_cupon==2){
	            	me.mostrar_descri=me.monedas_descripcion;
	            }else{
	            	if(me.opcion_cupon==1){
	            		me.mostrar_descri='%'
	            	}
	            	
	            }

	          

	            // ------------------------------------------------------------------------
	        },

     
        	formatoImporte(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;


	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO


	            me.importe_cupon = Common.darFormatoCommon(me.importe_cupon, me.candec);


	            // ------------------------------------------------------------------------

	        },
	            formatoMaximo(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;


	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO


	            me.gasto_maximo = Common.darFormatoCommon(me.gasto_maximo, me.candec);


	            // ------------------------------------------------------------------------

	        },
	           formatoMinimo(){

	            // ------------------------------------------------------------------------

	            // INICIAR VARIABLES 

	            let me = this;


	            // ------------------------------------------------------------------------

	            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO


	            me.gasto_minimo = Common.darFormatoCommon(me.gasto_minimo, me.candec);


	            // ------------------------------------------------------------------------

	        },
	        controlar(){

              let me=this;
              me.control=false;
            
              if(me.codigo_cupon==='' || me.codigo_cupon===null){
              	me.validarCupon=true;
              	me.control=true;
              }else{

              	me.validarCupon=false;
              }

              if(me.importe_cupon==='' || me.importe_cupon===0 || me.importe_cupon===null){
             
              	me.control=true;
              	me.validarImporte=true;
              }else{
            
              	me.validarImporte=false;
              }

              if((me.opcion_cupon===1 && me.importe_cupon>100) || me.importe_cupon===0 || me.importe_cupon===''){
              
              	me.control=true;
              	me.validarImporte=true;
              }else{
              
              	me.validarImporte=false;
              }


              if(me.selectedInicialFecha==='' || me.selectedInicialFecha===null){
              	    me.control=true;
                    me.validarInicialFecha=true;
              }else{
              	me.validarInicialFecha=false;
              }

              if(me.limite_uso_cupon==='' || me.limite_uso_cupon===0 || me.limite_uso_cupon===null){
              	me.control=true;
              	me.validarLimiteUso=true;
              }else{
              	me.validarLimiteUso=false;
              }

              if(me.limite_uso_cliente==='' || me.limite_uso_cliente===0 || me.limite_uso_cliente===null){
              	me.control=true;
              	me.validarLimiteCliente=true;
              }else{
              	me.validarLimiteCliente=false;
              }
              if(me.limite_uso_cupon<me.limite_uso_cliente){
              	me.validarLimiteCliente=true
              	me.control=true;

              }else{
              	me.validarLimiteCliente=false;
              }
              /* me.gasto_minimo = parseFloat(me.gasto_minimo).toFixed(2);
               me.gasto_maximo= parseFloat(me.gasto_maximo).toFixed(2);*/
            
              if((me.gasto_maximo)<(me.gasto_minimo)){
              	me.validarGastoMin=true;
              	me.control=true
              }else{
              	me.validarGastoMin=false;
              }
              if(me.gasto_minimo===''){
              	me.gasto_minimo=0;
              }
              if(me.gasto_maximo===''){
              	me.gasto_maximo=0;
              }

              if(me.control===true){
              	return false;
              }else{
              	return true;
              }
	        },
	        guardar(){
	        	let me=this;
	
                if(!this.controlar()){
                	return;
                }

                 var data = {
              Codigo:me.codigo_cupon,
              Descripcion:me.descripcion, 
              Tipo:me.opcion_cupon,
              Importe:me.importe_cupon,
              Fecha_caducidad:me.selectedInicialFecha,
              Gasto_maximo:me.gasto_maximo,
              Gasto_minimo:me.gasto_minimo,
              Excluir_articulos_con_descuento:me.excluir_articulos_con_descuento,
              Limite_uso_cupon:me.limite_uso_cupon,
              Limite_uso_cliente:me.limite_uso_cliente
              
             }
           Common.guardarCuponCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el cupón!!!',
                     'success'
                  )
               }else{
                Swal.fire(
                     'Error!!',
                     data.statusText,
                     'warning'
                  )
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });

 				 me.limpiar();
	        },
	        modificar(){
	        	let me=this;
	
                if(!this.controlar()){
                	return;
                }
               

                 var data = {
              Codigo:me.codigo_cupon,
              Descripcion:me.descripcion, 
              Tipo:me.opcion_cupon,
              Importe:me.importe_cupon,
              Fecha_caducidad:me.selectedInicialFecha,
              Gasto_maximo:me.gasto_maximo,
              Gasto_minimo:me.gasto_minimo,
              Excluir_articulos_con_descuento:me.excluir_articulos_con_descuento,
              Limite_uso_cupon:me.limite_uso_cupon,
              Limite_uso_cliente:me.limite_uso_cliente
              
             }
           Common.modificarCuponCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha modificado correctamente el cupón!!!',
                     'success'
                  )
               }else{
                Swal.fire(
                     'Error!!',
                     data.statusText,
                     'warning'
                  )
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });

 				 me.limpiar();
	        },

	      
		
	      },
	      mounted(){
	         
			          let me=this;
			          
		            if(me.$route.params.id){
		            	
		            		

		            	Common.conseguirDatosCuponCommon(me.$route.params.id).then(data => {
		            		
		            		
				               if(data.response===true){
				               	me.codigo_cupon=data.datos[0].CODIGO;
                                me.descripcion=data.datos[0].DESCRIPCION;
                                me.opcion_cupon= String(data.datos[0].TIPO_CUPON);
                                me.importe_cupon=data.datos[0].IMPORTE;
                                me.selectedInicialFecha=data.datos[0].FECHA_CADUCIDAD;
                                me.gasto_maximo=data.datos[0].GASTO_MAX;
                                me.gasto_minimo=data.datos[0].GASTO_MIN;
                                me.excluir_articulos_con_descuento=data.datos[0].EXCLUIR_ARTICULOS_CON_DESCUENTO;
                                me.limite_uso_cupon=data.datos[0].USO_LIMITE;
                                me.limite_uso_cliente=data.datos[0].USO_LIMITE_CLIENTE;
                                me.existe=false;

				                 
				               }
				           
				    
				             });
		            }
			           
			Common.obtenerParametroCommon().then(data => {
		            		

				               	me.candec=data.parametros[0].CANDEC;
                                me.monedas_descripcion =data.parametros[0].DESCRIPCION;
                                if(me.opcion_cupon===1){
                                	    me.mostrar_descri='%';
                                	}else{
                                	  me.mostrar_descri=me.monedas_descripcion;
                                	}
                            

                                

				                 
		
				    
				             });
			           $(function(){
			              $('#sandbox-container .input-daterange').datepicker({
			                    keyboardNavigation: false,
			                forceParse: false, format:"yyyy-mm-dd"
			            });
			            $("#selectedInicialFecha").datepicker().on(
			              "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
			          );
			         

			          $('table').dataTable();
			          });
			               hotkeys('f2', function(event, handler){

						  event.preventDefault() 
						   me.guardar();
						});
       

	      }
	}


</script>
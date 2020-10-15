<template>
	<div class="container-fluid">
			<div class="modal fade" id="modalTransferenciaProductosDevolucion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-xl" role="document">
		    <div class="modal-content">
		    	
		      <div class="modal-header">
		        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>Detalle de Transferencia para devolucion: {{codigo_transferencia}} </small></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="my-1">
					<div class="custom-control custom-switch mr-sm-2">
					 <input v-on:change="marcar_desmarcar()" type="checkbox" class="custom-control-input" id="switchMarcarTodo" v-model="checked_todo">
					 <label class="custom-control-label" for="switchMarcarTodo">Marcar Todo</label>
					</div>
				</div>
		      <div class="modal-body">
		        <table id="transferenciaProductosDevolucion" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
					            <thead>
					                <tr>
					                    <th>ITEM</th>
					                    <th>Codigo Producto</th>
					                    <th>Descripción</th>
					                    <th>Cantidad</th>
					                    <th>Salida</th>
					                    <th>Restante</th>
					                    <th>Precio</th>
					                    <th>Total</th>
					                    <th>Accion</th>

					                </tr>
					            </thead>
					            <tbody>
					                <td></td>
					            </tbody>
					        </table>
		      </div>
		      <div class="modal-footer">
		      	<input type="button" v-on:click="devolver" name="aceptar" value="Devolver" class="btn btn-danger">
		         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

	</div>

</template>

<script>
	 export default {
	  props: [''],
      data(){
        return {
          open: false,
           nuevoMarcados: [],
          codigo_transferencia: '',
          checked_todo:false,
          codigo_origen:'',
          codigotr:'',
          actualizar:false,
          datos:[]
        }
      }, 
      methods: {
      		mostrarModal(codigo, codigo_origen){

      			// ------------------------------------------------------------------------

      			// LLAMAR AJAX PARA CARGAR DATATABLE 

      			this.obtenerDatosTranferencia(codigo, codigo_origen);

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalTransferenciaProductosDevolucion').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            marcar_desmarcar(){
            	let me=this;
            	console.log(me.checked_todo);
            	if(me.checked_todo===true){
            		  var codigo_tr={
                     codigo:me.codigotr,
                     origen:me.codigo_origen
                   }
                   me.datos=[];
                   me.nuevoMarcados=[];

                      Common.marcarTodoTransferenciaCommon(codigo_tr).then(data => {
                      	 	for (var i=0; i<data.productos.length; i++){
                      	 		 me.nuevoMarcados.push(data.productos[i]["COD_PROD"]);
                      	 		 me.datos.push({
								  "CODIGO": data.productos[i]["COD_PROD"],
								  "CANTIDAD":data.productos[i]["CANTIDAD"],
								 });

                      	 	}
							me.Marcar();
							 me.marcar_dev();
              			});
            	}else{
            		me.Desmarcar();
            		me.nuevoMarcados=[];
            		me.datos=[];

            	}
            },
                Eliminar_Array(element){

                Array.prototype.removeItem = function (a) {
                for (var i = 0; i < this.length; i++) {
              if (this[i] == a) {
               for (var i2 = i; i2 < this.length - 1; i2++) {
                this[i2] = this[i2 + 1];
               }
               this.length = this.length - 1;
               return;
              }
             }
            };

            if (this.nuevoMarcados.length > 0) {
              this.nuevoMarcados.removeItem(element);
            }
            //console.log(this.marcado); //

         
        },
                        Eliminar_Array_datos(element){
                        	console.log(element);

                Array.prototype.removeItem = function (a) {
                for (var i = 0; i < this.length; i++) {
              if (this[i] == a) {
               for (var i2 = i; i2 < this.length - 1; i2++) {
                this[i2] = this[i2 + 1];
               }
               this.length = this.length - 1;
               return;
              }
             }
            };

            if (this.datos.length > 0) {
              this.datos.removeItem(element);
            }
            //console.log(this.marcado); //

         
        },
            devolver(){
            	let me =this;
            console.log(me.nuevoMarcados);
            	   var tableTransferencia = $('#transferenciaProductosDevolucion').DataTable();
            	   var tabla = tableTransferencia.rows().data().toArray();
                   var codigo_tr={
                     codigo:me.codigotr,
                     origen:me.codigo_origen
                   }
					/*Common.devolverTransferenciaCommon(tabla,codigo_tr,me.datos).then(data => {
				    
				  		return data;
		
				    });*/
      			// ------------------------------------------------------------------------

      			// MOSTRAR LA PREGUNTA DE ELIMINAR 

      			Swal.fire({
				  title: 'Estas seguro ?',
				  text: "Devolver la transferencia"+me.codigo_tr+" ?",
				  type: 'warning',
				  showLoaderOnConfirm: true,
				  showCancelButton: true,
				  cancelButtonColor: '#3085d6',
				  confirmButtonText: 'Si, devolver!',
				  cancelButtonText: 'Cancelar',
				  preConfirm: () => {
				    return Common.devolverTransferenciaCommon(codigo_tr,me.datos).then(data => {
				    	if (!data.response === true) {
				          throw new Error(data.statusText);
				        }
				  		return data;
				  	}).catch(error => {
				        Swal.showValidationMessage(
				          `Request failed: ${error}`
				        )
				    });
				  }
				}).then((result) => {
				  if (result.value) {
				  	Swal.fire(
						      'Devuelto !',
						      'Se ha Devuelto la transferencia correctamente !',
						      'success'
					)

				  	// ------------------------------------------------------------------------

				  	// RECARGAR TABLA 
				  	
					tableTransferencia.ajax.reload( null, false );

					// ------------------------------------------------------------------------

				  }
				})

            },
             Marcar(){
		            let me=this;
		           

		             me.nuevoMarcados.map(function (x) {

		              $("#"+x).prop('checked', true);
		            });
		             me.datos.map(function (x) {

				        if (document.getElementsByName($("#"+x.CODIGO).closest('input').attr('id'))[0] !== undefined) {
				        	document.getElementsByName($("#"+x.CODIGO).closest('input').attr('id'))[0].value = x.CANTIDAD;
				        }
		        
		   			 });
		            
                },
                 Desmarcar(){
		            let me=this;
		           

		             me.nuevoMarcados.map(function (x) {

		              $("#"+x).prop('checked', false);
		            });
		            
                },
marcar_dev(){
         
            this.tableContructorNota = $('#tableConstructorNota').DataTable();
                    
            let me=this;
                  //RECORRE TODOS LOS CHECKBOX EXISTENTES EN EL DATATABLE
            $( me.tableContructorNota.$('input[type="checkbox"]').map(function ()
                           {
                   	 
                                        //PREGUNTA SI ESTA MARCADO
                                       if  ($(this).prop("checked"))  {
                                         // SI ESTA MARCADO AGREGAR 
                                            if (me.nuevoMarcados.includes($(this).closest('input').attr('id')) === false) { 
                                         	  //PREGUNTA SI ESTE CODIGO NO EXISTE EN EL ARRAY AUXILIAR
                                         	
                                         	    me.nuevoMarcados.push($(this).closest('input').attr('id'));
                                         	   //ACA SE GUARDA EL CODIGO EN UN ARRAY AUXILIAR
		 											   $( me.tableContructorNota.$('input[type="number"]').map(function () {
		 											  	//SE RECORRE TODOS LOS INPUT TIPO NUMERO PARA GUARDAR LA CANTIDAD 
				 												if (me.nuevoMarcados.includes($(this).closest('input').attr('id')) ===true) {
				 													//PREGUNTA SI ESTE CODIGO YA EXISTE EN EL ARRAY AUXILIAR
				 													 	for (var i=0; i<me.datos.length; i++) { 
				 													 		//RECORREMOS EL ARRAY ORIGINAL SI TIENE DATOS
				 													 	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){
				 													 		//PREGUNTAMOS SI EXISTE UN CODIGO IGUAL EN NUESTRO ARRAY ORIGINAL PARA ACTUALIZAR LA CANTIDAD
 																			console.log("maximo: "+$(this).closest('input').attr('max'));
 																			if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {
 																				console.log("entre maximo: "+$(this).closest('input').attr('max'));
 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

				 													 		me.datos[i]["CANTIDAD"]=document.getElementsByName($(this).closest('input').attr('id'))[0].value;
				                                                             
				                                                             me.actualizar=true;
				                                                             //ACTUALIZAR ES LA VARIABLE QUE DECIDE SI EXISTE O NO EN NUESTRO ARRAY ORIGINAL EL PRODUCTO, SI ES TRUE ES POR QUE ENCONTRO UNO IGUAL
				                                                             i=me.datos.length;
				                                                             //CERRAMOS EL FOR
				 													 	}else{
				 													 		//SI NO ENCUENTRA NINGUN CODIGO IGUAL ENTONCES ES FALSE Y SE DEBE INSERTAR
				 													 		  me.actualizar=false;
				 													 	}
				 													 }
				 													 if(me.actualizar===false){
				 													 	//PREGUNTAMOS SI ES FALSE PARA PODER INSERTAR EN NUESTRO ARRAY ORIGINAL EL DATO

				 													 	 if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {

 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

				 													 	 me.datos.push({
																	    "CODIGO": $(this).closest('input').attr('id'),
																	    "CANTIDAD":document.getElementsByName($(this).closest('input').attr('id'))[0].value,
																	     });
				 													 }
				 													
				 													 	
				 													 
				 													 
						                                                me.actualizar=false;
						                                                //PONEMOS FALSE NUEVAMENTE LA VARIABLE ACTUALIZAR PARA PROXIMOS PRODUCTOS
						                                                  }
		                                                } ) );
	                                           
	                                            }else{
	                                            	me.checked_todo=false;
	                                         	//ACA ENTRA YA QUE NUESTRO ARRAY ORIGINAL DEVUELVE QUE YA EXISTE EL PRODUCTO EN NUESTROS ARRAYS
	                                         	  $( me.tableContructorNota.$('input[type="number"]').map(function () {
	                                         	  	//RECORREMOS TODOS LOS INPUT TIPO NUMEROS
	                                         	  		 for (var i=0; i<me.datos.length; i++) { 
	                                         	  		 	//RECORREMOS NUESTRO ARRAY ORIGINAL
	 													 	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){

	 													 		if (parseInt($(this).closest('input').attr('max')) < parseInt(document.getElementsByName($(this).closest('input').attr('id'))[0].value)) {

 																				document.getElementsByName($(this).closest('input').attr('id'))[0].value = $(this).closest('input').attr('max');
 																			}

	 													 		//PREGUNTAMOS SI EXISTE ALGUN CODIGO IGUAL PARA PODER ACTUALIZAR LA CANTIDAD AL PRODUCTO EN ESPECIFICO
	 													 		me.datos[i]["CANTIDAD"]=document.getElementsByName($(this).closest('input').attr('id'))[0].value;
	                                                             
	                                                           
	                                                             i=me.datos.length;
	                                                             //CERRAMOS EL FOR
	 													 	}
	 													 }
		                                         } ) );
		        										
		                                         }
                                         
                                        } else {
                                      //ACA ENTRA CUANDO SE DESMARCA UN PRODUCTO ENTONCES PROCEDEMOS A ELIMINAR DE LOS DOS ARRAYS EL ELEMENTO
                                        for (var i=0; i<me.datos.length; i++) { 
                                        	//SE RECORRE EL ARRAY
                                        	if(me.datos[i]["CODIGO"]===$(this).closest('input').attr('id')){
                                        		//PREGUNTAMOS POR EL CODIGO QUE DESEAMOS ELIMINAR (PUEDEN SER VARIOS A LA VEZ YA QUE ES UN WHILE POR CADA CHECK) 
 								
                                                             
                                                              me.datos.splice(i, 1); 
                                                              i--;
                                                              //FORMULA QUE ELIMINA EL ELEMENTO DE NUESTRO ARRAY ORIGINAL
                                                        
                                                              
                                                             
 										  }
                                        	}
                                        me.Eliminar_Array($(this).closest('input').attr('id'));
                                        //EN ESTA FUNCION ENVIAMOS EL CODIGO DEL PRODUCTO EN UNA FUNCION QUE ELIMINA EL ELEMENTO DE NUESTRO ARRAY AUXILIAR
                                       
                                       };
                             } ) );
            },
            obtenerDatosTranferencia(codigo, codigo_origen){

            	// ------------------------------------------------------------------------

            	let me = this;
              me.codigotr=codigo;
              me.codigo_origen=codigo_origen;
               me.nuevoMarcados=[];
            me.datos=[];
        	      
            	// ------------------------------------------------------------------------

            	// PREPARAR DATATABLE 

	 			var tableProductosTransferencia = $('#transferenciaProductosDevolucion').DataTable({
	                 	"processing": true,
	                 	"serverSide": true,
	                 	"destroy": true,
	                 	"bAutoWidth": true,
	                 	"select": true,
	                 	"ajax":{
	                 			"data": {
	                 				codigoTransferencia: codigo,
	                 				codigo_origen: codigo_origen
	                 			},
	                             "url": "/transferenciasMostrarProductosDevolucion",
	                             "dataType": "json",
	                             "type": "GET",
	                             "contentType": "application/json; charset=utf-8"
	                           },
	                    "columns": [
	                            { "data": "ITEM" },
	                            { "data": "COD_PROD" },
	                            { "data": "DESCRIPCION" },
	                            { "data": "CANTIDAD" },
	                            { "data": "SALIDA" },
	                            { "data": "RESTANTE" },
	                            { "data": "PRECIO" },
	                            { "data": "TOTAL" },
	                            { "data": "ACCION" }
	                        ],
	                         "fnDrawCallback": function( oSettings ) {

                       			me.Marcar();


                          //console.log(me.array_marcados);
                       //   me.marcados.map(function (x) {
                           
                           // 
                          //});
               
                          //   if(me.array_marcados[me.info2.page]!==undefined){
                           //   me.array_marcados[me.info2.page].map(function (x) {
                             //   $("#"+me.array_marcados[me.info2.page][x]).prop('checked', true);
                              //  });
                            // }
                         
                              
                    
                            

                          
                        }          
	                });
                    
	 				// ------------------------------------------------------------------------

	 				// CARGAR CODIGO TRANSFERENCIA

      				this.codigo_transferencia = codigo;

      				// ------------------------------------------------------------------------
            }
      },
        mounted() {
        	
        	let me=this;


           		 $(document).ready( function () {

 					    $('#transferenciaProductosDevolucion').on('change', 'tbody tr', function() {
                
				
				  me.marcar_dev();
                    // *******************************************************************
                          //me.marcados.map(function (x) {
                    
                   //         me.marcados[x]=me.marcados[x];
                           // $("#"+x).prop('checked', true);
                     //     });

                   
                   //me.marcados = $( tableSubCategoria.$('input[type="checkbox"]').map(function () {
                    //return $(this).prop("checked") ? $(this).closest('input').attr('id') : null;
                    //} ) );
                   
                 
                   
//                    if(me.marcado.length>0){
  //                    me.marcados.map(function(x){
    //                  me.Eliminar_Array(me.marcados[x]);
      //              });
        //            }

          //          me.array_marcados[info.page] = me.marcados;
                           // $("#"+x).prop('checked', true);
            
                    //rows.map(function)
                   

                    
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    


                    // *******************************************************************

                });
           		 

           		 });

           		 
        }
    }
</script>
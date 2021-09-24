<template>
		<!-- VENTA POR GONDOLA -->
	<div class="offset-md-3 col-3">

		<div class="card shadow border-bottom-primary mt-3" >
		  	<div class="card-header">Productos En Gondola</div>
			<div class="card-body">
			  	<div class="form-row">



					<!-- ------------------------------------------- GONDOLAS ----------------------------------------------- -->
				
					<div class=" offset-md-3 col-md-6">
						<label for="validationTooltip02">Seleccione Góndola</label> 
						<div class="container_checkbox1 rounded">
		                    <div class="ml-3" v-for="gondola in gondolas">
		                      <div class="custom-control custom-checkbox">
		                        <input  type="checkbox" class="custom-control-input" :disabled="onGondola" 
		                        :value="gondola.ID" 
		                        :id="gondola.ID" 
		                        v-model="selectedGondola" 
		                        v-bind:class="{ 'is-invalid': validarGondola }">
		                        <label class="custom-control-label" :for="gondola.ID">{{gondola.DESCRIPCION}}</label>
		                      </div>
		                    </div>
		                </div>
						<div>
						    <div class="form-text text-danger">{{messageInvalidGondola}}</div>
						</div>
						<div class="custom-control custom-switch mt-3">
							<input  type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onGondola" v-on:change="seleccionarTodo">
							<label class="custom-control-label" for="customSwitch2">Seleccionar todos</label>

							<input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-model="switch_stock">
							<label class="custom-control-label" for="customControlAutosizing" data-toggle="tooltip" data-placement="top" title="Cuando activas esta opcion muestra los productos con Stock en las gondolas, Si la desactivas te mostrara los productos sin Stock de las Gondolas">Stock</label>
						</div>
		            </div>
				</div>
				
				
					<button class="  offset-md-3 col-md-6 mt-3 btn btn-dark btn-sm" type="submit" v-on:click="descargar()"><font-awesome-icon icon="download" /> Descargar</button>
				
			</div>
		</div>


		<!-- CARD PARA MARCA Y SU CATEGORIA -->

		<div class=" row">

			<!-- SPINNER DESCARGA -->

			<div class="col-md-12">
				<div v-if="descarga" class="d-flex justify-content-center mt-3">
					<strong>Descargando...   </strong>
	                <div class="spinner-border text-success" role="status" aria-hidden="true"></div>
	             </div>
            </div>

	     	

         
        </div>






	</div>
		<!-- FIN DE PRODUCTOS POR GONDOLA -->




</template>

<script >
	export default {
        data(){
            return {
              	controlar:false,
              	datos: {},              
              	descarga: false,              
              	gondolas: [],
              	selectedGondola: [],
              	messageInvalidGondola: '',
              	validarGondola: false,
              	onGondola: false,              	
              	responseGondola: [],
				switch_stock:false
            }
        },
        methods: {
	      	seleccionarTodo(){

	      		let me = this;

	      		if(me.onGondola === true) {
	      			me.selectedGondola=[];
			        for (var key in me.gondolas){
			        	me.selectedGondola[key] = me.gondolas[key].ID;
			        }
			    }else{

			    }

	      		
			   
	      	},

            llamarBusquedas(){	


      			// LLAMAR FUNCION PARA FILTRAR GONDOLAS

      			Common.obtenerGondolasEncargadaSeccionCommon().then(data => {

	                this.gondolas = data;
      			});
	        },
	        descargar(){
	        	
	        	let me = this;	
	        		
	        	if(this.generarConsulta() === true) {
	        		me.descarga = true;
		        	axios({
					  url: '/export_productos_en_gondola',
					  method: 'POST',
					  data: me.datos,
					  responseType: 'blob', // important
					}).then((response) => {
						me.descarga = false;
					   const url = window.URL.createObjectURL(new Blob([response.data]));
					   const link = document.createElement('a');
					   link.href = url;
					   link.setAttribute('download', 'Productos_en_Gondola.xlsx'); //or any other extension
					   document.body.appendChild(link);
					   link.click();
					});
				}
	        },
	      
	        generarConsulta(){
	        	
	        	let me=this;
	

	        	if(me.onGondola === false && me.selectedGondola.length === 0) {
	        		me.messageInvalidGondola = 'Por favor seleccione una o varias Góndolas.';
	        		me.validarGondola = true;
	        		me.controlar = true;
	        	} else {
	        		me.messageInvalidGondola = '';
	        		me.validarGondola = false;
	        	}	
	        	if(me.controlar===true){
	        		me.controlar=false;
	        		return;
	        	}


 				if(me.onGondola === true) {
 					me.selectedGondola=[]
		        	for (var key in me.gondolas){
		        		me.selectedGondola[key] = me.gondolas[key].ID;
		        	}
		        }

	        	me.datos = {
		        	
		        	
					Gondolas: me.selectedGondola,
					AllGondolas: me.onGondola,
				 	Stock : me.switch_stock
				 	
	        	};
	        	
	        	return true;
	        }
        },
        mounted() {
        	let me = this;
/*        	Common.obtenerParametroCommon().then(data => {
		            		

				               	me.candec=data.parametros[0].CANDEC;
                                me.monedas_descripcion =data.parametros[0].DESCRIPCION;
                               
                            

                                

				                 
		
				    
				             });*/

        	

			me.llamarBusquedas();
        }
    }    
</script>
<style>
	.container_checkbox1 { 
		border:2px solid #ccc; 
		height: 224px; 
		overflow-y: scroll; 
	}
</style>
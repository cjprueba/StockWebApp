<template>
	<div class="container">
		<form>
			<!-- ------------------------------------------------------------------------------------- -->

			<!-- TITULO  -->
			
			<div class="col-md-12">
				<vs-divider>
					Cambio de Monedas
				</vs-divider>
			</div>
			
	        <!-- ------------------------------------------------------------------------------------- -->
			
			<div class="mt-4 ml-1 row">

	        	<!-- SELECCION DE MONEDAS -->

				<div class="col-sm-2">
					<select-moneda v-model="moneda.de" @cantidad_decimales="cantidadDecimal"></select-moneda>
	    		</div>

	    		<div class="col-sm-2">
					<select-moneda v-model="moneda.a"></select-moneda>
	    		</div>

	    		<!-- LISTAS DE SELECCION DE MES -->

	    		<div class="col-sm">
						<label>Mes</label>
		    			<select v-on:change="diasEnUnMes(mes, año)" v-model="mes" class="custom-select custom-select-sm">
		    				<option value="0">Única</option>
		  	  				<option value="1">Enero</option>
		    				<option value="2">Febrero</option>
		    				<option value="3">Marzo</option>
		  	  				<option value="4">Abril</option>
		    				<option value="5">Mayo</option>
		    				<option value="6">Junio</option>
		  	  				<option value="7">Julio</option>
		    				<option value="8">Agosto</option>
		    				<option value="9">Septiembre</option>
		  	  				<option value="10">Octubre</option>
		    				<option value="11">Noviembre</option>
		    				<option value="12">Diciembre</option>
		  				</select>
	    		</div>

	    		<!-- CAJA DE TEXTO DE AÑO -->

	    		<div class="col-sm-3">
	    			<div class="form">
    					<label for="" class="mr-1">Año </label>
	      				<input v-bind:class="{ 'is-invalid': validar.año }" v-model="año" type="" class="form-control form-control-sm" id="" v-on:change="diasEnUnMes(mes, año)">
	    			</div>
	    		</div>


	    		<!-- LISTA DE SELECCION DE OPERACION -->

	    		<div class="col-sm">
						<label>Operaciones</label>
		    			<select v-on:change="filtrarCot()" v-bind:class="{ 'is-invalid': validar.operacion }" v-model="operacion" class="custom-select custom-select-sm">
		      				<option> * </option>
		      				<option> / </option>
		    			</select>
		    	</div> 
	  		</div>


	  		<!-- TABLA DE "DIAS" -->


			<div class="mt-3 ml-3">
				
				<h6>Días</h6>


				<!--	PRIMERA FILA --- COLUMNA 1-4 -->

				<div class=" mt-2 row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm"> 1</span>
						  </div>
						  <input v-model="cotizaciones.uno" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoUno(1)">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm"> 2</span>
						  </div>
						  <input v-model="cotizaciones.dos" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDos">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm"> 3</span>
						  </div>
						  <input v-model="cotizaciones.tres" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoTres">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm"> 4</span>
						  </div>
						  <input v-model="cotizaciones.cuatro" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoCuatro">
						</div>
					</div>		
				</div>


				<!-- 	SEGUNDA FILA --- COLUMNA 5-8 -->

				<div class="row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">5</span>
						  </div>
						  <input v-model="cotizaciones.cinco" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoCinco">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">6</span>
						  </div>
						  <input v-model="cotizaciones.seis" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoSeis">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">7</span>
						  </div>
						  <input v-model="cotizaciones.siete" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoSiete">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">8</span>
						  </div>
						  <input v-model="cotizaciones.ocho" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoOcho">
						</div>
					</div>		
				</div>


				<!-- TERCERA FILA --- COLUMNA 9-12 -->

				<div class="row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">9</span>
						  </div>
						  <input v-model="cotizaciones.nueve" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoNueve">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">10</span>
						  </div>
						  <input v-model="cotizaciones.diez" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDiez">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">11</span>
						  </div>
						  <input v-model="cotizaciones.once" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoOnce">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">12</span>
						  </div>
						  <input v-model="cotizaciones.doce" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDoce">
						</div>
					</div>		
				</div>


				<!-- CUARTA FILA --- COLUMNA 13-16 -->

				<div class="row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">13</span>
						  </div>
						  <input v-model="cotizaciones.trece" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoTrece">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">14</span>
						  </div>
						  <input v-model="cotizaciones.catorce" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoCatorce">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">15</span>
						  </div>
						  <input v-model="cotizaciones.quince" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoQuince">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">16</span>
						  </div>
						  <input v-model="cotizaciones.dseis" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDseis">
						</div>
					</div>		
				</div>


				<!-- QUINTA FILA --- COLUMNA 17-20 -->

				<div class="row">
					<div class="col-sm-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">17</span>
						  </div>
						  <input v-model="cotizaciones.dsiete" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDsiete">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">18</span>
						  </div>
						  <input v-model="cotizaciones.docho" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDocho">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">19</span>
						  </div>
						  <input v-model="cotizaciones.dnueve" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoDnueve">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">20</span>
						  </div>
						  <input v-model="cotizaciones.veinte" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVeinte">
						</div>
					</div>		
				</div>



				<!-- SEXTA FILA --- COLUMNA 21-24 -->

				<div class="row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">21</span>
						  </div>
						  <input v-model="cotizaciones.vuno" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVuno">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">22</span>
						  </div>
						  <input v-model="cotizaciones.vdos" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVdos">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">23</span>
						  </div>
						  <input v-model="cotizaciones.vtres" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVtres">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">24</span>
						  </div>
						  <input v-model="cotizaciones.vcuatro" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVcuatro">
						</div>
					</div>		
				</div>


				<!-- SEPTIMA FILA --- COLUMNA 25-28 -->

				<div class="row">
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">25</span>
						  </div>
						  <input v-model="cotizaciones.vcinco" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVcinco">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">26</span>
						  </div>
						  <input v-model="cotizaciones.vseis" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVseis">
						</div>
					</div>	
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">27</span>
						  </div>
						  <input v-model="cotizaciones.vsiete" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVsiete">
						</div>
					</div>
					<div class="col-3">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">28</span>
						  </div>
						  <input v-model="cotizaciones.vocho" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVocho">
						</div>
					</div>		
				</div>


				<!--	OCTAVA FILA --- COLUMNA 29-31 -->

				<div class="row">
					<div class="col-3" v-if="mostrar.vnueve">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">29</span>
						  </div>
						  <input v-model="cotizaciones.vnueve" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoVnueve">
						</div>
					</div>
					<div class="col-3" v-if="mostrar.treinta">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">30</span>
						  </div>
						  <input v-model="cotizaciones.treinta" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoTreinta">
						</div>
					</div>	
					<div class="col-3" v-if="mostrar.tuno">
						<div class="input-group input-group-sm mb-3">
						  <div class="input-group-prepend">
						    <span class="input-group-text" id="inputGroup-sizing-sm">31</span>
						  </div>
						  <input v-model="cotizaciones.tuno" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" v-on:blur="formatoTuno">
						</div>
					</div>		
				</div>


				<!-- FILA DE BOTONES GUARDAR, ACTUALIZAR, ELIMINAR Y LIMPIAR -->

				<div class="mt-3 row">

					<div v-if='guardarbtn' class="col-9 text-right">
			  			<button v-on:click="guardar" type="button" class="btn btn-outline-success" >
			  				<font-awesome-icon icon="save" /> Guardar
			  			</button>
			  		</div>

			  		<div v-else class="col-9 text-right">
			  			<button v-on:click="guardar" type="button" class="btn btn-warning" >
			  				Actualizar
			  			</button>
			  		</div>

			  		<div class="col text-right">
			  			<button type="button" v-on:click="eliminar()" class="btn btn-outline-danger">
			  				<font-awesome-icon icon="trash-alt" /> Eliminar
			  			</button>
			  		</div>

			  		<div class="col text-right">
			  			<button type="button" v-on:click="limpiar()" id="btnlimpiar" class="btn btn-outline-primary">
			  				<font-awesome-icon icon="file" /> Limpiar
			  			</button>
			  		</div>
		  		</div>


	  		</div>
  		</form>
  	</div>
</template>

<script>

    export default {

    	data(){
    		return {

    			moneda: {
    				de: '1',
    				a: '1',
    				decimal: 0
    			},

    			año: 2020,

    			validar:{
    				de: false,
    				a: false,
    				año: false,
    				operacion: false
    			},

    			cotizaciones: {
    				uno: 0,
    				dos: 0,
    				tres: 0,
    				cuatro: 0,
    				cinco: 0,
    				seis: 0,
    				siete: 0,
    				ocho: 0,
    				nueve: 0,
    				diez: 0,
    				once: 0,
    				doce: 0,
    				trece: 0,
    				catorce: 0,
    				quince: 0,
    				dseis: 0,
    				dsiete: 0,
    				docho: 0,
    				dnueve: 0,
    				veinte: 0,
    				vuno: 0,
    				vdos: 0,
    				vtres: 0,
    				vcuatro: 0,
    				vcinco: 0,
    				vseis: 0,
    				vsiete: 0,
    				vocho: 0,
    				vnueve: 0,
    				treinta: 0,
    				tuno:0
    			},

    			mes: 0,

    			operacion: '',

    			mostrar: {
    				
    				vnueve: false,
    				treinta: false,
    				tuno: false
    			},

				guardarbtn: true,    			

    			existe: false
    		}
    	}, 

    	methods: {

    		// ELIMINAR LAS COTIZACIONES DE LA BASE DE DATOS

    		eliminar(){


    			// GUARDAR LOS DATOS EN UNA VARIABLE

    			var eliminar = {

    				demoneda: this.moneda.de,
    				amoneda: this.moneda.a,
    				ano: this.año,
    				mes: this.mes,
    				existe: this.existe
    			}


    			// LLAMAR A UNA FUNCION COMUN PARA ELIMINAR LOS DATOS

    			Common.eliminarCotizacionCommon(eliminar).then(data => {

    				// MENSAJE DE CONFIRMACION O ERROR

    				if(data.response===true){
	                  	Swal.fire(
		                    '¡Eliminado!',
		                    '¡Se ha eliminado correctamente la cotización!',
		                    'success'
	                  	)
	                  	this.limpiar();
	               	}else{
		                Swal.fire(
		                    '¡Error!',
		                    data.statusText,
		                    'warning'
		                )
	               	}

	               	// MENSAJE DE ERROR EN LA FUNCION COMUN

    			}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              });

    		},


    		// AGREGAR LOS DATOS DE LA DB AL FORMULARIO

    		filtrarCot(){


    			// GUARDAR LOS DATOS EN UNA VARIABLE

    			var filtro = {

    				demoneda: this.moneda.de,
    				amoneda: this.moneda.a,
    				ano: this.año,
    				mes: this.mes,
    				operacion: this.operacion

    			}


    			Common.filtrarCotizacionCommon(filtro).then(data => {

    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	               if(data.response===true){


	               		// ENVIAR LOS DATOS A LA TABLA 

	               		this.guardarbtn = false;
	               		this.existe = true;
	               		this.cotizaciones.uno = data.cotizacion[0].CA01;
	               		this.cotizaciones.dos = data.cotizacion[0].CA02;
	               		this.cotizaciones.tres = data.cotizacion[0].CA03;
	               		this.cotizaciones.cuatro = data.cotizacion[0].CA04;
	               		this.cotizaciones.cinco = data.cotizacion[0].CA05;
	               		this.cotizaciones.seis = data.cotizacion[0].CA06;
	               		this.cotizaciones.siete = data.cotizacion[0].CA07;
	               		this.cotizaciones.ocho = data.cotizacion[0].CA08;
	               		this.cotizaciones.nueve = data.cotizacion[0].CA09;
	               		this.cotizaciones.diez = data.cotizacion[0].CA10;
	               		this.cotizaciones.once = data.cotizacion[0].CA11;
	               		this.cotizaciones.doce = data.cotizacion[0].CA12;
	               		this.cotizaciones.trece = data.cotizacion[0].CA13;
	               		this.cotizaciones.catorce = data.cotizacion[0].CA14;
	               		this.cotizaciones.quince = data.cotizacion[0].CA15;
	               		this.cotizaciones.dseis = data.cotizacion[0].CA16;
	               		this.cotizaciones.dsiete = data.cotizacion[0].CA17;
	               		this.cotizaciones.docho = data.cotizacion[0].CA18;
	               		this.cotizaciones.dnueve = data.cotizacion[0].CA19;
	               		this.cotizaciones.veinte = data.cotizacion[0].CA20;
	                  	this.cotizaciones.vuno = data.cotizacion[0].CA21;
	               		this.cotizaciones.vdos = data.cotizacion[0].CA22;
	               		this.cotizaciones.vtres = data.cotizacion[0].CA23;
	               		this.cotizaciones.vcuatro = data.cotizacion[0].CA24;
	               		this.cotizaciones.vcinco = data.cotizacion[0].CA25;
	               		this.cotizaciones.vseis = data.cotizacion[0].CA26;
	               		this.cotizaciones.vsiete = data.cotizacion[0].CA27;
	               		this.cotizaciones.vocho = data.cotizacion[0].CA28;
	               		this.cotizaciones.vnueve = data.cotizacion[0].CA29;
	               		this.cotizaciones.treinta = data.cotizacion[0].CA30;
	               		this.cotizaciones.tuno = data.cotizacion[0].CA31;

	                }else{
	                   	
	                   	// HABILITA EL BOTON GUARDAR
	                   	this.existe = false;
	                   	this.guardarbtn = true;
	          
	               }
           		
           		// MOSTRAR ERROR
           			
           		}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              });

    		},


    		// LIMPIA TODO EL FORMULARIO

    		limpiar(){

    			this.cotizaciones.uno =0;
               	this.cotizaciones.dos = 0;
               	this.cotizaciones.tres = 0;
               	this.cotizaciones.cuatro = 0;
               	this.cotizaciones.cinco = 0;
               	this.cotizaciones.seis = 0;
               	this.cotizaciones.siete = 0;
               	this.cotizaciones.ocho = 0;
               	this.cotizaciones.nueve = 0;
               	this.cotizaciones.diez = 0;
               	this.cotizaciones.once = 0;
               	this.cotizaciones.doce = 0;
               	this.cotizaciones.trece = 0;
               	this.cotizaciones.catorce = 0;
               	this.cotizaciones.quince = 0;
               	this.cotizaciones.dseis = 0;
               	this.cotizaciones.dsiete = 0;
               	this.cotizaciones.docho = 0;
               	this.cotizaciones.dnueve = 0;
               	this.cotizaciones.veinte = 0;
                this.cotizaciones.vuno = 0;
               	this.cotizaciones.vdos = 0;
               	this.cotizaciones.vtres = 0;
               	this.cotizaciones.vcuatro = 0;
               	this.cotizaciones.vcinco = 0;
               	this.cotizaciones.vseis = 0;
               	this.cotizaciones.vsiete = 0;
               	this.cotizaciones.vocho = 0;
               	this.cotizaciones.vnueve = 0;
               	this.cotizaciones.treinta = 0;
               	this.cotizaciones.tuno = 0;
               	this.moneda.de = '1';
               	this.moneda.a = '1';
				this.año = 2020;
				this.mes = 0;
				this.operacion = '';
				this.existe = false;
				this.guardarbtn = true;
				this.moneda.decimal = 0;
				this.validar.de = false;
    			this.validar.a = false;
    			this.validar.año = false;
    			this.validar.operacion = false;
    			this.mostrar.vnueve = false;
    			this.mostrar.treinta = false;
    			this.mostrar.tuno = false;
    		},


    		// CONSEGUIR LA CANTIDAD DE DIAS QUE TIENE UN MES

    		diasEnUnMes(mes, año) {

				var dias = new  Date(año, mes, 0).getDate();

				// HABILITAR EL INPUT 29
				
				if (dias >= 29){

					this.mostrar.vnueve = true;
				}else {

					this.mostrar.vnueve = false;
				}

				// HABILITAR EL INPUT 30
				
				if (dias >= 30){

					this.mostrar.treinta = true;
				}else {
				                                   
					this.mostrar.treinta = false;
				}

				// HABILITAR EL INPUT 29

				if (dias == 31){

					this.mostrar.tuno = true;
				
				}else {

					this.mostrar.tuno = false;
				}
			},
    		
    		descripcionMoneda(valor){

        		// ------------------------------------------------------------------------

        		// DESCRIPCION MONEDA

        		// this.moneda.DESCRIPCION = valor;

        		// ------------------------------------------------------------------------

	        }, 

	        cantidadDecimal(valor){

	        	// DESCRIPCION MONEDA

	        	this.moneda.decimal = valor;
	        	
	        	// ------------------------------------------------------------------------


	        	// LLAMAR A LAS FUNCIONES DE LOS FORMATOS

	        	this.formatoUno(2);
	        	this.formatoDos();
	        	this.formatoTres();
	        	this.formatoCuatro();
	        	this.formatoCinco();
	        	this.formatoSeis();
	        	this.formatoSiete();
	        	this.formatoOcho();
	        	this.formatoNueve();
	        	this.formatoDiez();
	        	this.formatoOnce();
	        	this.formatoDoce();
	        	this.formatoTrece();
	        	this.formatoCatorce();
	        	this.formatoQuince();
	        	this.formatoDseis();
	        	this.formatoDsiete();
	        	this.formatoDocho();
	        	this.formatoDnueve();
	        	this.formatoVeinte();
	        	this.formatoVuno();
	        	this.formatoVdos();
	        	this.formatoVtres();
	        	this.formatoVcuatro();
	        	this.formatoVcinco();
	        	this.formatoVseis();
	        	this.formatoVsiete();
	        	this.formatoVocho();
	        	this.formatoVnueve();
	        	this.formatoTreinta();
	        	this.formatoTuno();

	        	// ------------------------------------------------------------------------

	        },


	        // 		GUARDAR LOS DATOS

    		guardar(){


    			// CONTOLAR QUE NO ESTEN VACIOS

    			if(this.controlador() === false){

    				return;
    			}


    			// CARGAR TODOS LOS DATOS EN UNA VARIABLE

    			var data = {

    				CODMONE: this.moneda.de,
    				CODMONE1: this.moneda.a,
    				ANO: this.año,
    				MES: this.mes,
    				CA01: this.cotizaciones.uno,
    				CA02: this.cotizaciones.dos,
    				CA03: this.cotizaciones.tres,
    				CA04: this.cotizaciones.cuatro,
    				CA05: this.cotizaciones.cinco,
    				CA06: this.cotizaciones.seis,
    				CA07: this.cotizaciones.siete,
    				CA08: this.cotizaciones.ocho,
    				CA09: this.cotizaciones.nueve,
    				CA10: this.cotizaciones.diez,
    				CA11: this.cotizaciones.once,
    				CA12: this.cotizaciones.doce,
    				CA13: this.cotizaciones.trece,
    				CA14: this.cotizaciones.catorce,
    				CA15: this.cotizaciones.quince,
    				CA16: this.cotizaciones.dseis,
    				CA17: this.cotizaciones.dsiete,
    				CA18: this.cotizaciones.docho,
    				CA19: this.cotizaciones.dnueve,
    				CA20: this.cotizaciones.veinte,
    				CA21: this.cotizaciones.vuno,
    				CA22: this.cotizaciones.vdos,
    				CA23: this.cotizaciones.vtres,
    				CA24: this.cotizaciones.vcuatro,
    				CA25: this.cotizaciones.vcinco,
    				CA26: this.cotizaciones.vseis,
    				CA27: this.cotizaciones.vsiete,
    				CA28: this.cotizaciones.vocho,
    				CA29: this.cotizaciones.vnueve,
    				CA30: this.cotizaciones.treinta,
    				CA31: this.cotizaciones.tuno,
    				FORMULA: this.operacion,
    				existe: this.existe
    			}


    			// ENVIAR LOS DATOS DE LA VARIABLE

    			Common.guardarCotizacionCommon(data).then(data => {


    				// CONFIRMAR QUE GUARDÓ O SI HAY UN ERROR

	               if(data.response===true){

	                  Swal.fire(
	                     '¡Guardado!',
	                     '¡Se ha guardado correctamente la cotización!',
	                     'success'
	                  	)
             			this.limpiar();
	               }else{

	                   Swal.fire(
	                     '¡Error!',
	                     data.statusText,
	                     'warning'
	                  )
	               }
           		
           		// MOSTRAR ERROR
           			
           		}).catch((err) => {
	                console.log(err);
	                this.mostrar_error = true;
	                this.mensaje = err;
              });

    		},


    		// PARA VERIFICAR QUE NO ESTEN VACIOS

    		controlador(){


    			if ((this.año).length === 0 || (this.año ===" ")){

    				this.validar.año = true;

    			}else{

    				this.validar.año = false;
    				
    			}


    			if ((this.operacion).length === 0 || (this.operacion ===" ")){

    				this.validar.operacion = true;

    			}else{

    				this.validar.operacion = false;
    			} 

    			if(this.validar.año === true || this.validar.operacion === true){
    				return false;
    			}  			

    		},

    		formatoUno(valor){

            	// REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

            	this.cotizaciones.uno = Common.darFormatoCommon(this.cotizaciones.uno, this.moneda.decimal);

            	// ------------------------------------------------------------------------


      			// SWEET ALERT

      			if (valor === 1){
	      		
	      		Swal.fire({
					title: '¿Desea poner la misma cotización para todos los días?',
					text: "¡Replicar!",
					type: 'info',
					showLoaderOnConfirm: true,
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: '¡Sí, replicarlo!',
					cancelButtonText: 'Cancelar'

				}).then((result) => {

					if (result.value) {

						this.replicar();

					}
				})

				}
			// ------------------------------------------------------------------------
           
        	},


        	// COPIAR EL PRIMER VALOR EN TODOS 

        	replicar(){

        		// ------------------------------------------------------------------------

        		this.cotizaciones.dos = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------

        		this.cotizaciones.tres = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.cuatro = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.cinco = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.seis = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.siete = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.ocho = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.nueve = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.diez = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.once = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.doce = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.trece = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.catorce = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.quince = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.dseis = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.dsiete = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.docho = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.dnueve = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.veinte = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vuno = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vdos = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vtres = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vcuatro = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vcinco = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vseis = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vsiete = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vocho = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.vnueve = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.treinta = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        		this.cotizaciones.tuno = this.cotizaciones.uno;

        		// ------------------------------------------------------------------------
        		
        	},


        	// CAMBIO DE FORMATO

        	formatoDos(){

        		this.cotizaciones.dos = Common.darFormatoCommon(this.cotizaciones.dos, this.moneda.decimal);
        	
        	},

        	formatoTres(){

        		this.cotizaciones.tres = Common.darFormatoCommon(this.cotizaciones.tres, this.moneda.decimal);

        	},

        	formatoCuatro(){

        		this.cotizaciones.cuatro = Common.darFormatoCommon(this.cotizaciones.cuatro, this.moneda.decimal);

        	},

        	formatoCinco(){

            	this.cotizaciones.cinco = Common.darFormatoCommon(this.cotizaciones.cinco, this.moneda.decimal);
           
        	},

        	formatoSeis(){

        		this.cotizaciones.seis = Common.darFormatoCommon(this.cotizaciones.seis, this.moneda.decimal);
        	
        	},

        	formatoSiete(){

        		this.cotizaciones.siete = Common.darFormatoCommon(this.cotizaciones.siete, this.moneda.decimal);

        	},

        	formatoOcho(){

        		this.cotizaciones.ocho = Common.darFormatoCommon(this.cotizaciones.ocho, this.moneda.decimal);

        	},

        	formatoNueve(){

            	this.cotizaciones.nueve = Common.darFormatoCommon(this.cotizaciones.nueve, this.moneda.decimal);
           
        	},

        	formatoDiez(){

        		this.cotizaciones.diez = Common.darFormatoCommon(this.cotizaciones.diez, this.moneda.decimal);
        	
        	},

        	formatoOnce(){

        		this.cotizaciones.once = Common.darFormatoCommon(this.cotizaciones.once, this.moneda.decimal);

        	},

        	formatoDoce(){

        		this.cotizaciones.doce = Common.darFormatoCommon(this.cotizaciones.doce, this.moneda.decimal);

        	},

        	formatoTrece(){

            	this.cotizaciones.trece = Common.darFormatoCommon(this.cotizaciones.trece, this.moneda.decimal);
           
        	},

        	formatoCatorce(){

        		this.cotizaciones.catorce = Common.darFormatoCommon(this.cotizaciones.catorce, this.moneda.decimal);
        	
        	},

        	formatoQuince(){

        		this.cotizaciones.quince = Common.darFormatoCommon(this.cotizaciones.quince, this.moneda.decimal);

        	},

        	formatoDseis(){

        		this.cotizaciones.dseis = Common.darFormatoCommon(this.cotizaciones.dseis, this.moneda.decimal);

        	},

        	formatoDsiete(){

            	this.cotizaciones.dsiete = Common.darFormatoCommon(this.cotizaciones.dsiete, this.moneda.decimal);
           
        	},

        	formatoDocho(){

        		this.cotizaciones.docho = Common.darFormatoCommon(this.cotizaciones.docho, this.moneda.decimal);
        	
        	},

        	formatoDnueve(){

        		this.cotizaciones.dnueve = Common.darFormatoCommon(this.cotizaciones.dnueve, this.moneda.decimal);

        	},

        	formatoVeinte(){

        		this.cotizaciones.veinte = Common.darFormatoCommon(this.cotizaciones.veinte, this.moneda.decimal);

        	},

        	formatoVuno(){

            	this.cotizaciones.vuno = Common.darFormatoCommon(this.cotizaciones.vuno, this.moneda.decimal);
           
        	},

        	formatoVdos(){

        		this.cotizaciones.vdos = Common.darFormatoCommon(this.cotizaciones.vdos, this.moneda.decimal);
        	
        	},

        	formatoVtres(){

        		this.cotizaciones.vtres = Common.darFormatoCommon(this.cotizaciones.vtres, this.moneda.decimal);

        	},

        	formatoVcuatro(){

        		this.cotizaciones.vcuatro = Common.darFormatoCommon(this.cotizaciones.vcuatro, this.moneda.decimal);

        	},

        	formatoVcinco(){

            	this.cotizaciones.vcinco = Common.darFormatoCommon(this.cotizaciones.vcinco, this.moneda.decimal);
           
        	},

        	formatoVseis(){

        		this.cotizaciones.vseis = Common.darFormatoCommon(this.cotizaciones.vseis, this.moneda.decimal);
        	
        	},

        	formatoVsiete(){

        		this.cotizaciones.vsiete = Common.darFormatoCommon(this.cotizaciones.vsiete, this.moneda.decimal);

        	},

        	formatoVocho(){

        		this.cotizaciones.vocho = Common.darFormatoCommon(this.cotizaciones.vocho, this.moneda.decimal);

        	},

        	formatoVnueve(){

            	this.cotizaciones.vnueve = Common.darFormatoCommon(this.cotizaciones.vnueve, this.moneda.decimal);
           
        	},

        	formatoTreinta(){

        		this.cotizaciones.treinta = Common.darFormatoCommon(this.cotizaciones.treinta, this.moneda.decimal);
        	
        	},

        	formatoTuno(){

        		this.cotizaciones.tuno = Common.darFormatoCommon(this.cotizaciones.tuno, this.moneda.decimal);

        	}
    	},

        mounted() {
        }
    }
</script>

<style></style>
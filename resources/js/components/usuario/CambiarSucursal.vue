<template>
	<div id="app">
    	<b-modal v-if="$can('user.cambiarsucursal') && $can('user') && $can('configuracion')" centered id="meu-modal">

	      	<template v-slot:modal-header="{ close }">
	      		<h5>Cambio de Sucursal</h5>
	      		
	      	</template>
	      	
	      	<select-sucursal ref="UsuarioSucursalTextbox" v-model="seleccion_sucursal" v-bind:seleccion="Seleccion_sucursal_modificar"> </select-sucursal>
	      	
	      	<template v-slot:modal-footer="{hide, ok}">
	      		<button type="button" class="btn btn-danger btn-md" v-on:click="hide()">Cancelar</button>
	      				<button type="button" class="btn btn-primary btn-md" v-on:click="okbtn()">Aceptar</button>
	      		
	      	</template>
	    </b-modal>
	    <b-modal v-else>
	      	<cuatrocientos-cuatro></cuatrocientos-cuatro>
	  	</b-modal>



    </div>
</template>
<script>
	export default {
		data(){
			return{
				seleccion_sucursal:[{}],
				Seleccion_sucursal_modificar:[{}],
				cod:''
			}
		},

		methods: {
			okbtn(){
				var data={
					seleccion_sucursal:this.seleccion_sucursal,
					Seleccion_sucursal_modificar:this.Seleccion_sucursal_modificar
				}
				console.log(data);
				Common.cambiarSucursalCommon(data).then(data=>{

					if(data.response===true){

	                  	Swal.fire(
	                     	'¡Actualizado',
	                     	'¡Se ha modificado correctamente la Sucursal!',
	                     	'success'
	                  	)
	                  	 // *******************************************************************
	                  	 window.location.href='/home';
	                }else{

	                   	Swal.fire(
	                     	'¡Error!',
	                     	data.statusText,
	                     	'warning'
	                    )
	                }
				}).catch((err) => {
	                console.log(err);
             	});
			}
		}
	}
</script>

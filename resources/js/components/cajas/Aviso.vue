<template>

  	<div class="container">
    
    	<!-- ------------------------------------------------------------------------ -->

    	<div class="modal bg-info fade" id="avisoModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="staticBackdropLabel"><b>AVISO  </b></h5><span class="text-right">{{aviso.version}}</span>
		      </div>
		      <div class="modal-body">
		      	<div class="container">
		      	<div class="row">
		      			<span v-html="aviso.errores"></span>
		        </div>
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" v-on:click="confirmarAviso">Aceptar</button>
		      </div>
		    </div>
		  </div>
		</div>

    	<!-- ------------------------------------------------------------------------ -->

  	</div>

</template>
<script>
    export default {
     
      data(){
        return {
        	aviso: {
        		tipo: 0,
        		errores: '',
        		funciones: '',
        		informacion: '',
        		codigo: '',
        		version: ''
        	}
        }
      }, 
      methods: {

      	confirmarAviso(){

      		// ------------------------------------------------------------------------

      		Common.confirmarAvisoDiaCommon(this.aviso.codigo).then(data => {

      		})

      		$('#avisoModal').modal('hide');

      		// ------------------------------------------------------------------------

      	}

      },  
        mounted() {

          // ------------------------------------------------------------------------
          	
          let me = this;

          // ------------------------------------------------------------------------

		  Common.obtenerAvisoDiaCommon().then(data => {
		  		 me.aviso.errores = data.aviso[0].ERRORES;
		  		 me.aviso.funciones = data.aviso[0].FUNCIONES;
		  		 me.aviso.informacion = data.aviso[0].ERRORES;
		  		 me.aviso.codigo = data.aviso[0].CODIGO;
		  		 me.aviso.version = data.aviso[0].VERSION;

		  		 if (data.activo === 0) {
		  		 	$('#avisoModal').modal('show');
		  		 }
				    	
		  }).catch(error => {
			Swal.showValidationMessage(
				`Request failed: ${error}`
			)
		  });

		  // ------------------------------------------------------------------------
		

		}
          
    }

</script>
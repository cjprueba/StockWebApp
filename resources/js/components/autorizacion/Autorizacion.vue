<template>
	<div>
		<div class="container-fluid">
		  <div class="modal fade bg-primary" id="modalAutorizacion" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog modal-lg " role="document">
			    <div class="modal-content">
			      <div class="modal-header text-center">
			        <h5 class="modal-title text-primary" id="exampleModalLabel"><small>AUTORIZACIÓN</small></h5><h5 class="modal-title text-primary" id="exampleModalLabel"><font-awesome-icon size="2x" icon="barcode" /></h5>
			      </div>
			      <div class="modal-body">
			      	<label>{{validar.informacion}}</label>
			        <input :tabindex="100" id="codigo_autorizacion" type="password" v-bind:class="{ 'is-invalid': validar.autorizacion }" v-model="autorizacion.CODIGO" class="form-control form-control-xl" name="" v-on:keyup.13="revisar" autofocus>
			      </div>
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
          codigo_compra: '',
          autorizacion: {
          	CODIGO: '',
          	ID_USUARIO: ''
          },
          validar: {
          	informacion: '',
          	autorizacion: false
          }
        }
      }, 
      methods: {
      		mostrarModal(){

      			// ------------------------------------------------------------------------

      			// LLAMAR MODAL TRANSFERENCIA PRODUCTOS

      			$('#modalAutorizacion').modal('show');

      			// ------------------------------------------------------------------------
            	
            }, 
            enviarOpcionesPadre(data){

            	// ------------------------------------------------------------------------

              	this.$emit('data', data);

              	// ------------------------------------------------------------------------

            },
            revisar(){

            	// ------------------------------------------------------------------------

            	let me = this;

            	// ------------------------------------------------------------------------

            	// OBTENER DATOS DE LA CLAVE 

            	Common.obtenerAutorizacion(me.autorizacion.CODIGO).then(data => {

            		if (data.response === false) {

            			me.validar.autorizacion = true;
                  me.autorizacion.CODIGO = '';
            			me.validar.informacion = data.statusText;

            			me.enviarOpcionesPadre({response: false});

            		} else if (data.response === true) {
                  
                  me.autorizacion.CODIGO = '';
            			me.enviarOpcionesPadre({id_user_supervisor: data.autorizacion[0].ID, usuario: data.autorizacion[0].FK_USER, response: true});
            			$('#modalAutorizacion').modal('hide');
            		}

            	});

            	// ------------------------------------------------------------------------

            }, enfocar(){

				// ------------------------------------------------------------------------

				// VACIAR Y DEVOLVER FOCUS AL TEXTBOX

	        	//$("#codigo_autorizacion").focus();
				//this.$refs.autorizacion.CODIGO.focus()
				$(':input[tabindex=100]').focus();

				// ------------------------------------------------------------------------

			}
      },
        mounted() {

        	// ------------------------------------------------------------------------

            let me = this;

          // ------------------------------------------------------------------------

	 		    // FOCUS EN SEARCH DEL DATATABLE DESPUES DE ABRIR EL MODAL 
            
            $('#modalAutorizacion').on('shown.bs.modal', function() {
              $('#codigo_autorizacion').focus();
            })

            // ------------------------------------------------------------------------

    		// ------------------------------------------------------------------------

        }
    }
</script>
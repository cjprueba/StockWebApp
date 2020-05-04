<template>
	<div>
            
              <label for="validationTooltip01">Marca</label>
              <div class="row">

                <div class="col-md-3">
                  
                  <div class="input-group" id="validationTooltip01">
                    
                    <div class="input-group-prepend">
                      <button :disabled="deshabilitar" type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target=".marca-search-modal"><font-awesome-icon icon="search"/></button>
                    </div>

                    <input :tabindex="tabIndexPadre" :disabled="deshabilitar" ref="codigo" id="marca_search" class="custom-select custom-select-sm shadow-sm" type="text"  :value="value" @input="$emit('input', $event.target.value)" v-bind:class="{ 'is-invalid': validar_marca }"  v-on:keyup.prevent.13="llamarPadre($event.target.value)" v-on:blur="llamarPadre($event.target.value)">

                    
                  </div>
                </div>

                <div class="col-md-9">
                  <input type="text" v-model="marca.DESCRIPCION" name="marcaTextbox" class="form-control form-control-sm">
                </div>  
              
              </div>
           <!--  <select :tabindex="tabIndexPadre" class="custom-select custom-select-sm" v-on:change="llamarPadre($event.target.value)" v-bind:class="{ 'shadow-sm': shadow, 'is-invalid': validar_marca }" @input="$emit('input', $event.target.value)" :disabled="deshabilitar">
                    <option :value="null">0 - Seleccionar</option>
                    <option v-for="marca in marcas" :selected="marca.CODIGO === parseInt(value)" :value="marca.CODIGO">{{ marca.CODIGO }} - {{ marca.DESCRIPCION }}</option>
            </select> -->

          <!-- ------------------------------------------------------------------------ -->

          <!-- MODAL PRODUCTOS -->

                  <div class="modal fade marca-search-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalCenterTitle">Productos: </small></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                              <table id="tablaModalProductos" class="table table-hover table-bordered table-sm mb-3" style="width:100%">
                                  <thead>
                                      <tr>
                                          <th>Codigo</th>
                                          <th>Descripcion</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <td></td>
                                  </tbody>
                              </table>        
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>  

          <!-- ------------------------------------------------------------------------ -->
			
	</div>	
</template>
<script>
	export default {
      props: [
              'value',
              'shadow',
              'validar_marca',
              'categoria',
              'deshabilitar',
              'tabIndexPadre'
            ],
      data(){
        return {
            marca: {
                CODIGO: '',
                DESCRIPCION: ''
            }
        }
      },
      watch: { 
        categoria: function(newVal, oldVal) { 
            this.obtenerMarca(newVal, this.value);
        }
      }, 
      methods: {
            obtenerMarca(categoria, marca){

      				// ------------------------------------------------------------------------
              
              let me = this;

      				// LLAMAR FUNCION PARA FILTRAR PRODUCTOS

              if(marca === undefined) {
                 marca = this.value;
              }

      				Common.obtenerMarcaCategoriaCommon(categoria, marca).then(data => {
                if (data !== 0) {
                  me.marca.CODIGO = data.CODIGO;
                  me.marca.DESCRIPCION = data.DESCRIPCION;
                  me.$emit('marcaDescripcion', data);
                  me.reAplicar(data.DESCRIPCION);
                }
      				  
      				});

      				// ------------------------------------------------------------------------

      			}, llamarPadre(valor){

              // ------------------------------------------------------------------------

              // ENVIAR DESCRIPCION TELA A PADRE
              
              this.obtenerMarca(this.categoria, valor);

              // ------------------------------------------------------------------------

            }, reAplicar(valor){

              this.marca.DESCRIPCION = valor;

            }
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;

        	// ------------------------------------------------------------------------
        	
        	 
        }
    }
</script>
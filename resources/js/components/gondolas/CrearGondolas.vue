
<template>
	<div class="container mt-3">
    <div v-if="$can('gondola.crear') && $can('gondola')">
    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->     
      <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>
          <div class="col-6">
            <div class="card shadow border-bottom-primary mb-3">
              <h5 class="text-center card-header">Gondolas</h5>
              <div class="card-body">            
                <div class="row">
                  <div class="col-12">
                    <div class="mb-3">
                      <gondola-nombre ref="componente_textbox_Gondola" @nombre_gondola='enviar_nombre' @existe_gondola='existe' :nombre='nombreGondola' :validarGondola='validarGondola' @seccion="enviar_seccion" @id='enviar_id' @descripcion='traer_descripcion'></gondola-nombre>
                    </div>

                    <div class="invalid-feedback">{{messageInvalidSeccion}}</div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Descripción</label>
                      <input v-model="descripcionTela" type="text" class="form-control form-control-sm" id="exampleFormControlTextarea1" v-bind:class="{ 'is-invalid': validarDescripcion}">
                    </div>


                    <div class="mb-3">
                      <label for="validationTooltip01">Seleccionar Sección</label>
                      <select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
                        <option value="null" selected>Seleccionar</option>
                        <option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
                      </select>
                    </div>


                      <div class="row">
                        <div class="col" align="left">
                          <button v-on:click="nuevaGondola" type="submit" class="btn btn-primary">Nuevo (F2)</button>
                        </div>
                        <div class="col" align="center">
                          <div v-if='guardar'>
                          <button v-on:click="guardarGondola" type="submit" class="btn btn-success" >Guardar (F3)</button>
                        </div>
                        <div v-else>
                          <button v-on:click="guardarGondola" type="submit" class="btn btn-warning">Actualizar (F3)</button>
                        </div>
                        </div>
                        <div class="col" align="right">
                          <button v-on:click="eliminarGondola" type="submit" class="btn btn-danger">Eliminar (F6)</button>
                        </div>
                             
                      </div>
                  </div>   
              </div>
            </div>
          </div>
        </div>
    </div>
    <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>
  </div>
</template>
<script>
	 export default {
      props: ['moneda'],
      data(){
        return {
          menu : 0,
          messageInvalidFecha: '',
          idPermiso:"",
          switch_descuento:false,
          validarGondola:false,
          validarDescuento:false,
          descuento:"",
          existeTela:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          selectedInicialFecha: '',
          validarInicialFecha: false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreGondola:"",
          validarCheck:false,
          descripcionTela:"",
          deshabilitar:false,
          validarSeccion: false,
          secciones: [],
          messageInvalidSeccion: '',
          selectedSeccion:"null"
        }
      }, 
      methods: {
          existe(data){

          this.existeTela=data;
           if(data===false){
             this.guardar=true;
             this.descripcionTela="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionTela=data;

        },
        nuevaGondola(){
          let me=this;
        Common.nuevaGondolaCommon().then(data => {
              me.nombreGondola =data.Gondola[0].CODIGO+1;

              me.guardar = true;    
              });
              me.limpiar();
        },
         traer_descuento(data){
         this.descuento=data;
        },


          traer_fechaini(data){
         this.selectedInicialFecha=data;
        },
        traer_fechafin(data){
         this.selectedFinalFecha=data;
        },
        enviar_nombre(data){

          this.nombreGondola=data;
           
        },
        enviar_id(id){
          
          this.idPermiso=id;
        },
        enviar_seccion(data){

          this.selectedSeccion=data;
        },
        limpiar(){
         
          let me =this;
          me.descripcionTela="";
          me.selectedSeccion="null";
          me.$refs.componente_textbox_Gondola.recargar();
          me.guardar= true;
        },

        timepicker(){
          let me=this;
           $(function(){
              $('#sandbox-container .input-daterange').datepicker({
                    keyboardNavigation: false,
                forceParse: false, format:"yyyy-mm-dd"
            });
            $("#selectedInicialFecha").datepicker().on(
              "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
          );
          $("#selectedFinalFecha").datepicker().on(
              "changeDate", () => {me.selectedFinalFecha = $('#selectedFinalFecha').val()}
          );

          $('table').dataTable();
          });
         },
      	guardarGondola(){

             let me =this;
             if(me.nombreGondola===""){
               
              me.validarGondola=true;
              return;
             } else {
              me.validarGondola = false;
             
             } 
             if(me.descripcionTela===""){
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 

             var data = {
              Codigo:me.nombreGondola,
              Descripcion:me.descripcionTela,
              SeccionGuardar:me.selectedSeccion,
              Existe:me.existeTela

             }

              Common.guardarGondolaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la gondola!!!',
                     'success'
                  )
                  window.location.href='/gond1';
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
              me.nuevaGondola();
              me.$refs.componente_textbox_Gondola.recargar();
      	},
                eliminarGondola(){

             let me =this;
             if(me.nombreGondola===""){
               
              me.validarGondola=true;
              return;
             } else {
              me.validarGondola = false;
             
             } 

             var data = {
              Codigo:me.nombreGondola,
              Existe:me.existeTela,
             }

              Common.eliminarGondolaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la Gondola!!!',
                     'success'
                  )
                  window.location.href='/gond1';
               }else{
                    Swal.fire(
                     'Error!',
                     data.statusText,
                     'warning'
                  )
               }
             
                
              }).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
              
              me.nuevaGondola();
              me.$refs.componente_textbox_Gondola.recargar();
        },
       Accesos(){
        let me =this;
          
         if(me.radioPermisos==="1"){

              me.deshabilitar=true;
              me.permisos.map(function(x) {
                 me.permisosSelected.push(x.id);
              });


         }else{

          if(me.radioPermisos==="2"){
             me.permisosSelected=[];
             me.deshabilitar=true;
          }else{ 

            me.deshabilitar=false;

          }

         }
          
        },
      BusquedaSeccion(){
        axios.get('busquedas/').then((response) => {
          this.secciones = response.data.seccion;

        });
      }
         
       },
        mounted() {

          let me=this;
          this.BusquedaSeccion();
          this.nuevaGondola();
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevaGondola();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarGondola();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarGondola();
});

        }
    }
</script>
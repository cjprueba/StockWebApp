
<template>
	
<div class="container">

  <div v-if="$can('color.crear') && $can('atributos')">
  
      <!-- ------------------------------------------------------------------ -->

      <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
          
      <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

      <!-- ------------------------------------------------------------------------------------- -->
          
         <div class="offset-md-2 col-6">
        <div class="card mt-3 shadow-sm">
          <h5 class="card-header">Colores</h5>
          <div class="card-body">
           
            <div class="row">

              <div class="col-12">
                <color-nombre ref="componente_textbox_Color" @nombre_marca='enviar_nombre' @existe_color='existe' :nombre='nombreColor' :validarColor='validarColor' @id='enviar_id' @descripcion='traer_descripcion'></color-nombre>
              </div>   
                <div class="col-12">

                     <hr>

                       <div class="form-group">
                            <label for="exampleFormControlTextarea1">Descripcion</label>
                        <textarea v-model="descripcionColor" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                        </div>
                </div>
                    <hr>
                      <div  class="col-3 mt-3 text-right">
                         <button v-on:click="nuevoColor" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                      </div>

                     <div class="col-4">
                       
                        
                      <div v-if='guardar' class="col-3 mt-3 text-right">
                         <button v-on:click="guardarColor" type="submit" class="btn btn-primary">Guardar(F3)</button>
                      </div>
                       <div v-else class="col-3 mt-3 text-right">
                         <button v-on:click="guardarColor" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                      </div>
                  

                 </div>
                        <div  class="col-3 mt-3 text-right">
                         <button v-on:click="eliminarColor" type="submit" class="btn btn-danger">Eliminar(F6)</button>
                      </div>

        </div>
      </div>
    </div>
         </div>

  </div>

  <!-- ------------------------------------------------------------------------ -->

  <div v-else>
    <cuatrocientos-cuatro></cuatrocientos-cuatro>
  </div>

  <!-- ------------------------------------------------------------------------ -->
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
          validarColor:false,
          validarDescuento:false,
          descuento:"",
          existeColor:false,
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
          nombreColor:"",
          validarCheck:false,
          descripcionColor:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeColor=data;
           if(data===false){
             this.guardar=true;
             this.descripcionColor="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionColor=data;

        },
        nuevoColor(){
          let me=this;
        Common.nuevoColorCommon().then(data => {
              me.nombreColor =data.colores[0].CODIGO+1;
              me.guardar = true;    
              });

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

          this.nombreColor=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
      	llamarRoles(){
      		let me =this;
              Common.llamarRolesCommon().then(data => {
              me.permisos=data;
              me.mostrarPermisos= true;
           
           			
           		});
      	},
        limpiar(){
         
          let me =this;
          me.nombreColor="";
          me.descripcionColor="";
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
      	guardarColor(){

             let me =this;
             if(me.nombreColor===""){
               
              me.validarColor=true;
              return;
             } else {
              me.validarColor = false;
             
             } 
             if(me.descripcionColor===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 

             var data = {
              Codigo:me.nombreColor,
              Descripcion:me.descripcionColor,
              Existe:me.existeColor,
             }

              Common.guardarColorCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el Color!!!',
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
                eliminarColor(){

             let me =this;
             if(me.nombreColor===""){
               
              me.validarColor=true;
              return;
             } else {
              me.validarColor = false;
             
             } 

             var data = {
              Codigo:me.nombreColor,
              Existe:me.existeColor,
             }

              Common.eliminarColorCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente el color!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de Color no existe o ya contiene productos registrados!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Color.recargar();
                
              }).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
              
              me.limpiar();
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
          
        }
         
       },
        mounted() {

          let me=this;
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevoColor();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarColor();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarColor();
});

        }
    }
</script>
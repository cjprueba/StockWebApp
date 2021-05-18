
<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div v-if="$can('proveedor.crear') && $can('proveedor')" class="offset-md-2 col-6">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Proveedores</h5>
      <div class="card-body">
       
        <div class="row">

          <div class="col-12">
            <proveedor-nombre ref="componente_textbox_Proveedor" @nombre_proveedor='enviar_nombre' @existe_proveedor='existe' :nombre='nombreProveedor' :validarProveedor='validarProveedor' @id='enviar_id' @descripcion='traer_descripcion' @ruc='traer_ruc' @direccion='traer_direccion' @telefono='traer_telefono' @celular='traer_celular' @contacto='traer_contacto' @email='traer_email' :tabIndexPadre=-1></proveedor-nombre>
          </div>   
            <div class="col-12">

                 <hr>

                   <div class="form-group">
                        <label for="exampleFormControlTextarea1">Nombre del Proveedor:</label>
                    <textarea v-model="descripcionProveedor"  class="form-control"  id="exampleFormControlTextarea1" rows="3" :tabindex=0 v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">R.U.C:</label>
                      <input v-model="ruc" type="text" class="form-control" id="formGroupExampleInput1 " :tabindex=1 v-bind:class="{ 'is-invalid': validarRuc }"  >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Direccion:</label>
                      <input v-model="Direccion" type="text" class="form-control" id="formGroupExampleInput2" :tabindex=2 v-bind:class="{ 'is-invalid': validarDireccion }" >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Telefono:</label>
                      <input v-model="telefono" type="text" class="form-control" id="formGroupExampleInput3" :tabindex=3 v-bind:class="{ 'is-invalid': validarTelefono }" >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Celular:</label>
                      <input v-model="cel" type="text" class="form-control" id="formGroupExampleInput4" :tabindex=4 placeholder="OPCIONAL" >
                    </div>
                 <hr>
                   <div class="form-group">
                     <label for="exampleFormControlInput1">Email </label>
                     <input v-model="email" type="email" class="form-control" id="exampleFormControlInput1" :tabindex=5 placeholder="opcional@example.com">
                   </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Nombre del contacto:</label>
                      <input v-model="contacto" type="text" class="form-control" id="formGroupExampleInput3" :tabindex=6 placeholder="OPCIONAL" >
                    </div>
            </div>

                <hr>
                  <div  class="col-3 mt-3 text-right">
                     <button v-on:click="nuevoProveedor" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                  </div>

                 <div class="col-4">
                   
                    
                  <div v-if='guardar' class="col-3 mt-3 text-right">
                     <button v-on:click="guardarProveedor" type="submit" class="btn btn-primary">Guardar(F3)</button>
                  </div>
                   <div v-else class="col-3 mt-3 text-right">
                     <button v-on:click="guardarProveedor" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                  </div>
              

             </div>
                    <div  class="col-3 mt-3 text-right">
                     <button v-on:click="eliminarProveedor" type="submit" class="btn btn-danger">Eliminar(F6)</button>
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
          validarProveedor:false,
          validarDescuento:false,
          descuento:"",
          existeProveedor:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          validarRuc:false,
          selectedInicialFecha: '',
          validarTelefono: false,
          guardar:true,
          ruc:"",
          Direccion:"",
          cb:"",
          telefono:"",
          cel:"",
          currentTabindex:"",
          nextInput:"",
          email:"",
          contacto:"",
          radioPermisos:3,
          validarDescripcion:false,
          validarDireccion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreProveedor:"",
          validarCheck:false,
          descripcionProveedor:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeProveedor=data;
           if(data===false){
             this.guardar=true;
             this.descripcionProveedor="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionProveedor=data;

        },
        traer_ruc(data){
          
         this.ruc=data;

        },
        traer_direccion(data){
          
         this.Direccion=data;

        },
          traer_telefono(data){
          
         this.telefono=data;

        },
          traer_celular(data){
          
         this.cel=data;

        },
        traer_contacto(data){
          
         this.contacto=data;

        },
          traer_email(data){
          
         this.email=data;

        },

        nuevoProveedor(){
          let me=this;
        Common.nuevoProveedorCommon().then(data => {

              me.nombreProveedor =data.proveedores[0].CODIGO;
              me.guardar = true;    
              });
         me.limpiar();
        },



        enviar_nombre(data){

          this.nombreProveedor=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreProveedor="";
          me.descripcionProveedor="";
          me.ruc="";
          me.Direccion="";
          me.telefono="";
          me.cel="";
          me.email="";
          me.contacto="";

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
      	guardarProveedor(){

             let me =this;
             if(me.nombreProveedor===""){
               
              me.validarProveedor=true;
              return;
             } else {
              me.validarProveedor = false;
             
             } 
             if(me.descripcionProveedor===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             
             if(me.ruc==="" || me.ruc===null){
             
              me.validarRuc=true;
              return;
             }else{
              me.validarRuc=false;
             }
              if(me.Direccion==="" || me.Direccion===null){
                me.validarDireccion=true;
                return;
              }else{
                me.validarDireccion=false;
              }
              if(me.telefono==="" || me.telefono===null){
                me.validarTelefono=true;
                return;
              }else{
                me.validarTelefono=false;
              }
             var data = {
              Codigo:me.nombreProveedor,
              Descripcion:me.descripcionProveedor,
              Ruc:me.ruc,
              Direccion:me.Direccion,
              Telefono:me.telefono,
              cel:me.cel,
              contacto:me.contacto,
              email:me.email,
              Existe:me.existeProveedor,
             }

              Common.guardarProveedorCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el Proveedor!!!',
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
                this.mostrar_error = true;
                this.mensaje = err;
              });
              me.limpiar();
      	},
                eliminarProveedor(){

             let me =this;
             if(me.nombreProveedor===""){
               
              me.validarProveedor=true;
              return;
             } else {
              me.validarProveedor = false;
             
             } 

             var data = {
              Codigo:me.nombreProveedor,
              Existe:me.existeProveedor,
             }

              Common.eliminarProveedorCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente el Proveedor!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de proveedor no existe o ya contiene productos registrados!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Proveedor.recargar();
                
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
          me.nuevoProveedor();
$(document).on('keypress', 'input', function(e) {

  if(e.keyCode == 13 ) {
               me.cb = parseInt($(this).attr('tabindex'));
           if ($(':input[tabindex=\'' + (me.cb + 1) + '\']') != null  ) {
            
               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
               e.preventDefault();
    
               return false;
           }
  }

});
$(document).on('keypress', 'textarea', function(e) {

  if(e.keyCode == 13 ) {
               me.cb = parseInt($(this).attr('tabindex'));
            
               $(':input[tabindex=\'' + (me.cb + 1) + '\']').focus();
               $(':input[tabindex=\'' + (me.cb + 1) + '\']').select();
               e.preventDefault();
    
               return false;
           
  }

});
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevoProveedor();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarProveedor();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarProveedor();
});

        }
    }
</script>
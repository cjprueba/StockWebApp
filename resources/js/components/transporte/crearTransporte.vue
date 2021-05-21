
<template>
	
<div v-if="$can('transporte.crear') && $can('transporte')" class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div class="offset-md-2 col-6">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Transportadora</h5>
      <div class="card-body">
       
        <div class="row">

          <div class="col-12">
            <transporte-nombre ref="componente_textbox_Transporte" @nombre_transporte='enviar_nombre' @existe_transporte='existe' :nombre='nombreTransporte' :validarTransporte='validarTransporte' @id='enviar_id' @descripcion='traer_descripcion' @ruc='traer_ruc' @direccion='traer_direccion' @telefono='traer_telefono' @celular='traer_celular' @email='traer_email' ></transporte-nombre>
          </div>   
            <div class="col-12">

                 <hr>

                   <div class="form-group">
                        <label for="exampleFormControlTextarea1">Nombre de la transportadora:</label>
                    <textarea v-model="descripcionTransporte" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">R.U.C:</label>
                      <input v-model="ruc" type="text" class="form-control" id="formGroupExampleInput1 " v-bind:class="{ 'is-invalid': validarRuc }"  >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Direccion:</label>
                      <input v-model="Direccion" type="text" class="form-control" id="formGroupExampleInput2" v-bind:class="{ 'is-invalid': validarDireccion }" >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Telefono:</label>
                      <input v-model="telefono" type="text" class="form-control" id="formGroupExampleInput3" v-bind:class="{ 'is-invalid': validarTelefono }" >
                    </div>
                  <hr>
                    <div class="form-group">
                      <label for="formGroupExampleInput">Celular:</label>
                      <input v-model="cel" type="text" class="form-control" id="formGroupExampleInput4" placeholder="OPCIONAL" >
                    </div>
                 <hr>
                   <div class="form-group">
                     <label for="exampleFormControlInput1">Email </label>
                     <input v-model="email" type="email" class="form-control" id="exampleFormControlInput1" placeholder="opcional@example.com">
                   </div>

            </div>

                <hr>
                  <div  class="col-3 mt-3 text-right">
                     <button v-on:click="nuevoTransporte" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                  </div>

                 <div class="col-4">
                   
                    
                  <div v-if='guardar' class="col-3 mt-3 text-right">
                     <button v-on:click="guardarTransporte" type="submit" class="btn btn-primary">Guardar(F3)</button>
                  </div>
                   <div v-else class="col-3 mt-3 text-right">
                     <button v-on:click="guardarTransporte" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                  </div>
              

             </div>
                    <div  class="col-3 mt-3 text-right">
                     <button v-on:click="eliminarTransporte" type="submit" class="btn btn-danger">Eliminar(F6)</button>
                  </div>

    </div>
  </div>
</div>
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
          validarTransporte:false,
          validarDescuento:false,
          descuento:"",
          existeTransporte:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          validarRuc:false,
          selectedInicialFecha: '',
          validarTelefono: false,
          guardar:true,
          ruc:"",
          Direccion:"",
          telefono:"",
          cel:"",
          email:"",
          contacto:"",
          radioPermisos:3,
          validarDescripcion:false,
          validarDireccion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreTransporte:"",
          validarCheck:false,
          descripcionTransporte:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeTransporte=data;
           if(data===false){
             this.guardar=true;
             this.descripcionTransporte="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionTransporte=data;

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
        traer_email(data){
          
         this.email=data;

        },

        nuevoTransporte(){
          let me=this;
        Common.nuevoTransporteCommon().then(data => {
              me.nombreTransporte =data.transporte[0].CODIGO+1;
              me.guardar = true;    
              });
         me.limpiar();
        },



        enviar_nombre(data){

          this.nombreTransporte=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreTransporte="";
          me.descripcionTransporte="";
          me.ruc="";
          me.Direccion="";
          me.telefono="";
          me.cel="";
          me.email="";
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
      	guardarTransporte(){

             let me =this;
             if(me.nombreTransporte===""){
               
              me.validarTransporte=true;
              return;
             } else {
              me.validarTransporte = false;
             
             } 
             if(me.descripcionTransporte===""){
              
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
              Codigo:me.nombreTransporte,
              Descripcion:me.descripcionTransporte,
              Ruc:me.ruc,
              Direccion:me.Direccion,
              Telefono:me.telefono,
              cel:me.cel,
              email:me.email,
              Existe:me.existeTransporte,
             }

              Common.guardarTransporteCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la Transportadora!!!',
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
              me.$refs.componente_textbox_Transporte.recargar();
      	},
                eliminarTransporte(){

             let me =this;
             if(me.nombreTransporte===""){
               
              me.validarTransporte=true;
              return;
             } else {
              me.validarTransporte = false;
             
             } 

             var data = {
              Codigo:me.nombreTransporte,
              Existe:me.existeTransporte,
             }

              Common.eliminarTransporteCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la Transportadora!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de transportadora no existe o ya contiene transferencias registradas!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Transporte.recargar();
                
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
   me.nuevoTransporte();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarTransporte();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarTransporte();
});

        }
    }
</script>
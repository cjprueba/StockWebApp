
<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div class="offset-md-2 col-6">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Container</h5>
      <div class="card-body">
       
        <div class="row">

          <div class="col-12">
            <container-nombre ref="componente_textbox_Container" @nombre_container='enviar_nombre' @existe_container='existe' :nombre='nombreContainer' :validarContainer='validarContainer' @id='enviar_id' @descripcion='traer_descripcion' @fecha_inicio='traer_inicio'  ></container-nombre>
          </div>   
            <div class="col-12">

                 <hr>

                   <div class="form-group">
                        <label for="exampleFormControlTextarea1">Descripcion:</label>
                    <textarea v-model="descripcionContainer" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                    </div>
                  <hr>
            <label class="mt-3" for="validationTooltip01">Fecha de inicio:</label>
            <div id="sandbox-container">
              <div class="input-daterange input-group">
                   <input type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" data-date-format="yyyy-mm-dd" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
                   <div class="input-group-append form-control-sm">
                   </div>
                 
              </div>

            </div>    


            </div>

                <hr>
                  <div  class="col-3 mt-3 text-right">
                     <button v-on:click="nuevoContainer" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                  </div>

                 <div class="col-4">
                   
                    
                  <div v-if='guardar' class="col-3 mt-3 text-right">
                     <button v-on:click="guardarContainer" type="submit" class="btn btn-primary">Guardar(F3)</button>
                  </div>
                   <div v-else class="col-3 mt-3 text-right">
                     <button v-on:click="guardarContainer" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                  </div>
              

             </div>
                    <div  class="col-3 mt-3 text-right">
                     <button v-on:click="eliminarContainer" type="submit" class="btn btn-danger">Eliminar(F6)</button>
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
          validarContainer:false,
          validarDescuento:false,
          descuento:"",
          existe_container:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          validarRuc:false,
          selectedInicialFecha: '',
          validarInicialFecha: false,
          validarTelefono: false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          validarDireccion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreContainer:"",
          validarCheck:false,
          descripcionContainer:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existe_container=data;
           if(data===false){
             this.guardar=true;
             this.descripcionContainer="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionContainer=data;

        },
        traer_inicio(data){
          
         this.selectedInicialFecha=data;

        },

        nuevoContainer(){
          let me=this;
        Common.nuevoContainerCommon().then(data => {
              me.nombreContainer =data.container[0].CODIGO+1;
              me.guardar = true;    
              });
         me.limpiar();
        },



        enviar_nombre(data){

          this.nombreContainer=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreContainer="";
          me.descripcionContainer="";
          me.selectedInicialFecha="";

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
      	guardarContainer(){

             let me =this;
             if(me.nombreContainer==="" || me.nuevoContainer===null){
               
              me.validarContainer=true;
              return;
             } else {
              me.validarContainer = false;
             
             } 
             if(me.descripcionContainer==="" || me.descripcionContainer===null){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.selectedInicialFecha==="" || me.selectedInicialFecha===null){
              
              me.validarInicialFecha=true;
              return;
             } else {
              me.validarInicialFecha = false;
             } 
             


             var data = {
              Codigo:me.nombreContainer,
              Descripcion:me.descripcionContainer,
              fecha_inicio:me.selectedInicialFecha,
              Existe:me.existe_container,
             }

              Common.guardarContainerCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el Container!!!',
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
              me.$refs.componente_textbox_Container.recargar();
              me.limpiar();
      	},
                eliminarContainer(){

             let me =this;
             if(me.nombreContainer===""){
               
              me.validarContainer=true;
              return;
             } else {
              me.validarContainer = false;
             
             } 

             var data = {
              Codigo:me.nombreContainer,
              Existe:me.existe_container,
             }

              Common.eliminarContainerCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente El Container!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de container no existe o ya contiene compras registradas!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Container.recargar();
                
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
          $(function(){
              $('#sandbox-container .input-daterange').datepicker({
                    keyboardNavigation: false,
                forceParse: false
            });
            $("#selectedInicialFecha").datepicker().on(
              "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
          );

      });

          let me=this;
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevoContainer();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarContainer();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarContainer();
});

        }
    }
</script>

<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div class="offset-md-2 col-6">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Telas</h5>
      <div class="card-body">
       
        <div class="row">

          <div class="col-12">
            <tela-nombre ref="componente_textbox_Tela" @nombre_tela='enviar_nombre' @existe_tela='existe' :nombre='nombreTela' :validarTela='validarTela' @id='enviar_id' @descripcion='traer_descripcion'></tela-nombre>
          </div>   
            <div class="col-12">

                 <hr>

                   <div class="form-group">
                        <label for="exampleFormControlTextarea1">Descripcion</label>
                    <textarea v-model="descripcionTela" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                    </div>
            </div>
                <hr>
                  <div  class="col-3 mt-3 text-right">
                     <button v-on:click="nuevaTela" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                  </div>

                 <div class="col-4">
                   
                    
                  <div v-if='guardar' class="col-3 mt-3 text-right">
                     <button v-on:click="guardarTalle" type="submit" class="btn btn-primary">Guardar(F3)</button>
                  </div>
                   <div v-else class="col-3 mt-3 text-right">
                     <button v-on:click="guardarTalle" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                  </div>
              

             </div>
                    <div  class="col-3 mt-3 text-right">
                     <button v-on:click="eliminarTela" type="submit" class="btn btn-danger">Eliminar(F6)</button>
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
          validarTela:false,
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
          nombreTela:"",
          validarCheck:false,
          descripcionTela:"",
          deshabilitar:false,
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
        nuevaTela(){
          let me=this;
        Common.nuevaTelaCommon().then(data => {
              me.nombreTela =data.tela[0].CODIGO+1;
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

          this.nombreTela=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreTela="";
          me.descripcionTela="";
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
      	guardarTalle(){

             let me =this;
             if(me.nombreTela===""){
               
              me.validarTela=true;
              return;
             } else {
              me.validarTela = false;
             
             } 
             if(me.descripcionTela===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 

             var data = {
              Codigo:me.nombreTela,
              Descripcion:me.descripcionTela,
              Existe:me.existeTela,
             }

              Common.guardarTelaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la Tela!!!',
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
                eliminarTela(){

             let me =this;
             if(me.nombreTela===""){
               
              me.validarTela=true;
              return;
             } else {
              me.validarTela = false;
             
             } 

             var data = {
              Codigo:me.nombreTela,
              Existe:me.existeTela,
             }

              Common.eliminarTelaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la tela!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de Tela no existe o ya contiene productos registrados!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Tela.recargar();
                
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
   me.nuevaTela();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarTalle();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarTela();
});

        }
    }
</script>
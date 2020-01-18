
<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div class="offset-md-2 col-6">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Marcas</h5>
      <div class="card-body">
       
        <div class="row">
          <div class="col-12">
            <div class="my-1 mb-3">
             <div class="custom-control custom-switch mr-sm-2">
               <input type="checkbox" class="custom-control-input" id="customControlAutosizing" v-on:change="timepicker" v-model="switch_descuento">
               <label class="custom-control-label" for="customControlAutosizing">Descuentos</label>
              </div>
           </div>
         </div>
          <div class="col-12">
            <marca-nombre ref="componente_textbox_marca" @nombre_marca='enviar_nombre' @descuento='traer_descuento' @fechaini='traer_fechaini' @fechafin='traer_fechafin' @existe_marca='existe' :nombre='nombreMarca' :validarMarca='validarMarca' @id='enviar_id' @descripcion='traer_descripcion'></marca-nombre>
          </div>   
            <div class="col-12">

                 <hr>

                   <div class="form-group">
                        <label for="exampleFormControlTextarea1">Descripcion</label>
                    <textarea v-model="descripcionMarca" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                    </div>
            </div>
              <div v-if="switch_descuento" class="col-12">

                 <hr>

                   <div class="form-group">
                      <label for="Descuento">Descuento</label>
                      <input v-model="descuento" type="text" class="form-control" id="Descuento" v-bind:class="{ 'is-invalid': validarDescuento }" ></input>
                    </div>
            </div>
                <div v-if="switch_descuento" class="col-12"> 
                  <hr>
            <label class="mt-3" for="validationTooltip01">Seleccione Intervalo de Tiempo</label>
            <div id="sandbox-container">
              <div class="input-daterange input-group">
                   <input type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" data-date-format="YYYY-MM-DD" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
                   <div class="input-group-append form-control-sm">
                      <span class="input-group-text">a</span>
                   </div>
                   <input type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" data-date-format="YYYY-MM-DD" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
              </div>
              <div class="invalid-feedback">
                    {{messageInvalidFecha}}
                </div>
            </div>  
                </div>
                <hr>
                  <div  class="col-3 mt-3 text-right">
                     <button v-on:click="nuevaMarca" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                  </div>

                 <div class="col-4">
                   
                    
                  <div v-if='guardar' class="col-3 mt-3 text-right">
                     <button v-on:click="guardarMarca" type="submit" class="btn btn-primary">Guardar(F3)</button>
                  </div>
                   <div v-else class="col-3 mt-3 text-right">
                     <button v-on:click="guardarMarca" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                  </div>
              

             </div>
                    <div  class="col-3 mt-3 text-right">
                     <button v-on:click="eliminarMarca" type="submit" class="btn btn-danger">Eliminar(F6)</button>
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
          validarMarca:false,
          validarDescuento:false,
          descuento:"",
          existeMarca:false,
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
          nombreMarca:"",
          validarCheck:false,
          descripcionMarca:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeMarca=data;
           if(data===false){
             this.guardar=true;
             this.descripcionMarca="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
         this.descripcionMarca=data;

        },
        nuevaMarca(){
          let me=this;
        Common.nuevaMarcaCommon().then(data => {
              me.nombreMarca =data.marcas[0].CODIGO+1;
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

          this.nombreMarca=data;
           
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
          me.nombreMarca="";
          me.descuento="";
          me.selectedInicialFecha="";
          me.selectedFinalFecha="";
          me.descripcionMarca="";
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
      	guardarMarca(){

             let me =this;
             if(me.nombreMarca===""){
               
              me.validarMarca=true;
              return;
             } else {
              me.validarMarca = false;
             
             } 
             if(me.descripcionMarca===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
              if((me.selectedInicialFecha === null || me.selectedInicialFecha === "") && me.switch_descuento===true) {
              me.validarInicialFecha = true;
             
              me.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
              return ;
            } else {
              me.validarInicialFecha = false;
              me.messageInvalidFecha = '';
            }

            if((me.selectedFinalFecha === null || me.selectedFinalFecha === "") && me.switch_descuento===true) {
              me.validarFinalFecha = true;
           
              me.messageInvalidFecha = 'Por favor seleccione una fecha Final';
              return ;
            } else {
              me.validarFinalFecha = false;
              me.messageInvalidFecha = '';
            } 
             if((me.descuento===null || me.descuento==="")  && me.switch_descuento===true){
                me.validarDescripcion=true;
                    
                return;
             }else{
              me.validarDescripcion=false;
             }

             var data = {
              Codigo:me.nombreMarca,
              Descripcion:me.descripcionMarca,
              Existe:me.existeMarca,
              switch_descuento:me.switch_descuento,
              fechaini:me.selectedInicialFecha,
              fechafin:me.selectedFinalFecha,
              descuento:me.descuento,
             }

              Common.guardarMarcaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la marca! !',
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
                eliminarMarca(){

             let me =this;
             if(me.nombreMarca===""){
               
              me.validarMarca=true;
              return;
             } else {
              me.validarMarca = false;
             
             } 

             var data = {
              Codigo:me.nombreMarca,
              Existe:me.existeMarca,
             }

              Common.eliminarMarcaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la marca!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de Marca no existe o ya contiene productos registrados! !',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_marca.recargar();
                
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
   me.nuevaMarca();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarMarca();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarMarca();
});

        }
    }
</script>
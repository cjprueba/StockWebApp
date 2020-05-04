
<template>
	
<div class="container">

  <div>
  
      <!-- ------------------------------------------------------------------ -->

      <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
          
      <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

      <!-- ------------------------------------------------------------------------------------- -->
          
         <div class="offset-md-2 col-6">
        <div class="card mt-3 shadow-sm">
          <h5 class="card-header">Nombres</h5>
          <div class="card-body">
           
            <div class="row">

              <div class="col-12">
                <nombre-textbox ref="componente_textbox_Nombre" @nombre_marca='enviar_nombre' @existe_color='existe' :nombre='nombreNombre' :validarNombre='validarNombre' @id='enviar_id' @descripcion='traer_descripcion'></nombre-textbox>
              </div>   
                <div class="col-12">

                     <hr>

                       <div class="form-group">
                        <label for="exampleFormControlTextarea1">Descripcion</label>
                        <textarea v-model="descripcionNombre" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                        </div>
                </div>
                    <hr>
                      <div  class="col-3 mt-3 text-right">
                         <button v-on:click="nuevoNombre" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                      </div>

                     <div class="col-4">
                       
                        
                      <div v-if='guardar' class="col-3 mt-3 text-right">
                         <button v-on:click="guardarNombre" type="submit" class="btn btn-primary">Guardar(F3)</button>
                      </div>
                       <div v-else class="col-3 mt-3 text-right">
                         <button v-on:click="guardarNombre" type="submit" class="btn btn-warning">Actualizar(F3)</button>
                      </div>
                  

                 </div>
                        <div  class="col-3 mt-3 text-right">
                         <button v-on:click="eliminarNombre" type="submit" class="btn btn-danger">Eliminar(F6)</button>
                      </div>

        </div>
      </div>
    </div>
         </div>

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
          validarNombre:false,
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
          nombreNombre:"",
          validarCheck:false,
          descripcionNombre:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeColor=data;
           if(data===false){
             this.guardar=true;
             this.descripcionNombre="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionNombre=data;

        },
        nuevoNombre(){

          let me=this;

          Common.nuevoNombreCommon().then(data => {
              me.nombreNombre =data.subCategoriaDetalle[0].CODIGO+1;
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

          this.nombreNombre=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreNombre="";
          me.descripcionNombre="";
        },
      	guardarNombre(){

             let me =this;
             if(me.nombreNombre===""){
               
              me.validarNombre=true;
              return;
             } else {
              me.validarNombre = false;
             
             } 
             if(me.descripcionNombre===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 

             var data = {
              Codigo:me.nombreNombre,
              Descripcion:me.descripcionNombre,
              Existe:me.existeColor,
             }

              Common.guardarNombreCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el Nombre !!!',
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
                eliminarNombre(){

             let me =this;
             if(me.nombreNombre===""){
               
              me.validarNombre=true;
              return;
             } else {
              me.validarNombre = false;
             
             } 

             var data = {
              Codigo:me.nombreNombre,
              Existe:me.existeColor,
             }

              Common.eliminarNombreCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente el nombre!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de Nombre no existe o ya contiene productos registrados!!!',
                     'warning'
                  )
               }
             me.$refs.componente_textbox_Nombre.recargar();
                
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
   me.nuevoNombre();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarNombre();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarNombre();
});

        }
    }
</script>
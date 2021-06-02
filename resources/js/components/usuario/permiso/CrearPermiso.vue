
<template>
	
<div class="container">

  <div v-if="$can('user.permission') && $can('user') && $can('configuracion')">

      <!-- ------------------------------------------------------------------ -->

      <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
          
      <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

      <!-- ------------------------------------------------------------------------------------- -->
    	<div class="card mt-3 shadow-sm">
      		<h5 class="card-header">Permisos</h5>
      		<div class="card-body">
             
      			<div class="row">
      			 	<div class="col-12">
                <permiso-nombre @nombre_permiso='enviar_nombre' @existe_permiso='existe' :nombre='nombrePermiso' :validarPermiso='validarPermiso' @id='enviar_id' @descripcion='traer_descripcion'></permiso-nombre>
              </div>   
      			    <div class="col-12">

      			     	   <hr>

    	                 <div class="form-group">
    	                      <label for="exampleFormControlTextarea1">Descripcion</label>
    	    		          <textarea v-model="descripcionPermiso" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
    	                  </div>
      			    </div>
    	             
    	             

                     <div class="col-12">
                     	 <hr>

                      <div v-if='guardar' class="col-12 mt-3 text-right">
                         <button v-on:click="guardarPermiso" type="submit" class="btn btn-primary">Guardar</button>
                      </div>
                       <div v-else class="col-12 mt-3 text-right">
                         <button v-on:click="guardarPermiso" type="submit" class="btn btn-warning">Actualizar</button>
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
          permisos:[],
          idPermiso:"",
          validarPermiso:false,
          existePermiso:false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombrePermiso:"",
          validarCheck:false,
          descripcionPermiso:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existePermiso=data;
           if(data===false){
             this.guardar=true;
             this.descripcionPermiso="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionPermiso=data;

        },
        enviar_nombre(data){

          this.nombrePermiso=data;

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
      	guardarPermiso(){
             let me =this;
             if(me.nombrePermiso===""){
              me.validarPermiso=true;
              return;
             } else {
              me.validarPermiso = false;
             
             } 
             if(me.descripcionPermiso===""){
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             
              Common.guardarPermisoCommon(me.nombrePermiso,me.descripcionPermiso,me.existePermiso,me.idPermiso).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el permiso !',
                     'success'
                  )
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
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
           this.llamarRoles();
        }
    }
</script>
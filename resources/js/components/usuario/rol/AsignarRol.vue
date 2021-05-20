
<template>
  
<div class="container">

  <div v-if="$can('user.rol.user') && $can('user')"> 

    <!-- ------------------------------------------------------------------ -->

    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
        
    <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

    <!-- ------------------------------------------------------------------------------------- -->
    <div class="card mt-3 shadow-sm">
        <h5 class="card-header">Asignar Rol</h5>
        <div class="card-body">
           
          <div class="row">
            
              <div class="col-12">
                 <usuario-nombre @nombre_usuario='enviar_nombre' :nombre='nombreUsuario' @id='enviar_id' @roles='traer_roles'></usuario-nombre>
              </div>
                 
                 

                   
             

                 
                    <div class="col-12">
                       <hr>

                     <h5>Lista de Roles</h5>
                        
                   <div v-if="mostrarRol" v-for="permiso in permisos" class="form-check " >
                         <input v-model="permisosSelected" :disabled="deshabilitar" v-bind:class="{ 'is-invalid': validarCheck }" class="form-check-input" type="checkbox" :value="permiso.id" :id="permiso.id" >
                         <label class="form-check-label" :for="permiso.id">{{permiso.name}}</label>
                   </div>
                    </div>

                    <div class="col-12 mt-3 text-right">
                       <button v-on:click="asignarRol" class="btn btn-primary">Guardar</button>
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
          validarRol:false,
          radioPermisos:1,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarRol:false,
          nombreRol:"",
          validarCheck:false,
          descripcionRol:"",
          nombreUsuario:"",
          idUsuario:"",
          deshabilitar:false,
        }
      }, 
      methods: {
        enviar_nombre(data){
           this.nombreUsuario=data;

        },
          traer_roles(data){

           this.permisosSelected=data;

        },
        enviar_id(id){

          this.idUsuario=id;
        },
        llamarRoles(){
          let me =this;
              Common.llamarRolesCommon().then(data => {
              me.permisos=data;
              me.mostrarRol= true;
           
                
              });
        },
        asignarRol(){

             let me =this;
             if(me.nombreUsuario===""){
              me.validarRol=true;
             
              return;
             } else {
              me.validarRol = false;
             
             } 
             if(me.permisosSelected.length === 0){
              me.validarCheck=true;
             
               return;
             }else{
              me.validarCheck=false;
             }   
              Common.asignarRolCommon(me.idUsuario,me.permisosSelected).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha asignado correctamente el rol !',
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

              me.deshabilitar=false;



         }else{

          if(me.radioPermisos==="2"){
             me.permisosSelected=[];
             me.deshabilitar=true;
          }

         }
          
        }

         
       },
        mounted() {
           this.llamarRoles();
        }
    }
</script>
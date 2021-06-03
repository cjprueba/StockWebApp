
<template>
  
<div class="container">

  <div v-if="$can('user.crear') && $can('user') && $can('configuracion')">

    <!-- ------------------------------------------------------------------ -->

    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
        
    <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

    <!-- ------------------------------------------------------------------------------------- -->

    <div class="row">
      <div class="col-auto"></div>
       <div class="offset-md-2 col-6">
          <div class="card mt-3 shadow-sm">
        <h5 class="card-header">Registro de Usuario</h5>
        <div class="card-body">
           
          <div class="row">
            <div class="col-12">
            
           
             <label for="NameUser" >Nombre</label>
              <input v-model="nombreUsuario" type="text" class="form-control" id="NameUser" v-bind:class="{ 'is-invalid': validarNombre }">
            
            
             </div>
              <div class="col-12">

                <hr>

                
                
                 <label for="EmailUser" >E-mail</label>
              <input v-model="EmailUsuario" type="text" class="form-control" id="EmailUser" v-bind:class="{ 'is-invalid': validarEmail }" >
            
            
              </div>
                 
                 

                   <div class="col-12">
                     <hr>

                   <label for="NameUser" >Contraseña</label>
                 <label for="inputPassword2" class="sr-only">Password</label>
                <input v-model="Contraseña" type="password" class="form-control" id="inputPassword2" placeholder="Password" v-bind:class="{ 'is-invalid': validarContraseña }">

                   </div>
             

                 
                    <div class="col-12">
                       <hr>
                   <label for="NameUser" >Confirmar Contraseña</label>
                    <label for="inputPassword3" class="sr-only">Confirm Password</label>
                   <input v-model="ConfirmContraseña" type="password" class="form-control" id="inputPassword3" placeholder="Confirm Password" v-bind:class="{ 'is-invalid': validarConfirmContraseña }">

                    </div>

                    <div  class="col-12 mt-3 text-right">
                       <button v-on:click="guardarUsuario" type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                

               </div>
      </div>
    </div>
       </div>
        <div class="col-auto"></div>
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
          Contraseña :"",
          EmailUsuario:"",
          ConfirmContraseña:"",
          validarNombre:false,
          radioPermisos:3,
          validarEmail:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarRol:false,
          validarContraseña:false,
           validarConfirmContraseña:false,
          descripcionRol:"",
          guardar:true,
          nombreUsuario:"",
          existeRol:false,
          idRol:"",
          deshabilitar:false,
        }
      }, 
      methods: {

        guardarUsuario(){
             let me =this;

             if(me.nombreUsuario===""){
           
              me.validarNombre=true;
              return;
             } else {
              me.validarNombre = false;
             
             } 
             if(me.EmailUsuario ===""){
              me.validarEmail=true;
              return;
             } else {
              me.validarEmail = false;
             } 
             if(me.Contraseña===""){
              me.validarContraseña=true;
               return;
             }else{
              me.validarContraseña=false;
             }
             if(me.Contraseña!==me.ConfirmContraseña || me.ConfirmContraseña===""){
             me.validarConfirmContraseña=true;
             return; 
             }else{
              me.validarConfirmContraseña=false;
             }
             
              Common.guardarUsuarioCommon(me.nombreUsuario,me.EmailUsuario,me.Contraseña).then(data => {
               if(data.response===true){

                Swal.fire({
                title: 'Guardado!!',
                text: "Se ha guardado el usuario con exito!",
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
                }).then((result) => {
                  if (result.value) {
                  location.reload();
                  }
                })

               }else{
                    Swal.fire(
                     'Error!',
                     data.status,
                     'warning'
                  )
                    me.validarEmail=true;
               }
           
                
              }).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
        }

},
        mounted() {
        }
    }
</script>
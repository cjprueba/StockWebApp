
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
                  <usuario-nombre @password_usuario='enviar_password' :password='Contraseña'  @email_usuario='enviar_email' :email='EmailUsuario' @nombre_usuario='enviar_nombre' :nombre='nombreUsuario' @id='enviar_id' @password_recibido='enviar_password_recibido' @sucursales_recibido='enviar_sucursales_recibido'></usuario-nombre>
                </div>

                <div class="col-12">
                  <hr>
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
                  <label for="PassUser" >Contraseña</label>
                  <label for="inputPassword2" class="sr-only">Password </label>
                  
                  <input type="password" v-if="!passwordRecibido || passwordRecibido==='null' || passwordRecibido===null" v-model="Contraseña"  class="form-control" id="inputPassword2" placeholder="Password" v-bind:class="{ 'is-invalid': validarContraseña }">

                  <input type="password" v-else v-model="Contraseña" class="form-control" placeholder="Password" aria-label="Disabled password example" disabled>
                  
                </div>

                <div class="col-12">
                  <hr>
                  <label for="PassUser" >Confirmar Contraseña</label>
                  <label for="inputPassword3" class="sr-only">Confirm Password</label>
                  
                  <input type="password" v-if="!passwordRecibido || passwordRecibido==='null' || passwordRecibido===null" v-model="ConfirmContraseña" class="form-control" id="inputPassword3" placeholder="Confirm Password" v-bind:class="{ 'is-invalid': validarConfirmContraseña }">
                  <input v-else v-model="Contraseña" type="password" class="form-control" aria-label="Disabled password example" disabled>

                </div>

                <div class="col-12 mt-2">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="mostrarSucursales">
                    <label class="custom-control-label" for="customSwitch1">Habilitar sucursales</label>
                  </div>
                </div>


                <div v-if="mostrarSucursales" for="customSwitch1" class="col-12">
                  <hr>
                  <label for="mosSucursal" >Sucursales</label>
                  <div class="container_checkbox1 rounded">
                    <div class="ml-3" v-for="sucursal in sucursales">
                      <div class="custom-control custom-checkbox">
                        <input class="form-check-input " type="checkbox" :value="sucursal.CODIGO" :id='sucursal.CODIGO' v-model="sucursalesSelected" :disabled="deshabilitar">
                        <label class="form-check-label" :for="sucursal.CODIGO">{{sucursal.DESCRIPCION}}</label>
                      </div>
                    </div>
                  </div>
                </div>


                <div  class="col-12 mt-3 text-right">
                  <button type="button" class="btn btn-primary" v-on:click="nuevo()">Nuevo</button>
                  <button v-if="btn_guardar" v-on:click="guardarUsuario" type="submit" class="btn btn-success">Guardar</button>
                  <button type="button" v-else class="btn btn-warning" v-on:click="guardarUsuario()">Modificar</button>
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
          sucursales:[],
          Sucursal_creado:0,
          sucursalesSelected:[],
          mostrarSucursales:false,
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
          idUsuario:"",
          existeRol:false,
          idRol:"",
          deshabilitar:false,
          passwordRecibido:false,
          btn_guardar: true
        }
      }, 
      methods: {
        nuevo(){
          this.menu=0;
          this.Contraseña='';
          this.EmailUsuario='';
          this.ConfirmContraseña='';
          this.validarNombre=false;
          this.radioPermisos=3;
          this.validarEmail=false;
          this.mostrar_error=false;
          this.mensaje='';
          this.mostrarRol=false;
          this.validarContraseña=false;
          this.validarConfirmContraseña=false;
          this.descripcionRol='';
          this.guardar=true;
          this.nombreUsuario='';
          this.idUsuario='';
          this.existeRol=false;
          this.idRol='';
          this.deshabilitar=false;
          this.passwordRecibido=false;
          this.btn_guardar= true;
          this.mostrarSucursales = false;
        },

        enviar_password_recibido(data){
          this.passwordRecibido=data;

        },
        enviar_sucursales_recibido(data){
           this.mostrarSucursales = true;
            for (var key in data){
              console.log(data[key].ID_SUCURSAL);
                 this.sucursalesSelected[key]=data[key].ID_SUCURSAL;
                
                

             }
             // console.log(this.sucursalesSelected);
            
           
        },
        enviar_password(data){
          this.Contraseña=data;

        },
        enviar_nombre(data){
          this.nombreUsuario=data;
          this.btn_guardar=false;

        },
        enviar_email(data){
          this.EmailUsuario=data;

        },
        enviar_id(id){
          this.idUsuario=id;
        },

        llamarSucursal(){
          let me =this;
          Common.filtrarSucursalUserCommon().then(data =>{
            me.sucursales=data.sucursal;
            me.sucursalesSelected[0] = data.Sucursal_creado;
            console.log(data.sucursal);
          })
          
        },
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

             if(me.Contraseña==="" && me.passwordRecibido===false){
              me.validarContraseña=true;
               return;
             }else{ 
              me.validarContraseña=false;
             }

             if((me.Contraseña!==me.ConfirmContraseña || me.ConfirmContraseña==="") && me.passwordRecibido===false){
             me.validarConfirmContraseña=true;
             return; 
             }else{
              me.validarConfirmContraseña=false;
             }
             
              Common.guardarUsuarioCommon(me.nombreUsuario,me.EmailUsuario,me.Contraseña,me.btn_guardar, me.idUsuario, me.mostrarSucursales, me.sucursalesSelected).then(data => {
               if(data.response===true){

                Swal.fire(
                    '¡Guardado!',
                    '¡Se ha guardado correctamente el empleado!',
                    'success'
                )

                setTimeout(function(){
                  location.reload();
                },1000);

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
          let me = this;
          me.llamarSucursal();
        }
    }
</script>
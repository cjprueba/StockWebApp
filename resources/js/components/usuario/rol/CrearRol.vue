
<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
	<div class="card mt-3 shadow-sm">
  		<h5 class="card-header">Roles</h5>
  		<div class="card-body">
         
  			<div class="row">

  			 	<div class="col-12">
            <rol-nombre @nombre_rol='enviar_nombre' @existe_rol='existe' :nombre='nombreRol' :validarRol='validarRol' @id='enviar_id' @permisos='traer_permisos' @roles='traer_descripcion'></rol-nombre>
          </div>   

  			    <div class="col-12">

                 <hr>

  			     	
	                 <div class="form-group">
	                      <label for="exampleFormControlTextarea1">Descripcion</label>
	    		          <textarea v-model="descripcionRol" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
	                  </div>
  			    </div>
	             
	             

                 <div class="col-12">
                 	 <hr>

	                 <h5>Permiso especial</h5>
		 		     <div class="form-check form-check-inline">
		                  <input v-model="radioPermisos"  v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1">
		                  <label class="form-check-label" for="inlineRadio1">Acceso total</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input v-model="radioPermisos" v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2">
		                  <label class="form-check-label" for="inlineRadio2">Ningun acceso</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input  v-model="radioPermisos"  v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="3">
		                  <label class="form-check-label" for="inlineRadio3">Personalizado</label>
		             </div>

                 </div>
	 		     

	             
                  <div class="col-12">
                  	 <hr>

	                 <h5>Lista de permisos</h5>
                      
		             <div v-if="mostrarPermisos" v-for="permiso in permisos" class="form-check " >
		               	   <input v-model="permisosSelected" :disabled="deshabilitar" v-bind:class="{ 'is-invalid': validarCheck }" class="form-check-input" type="checkbox" :value="permiso.id" :id="permiso.id" >
		               	   <label class="form-check-label" :for="permiso.id">{{permiso.name}}</label>
		             </div>
                  </div>

                  <div v-if='guardar' class="col-12 mt-3 text-right">
                  	 <button v-on:click="guardarRol" type="submit" class="btn btn-primary">Guardar</button>
                  </div>
                   <div v-else class="col-12 mt-3 text-right">
                     <button v-on:click="guardarRol" type="submit" class="btn btn-warning">Actualizar</button>
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
          permisos:[],
          validarRol:false,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarRol:false,
          nombreRol:"",
          validarCheck:false,
          descripcionRol:"",
          guardar:true,
          nombreUsuario:"",
          existeRol:false,
          idRol:"",
          deshabilitar:false,
        }
      }, 
      methods: {
         traer_permisos(data){

           this.permisosSelected=data;
          

        },
          existe(data){
          this.existeRol=data;
           if(data===false){
             this.guardar=true;
             this.permisosSelected=[];
             this.descripcionRol="";
             this.radioPermisos=3;
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionRol=data.description;

        },
        enviar_nombre(data){

          this.nombreRol=data;

        },
        enviar_id(id){

          this.idRol=id;
        },
      	llamarRoles(){
      		let me =this;
              Common.llamarPermisoCommon().then(data => {
              me.permisos=data.permisos;
              me.mostrarPermisos= true;
           
           			
           		});
      	},
      	guardarRol(){
             let me =this;

             if(me.nombreRol===""){
           
              me.validarRol=true;
              return;
             } else {
              me.validarRol = false;
             
             } 
             if(me.descripcionRol===""){
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.permisosSelected.length === 0){
              me.validarCheck=true;
               return;
             }else{
              me.validarCheck=false;
             }	 
             
              Common.guardarRolCommon(me.nombreRol,me.descripcionRol,me.permisosSelected,me.existeRol,me.idRol).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el rol !',
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
            me.permisosSelected=[];
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
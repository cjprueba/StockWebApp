
<template>
	
<div class="container">
	<div class="card mt-3 shadow-sm">
  		<h5 class="card-header">Roles</h5>
  		<div class="card-body">

  			<div class="row">
  			 	
  			    <div class="col-12">
  			     	  <label>Nombre de la etiqueta</label>
	                  <input v-model="nombreRol" type="text" name="" class="form-control">
	                 <div class="form-group">
	                      <label for="exampleFormControlTextarea1">Descripcion</label>
	    		          <textarea v-model="descripcionRol" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
	                  </div>
  			    </div>
	             
	             

                 <div class="col-12">
                 	 <hr>

	                 <h5>Permiso especial</h5>
		 		     <div class="form-check form-check-inline">
		                  <input  class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
		                  <label class="form-check-label" for="inlineRadio1">Acceso total</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
		                  <label class="form-check-label" for="inlineRadio2">Ningun acceso</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
		                  <label class="form-check-label" for="inlineRadio3">Personalizado</label>
		             </div>

                 </div>
	 		     

	             
                  <div class="col-12">
                  	 <hr>

	                 <h5>Lista de permisos</h5>
                      
		             <div v-if="mostrarPermisos" v-for="permiso in permisos" class="form-check " >
		               	   <input v-model="permisosSelected" class="form-check-input" type="checkbox" :value="permiso.id" :id="permiso.id" >
		               	   <label class="form-check-label" :for="permiso.id">{{permiso.name}}</label>
		             </div>
                  </div>

                  <div class="col-12 mt-3 text-right">
                  	 <button v-on:click="guardarRol" type="submit" class="btn btn-primary">Guardar</button>
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
          permisosSelected:[],
          mostrarPermisos:false,
          nombreRol:"",
          descripcionRol:""
        }
      }, 
      methods: {
      	llamarRoles(){
      		let me =this;
              Common.llamarRolesCommon().then(data => {
              me.permisos=data;
              me.mostrarPermisos= true;
           
           			
           		});
      	},
      	guardarRol(){
             let me =this;
             

              	 

              Common.guardarRolCommon(me.nombreRol,me.descripcionRol,me.permisosSelected).then(data => {
               
           
           			
           		});
      	}

      },
        mounted() {
           this.llamarRoles();
        }
    }
</script>
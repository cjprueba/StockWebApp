<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;

class CategoriaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function obtenerCategorias(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $categoria = Categoria::obtener_categorias();
        return response()->json($categoria);

        /*  --------------------------------------------------------------------------------- */
    }
     public function categoriasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $categoria = Categoria::categorias_datatable($request);
        return response()->json($categoria);
        
        /*  --------------------------------------------------------------------------------- */

}
     public function categoriaDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $categoria = Categoria::categoria_datatable($request);
        return response()->json($categoria);
        
        /*  --------------------------------------------------------------------------------- */

}
       public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $categoria = Categoria::obtener_codigo();
        return response()->json($categoria);

        /*  --------------------------------------------------------------------------------- */
    }
   public function filtrarCategoria(Request $request)
{

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $categoria = Categoria::filtrar_categoria($request->all());
        return response()->json($categoria);
        
        /*  --------------------------------------------------------------------------------- */

    }
    public function categoriaGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $categoria = Categoria::categoria_guardar($request->all());
        return response()->json($categoria);
        
        /*  --------------------------------------------------------------------------------- */

    }
                public function CategoriaEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $Categoria = Categoria::Categoria_eliminar($request->all());
        return response()->json($Categoria);
        
        /*  --------------------------------------------------------------------------------- */

    }

    // public function index()
    // {
    //     return Categoria::where('user_id', auth()->id())->get();
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $categoria = new Categoria();
    //     $categoria->description = $request->description;
    //     $categoria->user_id = auth()->id();
    //     $categoria->save();

    //     return $categoria;
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $categoria = Categoria::find($id);
    //     $categoria->description = $request->description;
    //     $categoria->save();
    //     return $categoria;
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $categoria = Categoria::find($id);
    //     $categoria->delete();
    // }
}

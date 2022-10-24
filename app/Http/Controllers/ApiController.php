<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function producto()
    {
        return view('producto');
    }

    public function catalogo()
    {
        return view('catalogo');
    }

    public function productoqr()
    {
        return view('productoQR');
    }
        public function Cajaqr()
    {

        return view('CajaCompraQr');
    }
    public function bannerCotizacion()
    {

        return view('bannerCotizacion');
    }
    public function bannerMantenimiento()
    {

        return view('bannerMantenimiento');
    }
    
}

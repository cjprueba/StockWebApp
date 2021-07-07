@extends('layouts.appProducto')

@section('content')
<div class="">
    <div>
    	<!-- PASAR LA VARIABLE DE LA URL A UN PROP DENTRO DE VUE -->
        <cajacompraqr :sucursal="{{ app('request')->input('s') }}" :codigo_ca="{{ app('request')->input('c') }}"></cajacompraqr>

    </div>
</div>
@endsection

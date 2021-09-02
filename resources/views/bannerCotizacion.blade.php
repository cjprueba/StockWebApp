@extends('layouts.appProducto')

@section('content')
<div class="">
    <div>
    	<!-- PASAR LA VARIABLE DE LA URL A UN PROP DENTRO DE VUE -->
        <banner-cotizacion :sucursal="{{ app('request')->input('s') }}"></banner-cotizacion>
    </div>
</div>
@endsection

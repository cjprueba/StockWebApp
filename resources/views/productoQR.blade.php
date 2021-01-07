@extends('layouts.appProducto')

@section('content')
<div class="">
    <div>
    	<!-- PASAR LA VARIABLE DE LA URL A UN PROP DENTRO DE VUE -->
        <productoQR :sucursal="{{ app('request')->input('s') }}" :codigo="{{ app('request')->input('c') }}"></productoQR>
    </div>
</div>
@endsection

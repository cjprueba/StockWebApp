@extends('layouts.appProducto')

@section('content')
<div class="">
    <div>
    	<!-- PASAR LA VARIABLE DE LA URL A UN PROP DENTRO DE VUE -->
        <producto :sucursal="{{ app('request')->input('s') }}"></producto>
    </div>
</div>
@endsection

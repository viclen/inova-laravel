@extends('layouts.app')

@section('content')
<cadastro-carro-cliente :caracteristicas="{{ $caracteristicas }}" :carros="{{ $carros }}" :marcas="{{ $marcas }}"
    :cliente="{{ $cliente }}" />
@endsection

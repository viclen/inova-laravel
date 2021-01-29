@extends('layouts.app')

@section('content')
<cadastro-carro-cliente :caracteristicas="{{ $caracteristicas }}" :carros="{{ $carros }}" :marcas="{{ $marcas }}"
    :dados="{{ json_encode($dados) }}" :cliente="{{ $cliente }}" />
@endsection

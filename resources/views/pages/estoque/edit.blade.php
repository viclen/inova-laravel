@extends('layouts.app')

@section('content')
<cadastro-estoque :caracteristicas="{{ $caracteristicas }}" :carros="{{ $carros }}" :marcas="{{ $marcas }}"
    :dados="{{ json_encode($dados) }}" />
@endsection

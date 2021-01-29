@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <h1>Consulta Fipe</h1>
            <consulta-fipe :carros="{{ json_encode($carros) }}" :marcas="{{ json_encode($marcas) }}" />
        </div>
    </div>
</div>
@endsection

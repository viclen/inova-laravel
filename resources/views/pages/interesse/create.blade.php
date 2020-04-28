@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <h1>Cadastro de Interesses</h1>
            <b-card no-body>
                <cadastro-interesse :caracteristicas="{{ $caracteristicas }}" :carros="{{ $carros }}"
                    :marcas="{{ $marcas }}" />
            </b-card>
        </div>
    </div>
</div>
@endsection

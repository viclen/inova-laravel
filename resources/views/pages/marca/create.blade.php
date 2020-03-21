@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Cadastro de Marcas</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :action="'/marcas'" :redirect="'/marcas'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

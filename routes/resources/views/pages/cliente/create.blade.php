@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Cadastro de Clientes</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :action="'/clientes'" :redirect="'/clientes'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

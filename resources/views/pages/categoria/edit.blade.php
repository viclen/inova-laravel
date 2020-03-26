@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Cadastro de Categorias</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :action="'/categorias'" :redirect="'/categorias'"
                        :valores="{{ json_encode($dados) }}" :esconder="['fipe_id']" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

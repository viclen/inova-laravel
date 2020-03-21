@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Cadastro de Estoque</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :opcoes="{{ json_encode($opcoes) }}"
                        :action="'/estoques'" :redirect="'/estoques'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

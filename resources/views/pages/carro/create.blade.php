@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Cadastro de Carros</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :opcoes="{{ json_encode($opcoes) }}"
                        :action="'/carros'" :redirect="'/carros'" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Cadastro de Marcas</div>

                <div class="card-body">
                    <form-padrao :dados="{{ json_encode($tipos) }}" :action="'/marcas'" :redirect="'/marcas'"
                        :valores="{{ json_encode($dados) }}" :esconder="['fipe_id']" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

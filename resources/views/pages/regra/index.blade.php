@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Regras</div>

                <div class="card-body">
                    <regras-editor :dados="{{ json_encode($dados) }}">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

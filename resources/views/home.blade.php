@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <ul>
                        <li>
                            <a href="{{route('clientes.index')}}" class="btn btn-primary">Clientes</a>
                        </li>
                        <li>
                            <a href="{{route('carros.index')}}" class="btn btn-primary">Carros</a>
                        </li>
                        <li>
                            <a href="{{route('marcas.index')}}" class="btn btn-primary">Marcas</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

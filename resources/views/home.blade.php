@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div>
                <graficos-home :data="{{ json_encode($graficos_data) }}" />
            </div>

            <div class="card mb-3">
                <div class="card-header">Possíveis clientes</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <th>Cliente</th>
                                <th>Interesse</th>
                                <th>Estoque</th>
                                <th style="width: 5px;"></th>
                            </thead>
                            <tbody>
                                @foreach ($matches as $match)
                                <tr
                                    style="background-color: rgba(108, 178, 235, {{ round(($match->prioridade) / count(explode(',', $match->caracteristicas)) * 100) / 200 }})">
                                    <td>
                                        {{ $match->cliente }}
                                    </td>
                                    <td>
                                        {{ $match->carro_interesse }}
                                    </td>
                                    <td>
                                        {{ $match->carro_estoque }}
                                    </td>
                                    <td>
                                        <a href="/estoques/{{ $match->estoque_id }}"
                                            class="btn btn-secondary text-nowrap">
                                            <span>
                                                <fa-icon icon="eye" />
                                            </span>&nbsp;
                                            Ver mais
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

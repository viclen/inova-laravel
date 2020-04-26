@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mb-3">
        <div class="col">
            <div class="card">
                <div class="card-header">Estoque</div>

                <div class="card-body">
                    @foreach ($estoque->dadosTabela($relacionamentos) as $nome => $item)
                    <div class="form-group row">
                        <label for="{{ $nome . "_" }}" class="text-capitalize col-md-4 col-form-label text-md-right">
                            {{ $nome }}
                        </label>
                        <div class="col-md-6">
                            <a @if ($estoque[$nome . "_id" ]) href="/{{ $nome }}s/{{ $estoque[$nome . "_id"] }}" @endif
                                type="text" id="{{ $nome . "_" }}" class="form-control btn-link bg-light">
                                {{ $item }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header" id="interesses">Interesses</div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            @foreach ($ignorar as $nome)
                            @if ($nome)
                            <a class="btn btn-light text-capitalize"
                                href="/{{ request()->path() }}?ignorar={{ str_replace($nome . ',', '', request()->input('ignorar')) }}#interesses">
                                {{ $nome }}
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        Cliente
                                    </th>
                                    @foreach ($estoque->dadosTabela($relacionamentos, $ignorar) as $nome => $_)
                                    <th class="text-capitalize text-nowrap">
                                        {{ $nome }}
                                        <a class="btn-link"
                                            href="/{{ request()->path() }}?ignorar={{ $nome }},{{ request()->input('ignorar') }}#interesses">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                    </th>
                                    @endforeach
                                    <th width="5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matches as $match)
                                <tr @if ($match->prioridade >= count($estoque->caracteristicas)*2) class="bg-info
                                    text-white" @endif>
                                    <td>
                                        {{ $match->interesse->cliente->nome }}
                                    </td>
                                    @foreach ($estoque->dadosTabela($relacionamentos, $ignorar) as $est_coluna =>
                                    $est_valor)
                                    <td class="text-capitalize">
                                        @foreach ($match->interesse->dadosTabela($relacionamentos, $ignorar) as
                                        $int_coluna
                                        => $int_valor)
                                        @if ($int_coluna == $est_coluna)
                                        <span @if ($int_valor==$est_valor) class="text-danger" @endif>
                                            {{ $int_valor }}
                                        </span>
                                        @endif
                                        @endforeach
                                    </td>
                                    @endforeach
                                    <td>
                                        <a href="/interesses/{{ $match->interesse_id }}"
                                            class="btn btn-secondary btn-sm text-nowrap">
                                            Ver
                                            <i class="fas fa-eye"></i>
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

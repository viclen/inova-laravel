@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mb-3">
        <div class="col">
            <div class="card">
                <div class="card-header">Estoque</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="text-capitalize col-md-4 col-form-label text-md-right">
                            Imagem
                        </label>
                        <div class="col-md-6">
                            <img src="{{ Storage::url($estoque->imagem) }}" height="200" />
                        </div>
                    </div>

                    @foreach ($estoque->dadosTabela($relacionamentos) as $nome => $item)
                    <div class="form-group row">
                        <label class="text-capitalize col-md-4 col-form-label text-md-right">
                            {{ str_replace("-", " ", $nome) }}
                        </label>
                        <div class="col-md-6">
                            <a @if ($estoque[$nome . "_id" ]) href="/{{ $nome }}s/{{ $estoque[$nome . "_id"] }}" @endif
                                id="{{ $nome . "_" }}" class="form-control btn-link bg-light">
                                {{ $item }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                    <div class="form-group row">
                        <label class="text-capitalize col-md-4 col-form-label text-md-right">
                            Observações
                        </label>
                        <div class="col-md-6">
                            <textarea class="form-control bg-light" readonly>{{ $estoque->observacoes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header" id="interesses">
                    Interesses

                    <a href="/estoques/{{ $estoque->id }}/matches/download" class="btn btn-sm btn-success float-right">
                        Baixar CSV
                        <i class="fas fa-download"></i>
                    </a>
                </div>

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
                                        {{ str_replace("-", " ", $nome) }}
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
                                <tr>
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
                                        <span @if (array_search($est_coluna, $match->getCaracteristicas())!==false)
                                            class="text-danger" @endif>
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

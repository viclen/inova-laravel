@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mb-3">
        <div class="col">
            <div class="card">
                <div class="card-header">Interesse</div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="" class="text-capitalize col-md-4 col-form-label text-md-right">
                            Cliente
                        </label>
                        <div class="col-md-6">
                            <a href="http://wa.me/55{{ \App\Formatter::soNumeros($interesse->cliente->telefone) }}"
                                class="form-control bg-light btn-link">
                                {{ $interesse->cliente->nome }}
                                <span>
                                    <fa-icon :icon="['fab', 'whatsapp']" />
                                </span>
                            </a>
                        </div>
                    </div>
                    @foreach ($interesse->dadosTabela($relacionamentos) as $nome => $item)
                    <div class="form-group row">
                        <label for="{{ $nome . "_" }}" class="text-capitalize col-md-4 col-form-label text-md-right">
                            {{ $nome }}
                        </label>
                        <div class="col-md-6">
                            <input type="text" id="{{ $nome . "_" }}" class="form-control" value="{{ $item }}"
                                readonly />
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
                <div class="card-header" id="estoques">Carros em Estoque</div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            @foreach ($ignorar as $nome)
                            @if ($nome)
                            <a class="btn btn-light text-capitalize"
                                href="/{{ request()->path() }}?ignorar={{ str_replace($nome . ',', '', request()->input('ignorar')) }}#estoques">
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
                                    @foreach ($interesse->dadosTabela($relacionamentos, $ignorar) as $nome => $_)
                                    <th class="text-capitalize text-nowrap">
                                        {{ $nome }}
                                        <a class="btn-link"
                                            href="/{{ request()->path() }}?ignorar={{ $nome }},{{ request()->input('ignorar') }}#estoques">
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
                                    @foreach ($interesse->dadosTabela($relacionamentos, $ignorar) as $int_coluna =>
                                    $int_valor)
                                    <td class="text-capitalize">
                                        @foreach ($match->estoque->dadosTabela($relacionamentos, $ignorar) as
                                        $est_coluna
                                        => $est_valor)
                                        @if ($int_coluna == $est_coluna)
                                        <span @if (array_search($int_coluna, $match->getCaracteristicas())!==false)
                                            class="text-danger" @endif>
                                            {{ $est_valor }}
                                        </span>
                                        @endif
                                        @endforeach
                                    </td>
                                    @endforeach
                                    <td>
                                        <a href="/estoques/{{ $match->estoque_id }}"
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

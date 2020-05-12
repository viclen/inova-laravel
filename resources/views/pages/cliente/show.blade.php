@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 mb-3">
            <a href="{{ url()->current() . "/edit" }}" class="btn btn-light">
                <span>
                    <fa-icon icon="edit" />
                </span>
                &nbsp;
                {{ __("Editar") }}
            </a>
        </div>

        @foreach ($dados as $titulo => $items)
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header text-capitalize">
                    {{ $titulo }}
                </div>

                <div class="card-body">
                    @if ($items)
                    @foreach ($items as $nome => $item)
                    @if (is_array($item) || $item instanceof Collection)
                    <tabela-acoes :mostrarid="false" :dados="{{ json_encode($items) }}" :colunas="''"
                        :controller="'{{ $titulo }}'" :colunasvalor="['fipe', 'valor']" :podecriar="false"
                        :podevoltar="false" :podepesquisar="false" :colunascheck="['financiado']"
                        :highlight="{{ isset($highlight) && $highlight ? "true" : "false" }}">

                        @break
                        @endif

                        @if (!strpos($nome, "_id") && !strpos($nome, "_at"))
                        <div class="form-group row">
                            <label for="{{ $nome . "_" }}"
                                class="text-capitalize col-md-4 col-form-label text-md-right">
                                {{ $nome }}
                            </label>
                            <div class="col-md-6">
                                <input type="text" id="{{ $nome . "_" }}" class="form-control" value="{{ $item }}"
                                    readonly />
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @else
                        <div class="alert alert-secondary mb-0" role="alert">Nenhum dado.</div>
                        @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-12 mb-3">
            <div class="card" id="interesses">
                <div class="card-header text-capitalize">
                    Interesses
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
                                    <td>Carro</td>
                                    <td>Marca</td>
                                    @foreach ($caracteristicas as $nome)
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
                                @foreach ($interesses as $interesse)
                                <tr>
                                    <td>
                                        {{ $interesse->carro->nome }}
                                    </td>
                                    <td>
                                        {{ $interesse->carro->marca->nome }}
                                    </td>
                                    @foreach ($caracteristicas as $coluna)
                                    <td class="text-capitalize">
                                        @foreach ($interesse->dadosTabela(['carro.marca', 'caracteristicas'], $ignorar)
                                        as $int_coluna =>
                                        $int_valor)
                                        @if ($coluna == $int_coluna)
                                        <span>
                                            {{ $int_valor }}
                                        </span>
                                        @endif
                                        @endforeach
                                    </td>
                                    @endforeach
                                    <td>
                                        <a href="/interesses/{{ $interesse->id }}"
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

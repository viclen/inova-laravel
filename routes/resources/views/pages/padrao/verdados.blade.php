@extends('layouts.app')

@section('content')
<ver-dados :dados="{{json_encode($dados)}}" :highlight="{{ isset($highlight) && $highlight ? "true" : "false" }}" />

{{-- <div class="container">
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
    </div>
</div> --}}
@endsection

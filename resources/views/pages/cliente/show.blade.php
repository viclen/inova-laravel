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
                    @if(strtolower($nome) == "telefone")
                    <div class="form-group row">
                        <label for="" class="text-capitalize col-md-4 col-form-label text-md-right">
                            Cliente
                        </label>
                        <div class="col-md-6">
                            <a id="whatsMobile"
                                href="https://api.whatsapp.com/send?phone=55{{ \App\Formatter::soNumeros($item) }}"
                                class="form-control bg-light btn-link">
                                {{ $item }}
                                <span>
                                    <fa-icon :icon="['fab', 'whatsapp']" />
                                </span>
                            </a>

                            <a id="whatsWeb"
                                href="https://web.whatsapp.com/send?phone=55{{ \App\Formatter::soNumeros($item) }}"
                                class="form-control bg-light btn-link">
                                {{ $item }}
                                <span>
                                    <fa-icon :icon="['fab', 'whatsapp']" />
                                </span>
                            </a>
                        </div>
                    </div>
                    @elseif(is_array($item) || $item instanceof Collection)
                    <tabela-acoes :mostrarid="false" :dados="{{ json_encode($items) }}" :colunas="''"
                        :controller="'{{ $titulo }}'" :colunasvalor="['fipe', 'valor']" :podecriar="false"
                        :podevoltar="false" :podepesquisar="false" :colunascheck="['financiado']"
                        :highlight="{{ isset($highlight) && $highlight ? "true" : "false" }}" />

                    @break
                    @elseif(!strpos($nome, "_id") && !strpos($nome, "_at"))
                    <div class="form-group row">
                        <label for="{{ $nome . "_" }}" class="text-capitalize col-md-4 col-form-label text-md-right">
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
            <div class="card" id="carros">
                <div class="card-header text-capitalize">
                    Carros para troca

                    <span class="float-right">
                        <a href="{{ url()->current() }}/carros/create" class="btn btn-success btn-sm">
                            <span>
                                <fa-icon icon="plus" />
                            </span>
                            Novo
                        </a>
                    </span>
                </div>
                <div class="card-body">
                    @if (count($carros))
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Carro</th>
                                    <th>Marca</th>
                                    @foreach ($caracteristicas_carros as $nome)
                                    <th class="text-capitalize text-nowrap">
                                        {{ $nome }}
                                    </th>
                                    @endforeach
                                    <th style="width: 5px;">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carros as $carro)
                                <tr>
                                    <td>
                                        {{ $carro->carro->nome }}
                                    </td>
                                    <td>
                                        {{ $carro->carro->marca->nome }}
                                    </td>
                                    @foreach ($caracteristicas_carros as $coluna)
                                    <td class="text-capitalize">
                                        @foreach ($carro->dadosTabela(['carro.marca', 'caracteristicas'],
                                        $ignorar) as $car_coluna => $car_valor)
                                        @if ($coluna == $car_coluna)
                                        <span>
                                            {{ $car_valor }}
                                        </span>
                                        @endif
                                        @endforeach
                                    </td>
                                    @endforeach
                                    <td class="text-nowrap">
                                        <a href="{{ url()->current() }}/carros/{{ $carro->id }}/edit"
                                            class="btn btn-primary btn-sm text-nowrap">
                                            <span>
                                                <fa-icon icon="edit" />
                                            </span>
                                            Editar
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="mostrarExcluir({{ $carro->id }})">
                                            <span>
                                                <fa-icon icon="trash" />
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    Nenhum carro
                    @endif
                </div>
            </div>
        </div>

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
<div class="modal fade" id="modalDelete" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Tem certeza que deseja excluir o registro?</h3>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <b-button class="float-right" variant="outline-secondary" onclick="cancelDelete()">
                        <span>
                            <fa-icon icon="times" />
                        </span>
                        Cancelar
                    </b-button>
                    <b-button class="float-right mr-2" variant="outline-danger" onclick="excluirRegistro()">
                        <span>
                            <fa-icon icon="trash" />
                        </span>
                        Excluir
                    </b-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    var idExcluir = 0;

    function mostrarExcluir(id){
        $("#modalDelete").modal('show');
        idExcluir = id;
    }

    function cancelDelete(){
        $("#modalDelete").modal('hide');
        idExcluir = 0;
    }

    function excluirRegistro(){
        if(!idExcluir){
            $("#modalDelete").modal('hide');
            return;
        }

        $.ajax({
            url: window.location.href + '/carros/' + idExcluir,
            method: 'delete',
            data : {
                "_token": $('meta[name="csrf-token"]').attr('content')  //pass the CSRF_TOKEN()
            }
        }).done((r) => {
            $("#modalDelete").modal('hide');
            window.location = window.location.href;
        });
    }
</script>
@endsection

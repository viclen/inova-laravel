@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Estoques</div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col">
                            <a href="/" class="btn btn-light">
                                <span>
                                    <fa-icon icon="arrow-left" />
                                </span>
                                &nbsp;
                                Voltar
                            </a>
                            <a href="/estoques/create" class="btn btn-success float-right">
                                <span>
                                    <fa-icon icon="plus" />
                                </span>
                                &nbsp;
                                Novo
                            </a>
                        </div>
                    </div>

                    <form action="" method="get" id="filtros">
                        <input type="hidden" name="ref" value="f">

                        <div class="row mb-2">
                            <div class="col">
                                @foreach ($ignorar as $coluna)
                                @if (trim($coluna))
                                <a class="btn btn-light text-capitalize"
                                    href="/{{ request()->path() }}?ignorar={{ str_replace([$coluna, ',,'], ['', ','], join(',', $ignorar)) }}&ref=r">
                                    {{ $coluna }}
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
                                        @foreach ($colunas as $coluna)
                                        @if (trim($coluna))
                                        <td class="text-capitalize text-nowrap p-0">
                                            <div class="input-group">
                                                @if (isset($opcoes_colunas[$coluna]))
                                                <select class="custom-select search-coluna" name="{{ $coluna }}">
                                                    <option value="">-</option>
                                                    @foreach ($opcoes_colunas[$coluna] as $opcao)
                                                    <option value="{{ $opcao['ordem'] }}" @if (isset($filtros[$coluna])
                                                        && $filtros[$coluna]==$opcao['ordem']) selected @endif>
                                                        {{ $opcao['valor'] }}</option>
                                                    @endforeach
                                                </select>
                                                @else
                                                <input class="form-control w-100 search-coluna" name="{{ $coluna }}"
                                                    placeholder="{{ $coluna }}" @if (isset($filtros[$coluna]))
                                                    value="{{ $filtros[$coluna] }}" @endif>
                                                @endif
                                                @if (isset($filtros[$coluna]))
                                                <div class="input-group-append">
                                                    <button for="{{ $coluna }}" type="button"
                                                        class="btn input-group-text text-danger btn-remove-filter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        @endif
                                        @endforeach
                                        <td class="p-0" width="5px">
                                            <button class="btn w-100">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        @foreach ($colunas as $coluna)
                                        @if (trim($coluna))
                                        <th class="text-capitalize text-nowrap">
                                            {{ $coluna }}
                                            <a class="btn-link"
                                                href="/{{ request()->path() }}?ignorar={{ $coluna }},{{ join(',', $ignorar) }}">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        </th>
                                        @endif
                                        @endforeach
                                        <th width="5px">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($estoques as $estoque)
                                    <tr>
                                        @foreach ($colunas as $coluna)
                                        @if (trim($coluna))
                                        <td class="text-capitalize text-nowrap text-ellipsis">
                                            {{ isset($estoque->dadosTabela($relacionamentos, $ignorar)[$coluna]) ?
                                            $estoque->dadosTabela($relacionamentos, $ignorar)[$coluna]
                                        :
                                            ""
                                        }}
                                        </td>
                                        @endif
                                        @endforeach

                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-sm dropdown-toggle text-nowrap"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <span>
                                                        <fa-icon icon="cog" />
                                                    </span>
                                                    &nbsp;Ações
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="/estoques/{{ $estoque->id }}"
                                                        class="dropdown-item text-success" v-on:click="verRegistro(i)">
                                                        <span>
                                                            <fa-icon icon="eye" />
                                                        </span>
                                                        &nbsp;Ver
                                                    </a>
                                                    <a href="/estoques/{{ $estoque->id }}/edit"
                                                        class="dropdown-item text-dark">
                                                        <span>
                                                            <fa-icon icon="edit" />
                                                        </span>
                                                        &nbsp;Editar
                                                    </a>
                                                    <button type="button" class="dropdown-item text-danger"
                                                        onclick="mostrarExcluir({{ $estoque->id }})">
                                                        <span>
                                                            <fa-icon icon="trash" />
                                                        </span>
                                                        &nbsp;Excluir
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(!count($estoques))
                        <div class="alert alert-secondary">
                            Nenhum dado encontrado.
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mt-2 text-center overflow-auto">
            {{ $dados->links() }}
        </div>
        <div class="col-md-2 mt-2">
            <form action="" method="get">
                <select name="qtd" id="qtd" class="custom-select" onchange="$(this).closest('form').trigger('submit')">
                    <option value="">Por página</option>
                    <option value="10" {{ $dados->perPage() == 10 ? "selected" : "" }}>10</option>
                    <option value="25" {{ $dados->perPage() == 25 ? "selected" : "" }}>25</option>
                    <option value="50" {{ $dados->perPage() == 50 ? "selected" : "" }}>50</option>
                    <option value="100" {{ $dados->perPage() == 100 ? "selected" : "" }}>100</option>
                    <option value="250" {{ $dados->perPage() == 250 ? "selected" : "" }}>250</option>
                </select>
            </form>
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
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    document.onreadystatechange = () => {
        for (let i = 0; i < document.getElementsByClassName('search-coluna').length; i++) {
            document.getElementsByClassName('search-coluna')[i].addEventListener("change", function() {
                document.querySelector("form#filtros").submit();
            });
        }

        for (let i = 0; i < document.getElementsByClassName('btn-remove-filter').length; i++) {
            document.getElementsByClassName('btn-remove-filter')[i].addEventListener("click", function() {
                document.querySelector('[name="' + this.getAttribute('for') + '"]').value = "";
                document.querySelector('[name="' + this.getAttribute('for') + '"]').selectedIndex = 0;
                document.querySelector("form#filtros").submit();
            });
        }
    }

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
            url: '/estoques/' + idExcluir,
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

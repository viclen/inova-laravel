@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Interesses</div>

                <div class="card-body">
                    <form action="" method="get" id="filtros">
                        <input type="hidden" name="ref" value="f">

                        <div class="row mb-2">
                            <div class="col">
                                <a href="/" class="btn btn-light">
                                    <span>
                                        <fa-icon icon="arrow-left" />
                                    </span>
                                    &nbsp;
                                    Voltar
                                </a>
                                <a href="/interesses/create" class="btn btn-success float-right">
                                    <span>
                                        <fa-icon icon="plus" />
                                    </span>
                                    &nbsp;
                                    Novo
                                </a>
                            </div>
                        </div>

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
                                        <td class="p-0">
                                            <button class="btn w-100">
                                                <i class="fas fa-filter"></i>
                                                &nbsp;
                                                Filtrar
                                            </button>
                                        </td>
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
                                        <th>
                                            Cliente
                                        </th>
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
                                    @foreach ($interesses as $interesse)
                                    <tr>
                                        <td class="text-capitalize text-nowrap text-ellipsis">
                                            {{ $interesse->cliente->nome }}
                                        </td>
                                        @foreach ($colunas as $coluna)
                                        @if (trim($coluna))
                                        <td class="text-capitalize text-nowrap text-ellipsis">
                                            {{ isset($interesse->dadosTabela($relacionamentos, $ignorar)[$coluna]) ?
                                            $interesse->dadosTabela($relacionamentos, $ignorar)[$coluna]
                                        :
                                            ""
                                        }}
                                        </td>
                                        @endif
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

                        @if(!count($interesses))
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
</script>
@endsection

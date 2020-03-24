@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Interesses</div>

                <div class="card-body">
                    <tabela-acoes :mostrarid="false" :dados="{{ json_encode($dados->items()) }}" :colunas="''"
                        :controller="'interesse'" :colunascheck="['financiado']" :colunasvalor="['valor']">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mt-2 text-center">
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

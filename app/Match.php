<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    public function interesse()
    {
        return $this->belongsTo(Interesse::class);
    }

    public function estoque()
    {
        return $this->belongsTo(Estoque::class);
    }

    static public function findInteresses(Estoque $estoque, $financiado = false)
    {
        $matches = [];

        $int_carro = Interesse::where('carro_id', $estoque->carro_id)->get();
        foreach ($int_carro as $interesse) {
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += 16;
            } else {
                $matches[$interesse->id] = 16;
            }

            if (($financiado && $interesse->financiado) || (!$financiado && !$interesse->financiado)) {
                $matches[$interesse->id]++;
            }
        }

        $int_marca = Interesse::join('carros', 'carros.id', 'interesses.carro_id')
            ->where('carros.marca_id', $estoque->carro->marca_id)
            ->select(['interesses.id'])
            ->get();
        foreach ($int_marca as $interesse) {
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += 8;
            } else {
                $matches[$interesse->id] = 8;
            }

            if (($financiado && $interesse->financiado) || (!$financiado && !$interesse->financiado)) {
                $matches[$interesse->id]++;
            }
        }

        $porcentagem = 0.2;
        $int_valor = Interesse::where('valor', ">", $estoque->valor - $estoque->valor * $porcentagem)
            ->where('valor', "<", $estoque->valor + $estoque->valor * $porcentagem)
            ->get();
        foreach ($int_valor as $interesse) {
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += 4;
            } else {
                $matches[$interesse->id] = 4;
            }
        }

        $int_cor = Interesse::where('cor', $estoque->cor)->get();
        foreach ($int_cor as $interesse) {
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += 2;
            } else {
                $matches[$interesse->id] = 2;
            }
        }

        $int_ano = Interesse::where('ano', $estoque->ano)->get();
        foreach ($int_ano as $interesse) {
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += 1;
            } else {
                $matches[$interesse->id] = 1;
            }
        }

        $insert = [];
        foreach ($matches as $interesse_id => $prioridade) {
            $insert[] = [
                'interesse_id' => $interesse_id,
                'prioridade' => $prioridade,
                'estoque_id' => $estoque->id,
            ];
        }

        Match::where('estoque_id', $estoque->id)->delete();
        Match::insert($insert);

        return Interesse::join('matches', 'matches.interesse_id', 'interesses.id')
            ->join('clientes', 'clientes.id', 'interesses.cliente_id')
            ->join('carros', 'carros.id', 'interesses.carro_id')
            ->join('marcas', 'marcas.id', 'carros.marca_id')
            ->where('matches.estoque_id', $estoque->id)
            ->selectRaw('
                interesses.id,
                clientes.nome as cliente,
                clientes.telefone,
                carros.nome as carro,
                marcas.nome as marca,
                interesses.ano,
                interesses.cor,
                interesses.financiado,
                interesses.created_at as data
            ')
            ->orderByDesc('matches.prioridade')
            ->orderByDesc('interesses.created_at')
            ->get();
    }

    static public function findEstoques(Interesse $interesse)
    {
        $matches = [];

        $est_carro = Estoque::where('carro_id', $interesse->carro_id)->get();
        foreach ($est_carro as $estoque) {
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += 8;
            } else {
                $matches[$estoque->id] = 8;
            }
        }

        $est_marca = Estoque::join('carros', 'carros.id', 'estoques.carro_id')
            ->where('carros.marca_id', $interesse->carro->marca_id)
            ->select(['estoques.id'])
            ->get();
        foreach ($est_marca as $estoque) {
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += 4;
            } else {
                $matches[$estoque->id] = 4;
            }
        }

        $est_cor = Estoque::where('cor', $interesse->cor)->get();
        foreach ($est_cor as $estoque) {
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += 2;
            } else {
                $matches[$estoque->id] = 2;
            }
        }

        $est_ano = Estoque::where('ano', $interesse->ano)->get();
        foreach ($est_ano as $estoque) {
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += 1;
            } else {
                $matches[$estoque->id] = 1;
            }
        }

        $insert = [];
        foreach ($matches as $estoque_id => $prioridade) {
            $insert[] = [
                'estoque_id' => $estoque_id,
                'prioridade' => $prioridade,
                'interesse_id' => $interesse->id,
            ];
        }

        Match::where('interesse_id', $interesse->id)->delete();
        Match::insert($insert);

        return Estoque::join('matches', 'matches.estoque_id', 'estoques.id')
            ->join('carros', 'carros.id', 'estoques.carro_id')
            ->join('marcas', 'marcas.id', 'carros.marca_id')
            ->where('matches.interesse_id', $interesse->id)
            ->selectRaw('
                estoques.id,
                carros.nome as carro,
                marcas.nome as marca,
                estoques.valor,
                estoques.placa,
                estoques.ano,
                estoques.cor
            ')
            ->orderByDesc('matches.prioridade')
            ->orderBy('estoques.valor')
            ->get();
    }
}

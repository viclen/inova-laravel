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
        $regras = Regra::where('grupo', 'ordem')->get();
        $ordem = [];
        foreach ($regras as $regra) {
            $ordem[$regra->nome] = $regra->valor;
        }

        $matches = [];

        $int_carro = Interesse::where('carro_id', $estoque->carro_id)->get();
        foreach ($int_carro as $interesse) {
            $valor = isset($ordem['carro']) ? pow(2, intval($ordem['carro'])) : 32;
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += $valor;
            } else {
                $matches[$interesse->id] = $valor;
            }

            if (($financiado && $interesse->financiado) || (!$financiado && !$interesse->financiado)) {
                $matches[$interesse->id]++;
            }
        }

        if ($estoque->carro->marca_id) {
            $int_marca = Interesse::join('carros', 'carros.id', 'interesses.carro_id')
                ->where('carros.marca_id', $estoque->carro->marca_id)
                ->select(['interesses.id'])
                ->get();
            foreach ($int_marca as $interesse) {
                $valor = isset($ordem['marca']) ? pow(2, intval($ordem['marca'])) : 16;
                if (isset($matches[$interesse->id])) {
                    $matches[$interesse->id] += $valor;
                } else {
                    $matches[$interesse->id] = $valor;
                }

                if (($financiado && $interesse->financiado) || (!$financiado && !$interesse->financiado)) {
                    $matches[$interesse->id]++;
                }
            }
        }

        if ($estoque->carro->categoria_id) {
            $int_categoria = Interesse::join('carros', 'carros.id', 'interesses.carro_id')
                ->where('carros.categoria_id', $estoque->carro->categoria_id)
                ->select(['interesses.id'])
                ->get();
            foreach ($int_categoria as $interesse) {
                $valor = isset($ordem['categoria']) ? pow(2, intval($ordem['categoria'])) : 8;
                if (isset($matches[$interesse->id])) {
                    $matches[$interesse->id] += $valor;
                } else {
                    $matches[$interesse->id] = $valor;
                }

                if (($financiado && $interesse->financiado) || (!$financiado && !$interesse->financiado)) {
                    $matches[$interesse->id]++;
                }
            }
        }

        $regra = Regra::where([
            ['grupo', 'valor'],
            ['nome', 'porcentagem']
        ])->first();
        $porcentagem = $regra ? $regra->valor / 100 : 0.2;

        $int_valor = Interesse::where('valor', ">", $estoque->valor - $estoque->valor * $porcentagem)
            ->where('valor', "<", $estoque->valor + $estoque->valor * $porcentagem)
            ->get();
        foreach ($int_valor as $interesse) {
            $valor = isset($ordem['valor']) ? pow(2, intval($ordem['valor'])) : 4;
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += $valor;
            } else {
                $matches[$interesse->id] = $valor;
            }
        }

        $int_cor = Interesse::where('cor', $estoque->cor)->get();
        foreach ($int_cor as $interesse) {
            $valor = isset($ordem['cor']) ? pow(2, intval($ordem['cor'])) : 2;
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += $valor;
            } else {
                $matches[$interesse->id] = $valor;
            }
        }

        $int_ano = Interesse::where('ano', $estoque->ano)->get();
        foreach ($int_ano as $interesse) {
            $valor = isset($ordem['ano']) ? pow(2, intval($ordem['ano'])) : 1;
            if (isset($matches[$interesse->id])) {
                $matches[$interesse->id] += $valor;
            } else {
                $matches[$interesse->id] = $valor;
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
            ->leftJoin('categorias', 'categorias.id', 'carros.categoria_id')
            ->where('matches.estoque_id', $estoque->id)
            ->selectRaw('
                interesses.id,
                clientes.nome as cliente,
                clientes.telefone,
                carros.nome as carro,
                marcas.nome as marca,
                categorias.nome as categoria,
                interesses.valor,
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
        $regras = Regra::where('grupo', 'ordem')->get();
        $ordem = [];
        foreach ($regras as $regra) {
            $ordem[$regra->nome] = $regra->valor;
        }

        $matches = [];

        $est_carro = Estoque::where('carro_id', $interesse->carro_id)->get();
        foreach ($est_carro as $estoque) {
            $valor = isset($ordem['carro']) ? pow(2, intval($ordem['carro'])) : 32;
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += $valor;
            } else {
                $matches[$estoque->id] = $valor;
            }
        }

        if ($interesse->carro->marca_id) {
            $est_marca = Estoque::join('carros', 'carros.id', 'estoques.carro_id')
                ->where('carros.marca_id', $interesse->carro->marca_id)
                ->select(['estoques.id'])
                ->get();
            foreach ($est_marca as $estoque) {
                $valor = isset($ordem['marca']) ? pow(2, intval($ordem['marca'])) : 16;
                if (isset($matches[$estoque->id])) {
                    $matches[$estoque->id] += $valor;
                } else {
                    $matches[$estoque->id] = $valor;
                }
            }
        }

        if ($interesse->carro->categoria_id) {
            $est_categoria = Estoque::join('carros', 'carros.id', 'estoques.carro_id')
                ->where('carros.categoria_id', $interesse->carro->categoria_id)
                ->select(['estoques.id'])
                ->get();
            foreach ($est_categoria as $estoque) {
                $valor = isset($ordem['categoria']) ? pow(2, intval($ordem['categoria'])) : 8;
                if (isset($matches[$estoque->id])) {
                    $matches[$estoque->id] += $valor;
                } else {
                    $matches[$estoque->id] = $valor;
                }
            }
        }

        $regra = Regra::where([
            ['grupo', 'valor'],
            ['nome', 'porcentagem']
        ])->first();
        $porcentagem = $regra ? $regra->valor / 100 : 0.2;
        $est_valor = Estoque::where('valor', ">=", $interesse->valor - $interesse->valor * $porcentagem)
            ->where('valor', "<=", $interesse->valor + $interesse->valor * $porcentagem)
            ->get();
        foreach ($est_valor as $estoque) {
            $valor = isset($ordem['valor']) ? pow(2, intval($ordem['valor'])) : 4;
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += $valor;
            } else {
                $matches[$estoque->id] = $valor;
            }
        }

        $est_cor = Estoque::where('cor', $interesse->cor)->get();
        foreach ($est_cor as $estoque) {
            $valor = isset($ordem['cor']) ? pow(2, intval($ordem['cor'])) : 2;
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += $valor;
            } else {
                $matches[$estoque->id] = $valor;
            }
        }

        $est_ano = Estoque::where('ano', $interesse->ano)->get();
        foreach ($est_ano as $estoque) {
            $valor = isset($ordem['ano']) ? pow(2, intval($ordem['ano'])) : 1;
            if (isset($matches[$estoque->id])) {
                $matches[$estoque->id] += $valor;
            } else {
                $matches[$estoque->id] = $valor;
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
            ->leftJoin('marcas', 'marcas.id', 'carros.marca_id')
            ->leftJoin('categorias', 'categorias.id', 'carros.categoria_id')
            ->where('matches.interesse_id', $interesse->id)
            ->selectRaw('
                estoques.id,
                carros.nome as carro,
                marcas.nome as marca,
                categorias.nome as categoria,
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

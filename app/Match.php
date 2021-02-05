<?php

namespace App;

use Exception;
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

    public function getCaracteristicas()
    {
        $arr = json_decode($this->caracteristicas);

        $caracteristicas = [];
        foreach ($arr as $id) {
            if (is_numeric($id)) {
                $caracteristicas[] = Caracteristica::find($id)->nome;
            } else {
                $caracteristicas[] = $id;
            }
        }

        return $caracteristicas;
    }

    static public function findInteresses(Estoque $estoque, $quantidade = 50)
    {
        $matches = [];
        $num_caracteristicas = count($estoque->caracteristicas) ?: 1;

        $int_carro = Interesse::where('carro_id', $estoque->carro_id)->get();
        foreach ($int_carro as $interesse) {
            $matches[$interesse->id] = [
                'prioridade' => $num_caracteristicas * 2,
                'caracteristicas' => ['carro', 'marca', 'categoria'],
            ];
        }

        if($estoque->carro->categoria_id){
            $int_carro = Interesse::whereHas('carro', function ($query) use ($estoque, $matches) {
                $query->whereNotIn('interesses.id', array_keys($matches))->where('categoria_id', $estoque->carro->categoria_id);
            })->get();
            foreach ($int_carro as $interesse) {
                if (isset($matches[$interesse->id])) {
                    $matches[$interesse->id]['prioridade']++;
                    $matches[$interesse->id]['caracteristicas'][] = 'categoria';
                } else {
                    $matches[$interesse->id] = [
                        'caracteristicas' => ['categoria'],
                        'prioridade' => 1
                    ];
                }
            }

            $encontradas = CaracteristicaInteresse::where([
                ['caracteristica_id', 2],
                ['valor', $estoque->carro->categoria_id]
            ])
                ->selectRaw('DISTINCT interesse_id, caracteristica_id')
                ->get();

            foreach ($encontradas as $int_car) {
                if(array_search('categoria', $matches[$int_car->interesse_id]['caracteristicas']) === false){
                    if (isset($matches[$int_car->interesse_id])) {
                        $matches[$int_car->interesse_id]['prioridade'] ++;
                        $matches[$int_car->interesse_id]['caracteristicas'][] = 'categoria';
                    } else {
                        $matches[$int_car->interesse_id] = [
                            'caracteristicas' => ['categoria'],
                            'prioridade' => 1
                        ];
                    }
                }
            }
        }

        $regra_marca = Regra::where([
            ['grupo', 'match'],
            ['nome', 'marca']
        ])->first();
        $match_marca = $regra_marca ? !!$regra_marca->valor : 1;
        if ($match_marca && $estoque->carro && $estoque->carro->marca_id) {
            $int_marca = Interesse::join('carros', 'carros.id', 'interesses.carro_id')
                ->where('carros.marca_id', $estoque->carro->marca_id)
                ->whereNotIn('interesses.id', array_keys($matches))
                ->select(['interesses.id'])
                ->get();

            foreach ($int_marca as $interesse) {
                if (isset($matches[$interesse->id])) {
                    $matches[$interesse->id]['prioridade']++;
                    $matches[$interesse->id]['caracteristicas'][] = 'marca';
                } else {
                    $matches[$interesse->id] = [
                        'caracteristicas' => ['marca'],
                        'prioridade' => 1
                    ];
                }
            }
        }

        $regra = Regra::where([
            ['grupo', 'valor'],
            ['nome', 'porcentagem']
        ])->first();
        $porcentagem = $regra ? $regra->valor / 100 : 0.2;

        foreach ($estoque->caracteristicas as $caracteristica) {
            $encontradas = CaracteristicaInteresse::where([
                ['caracteristica_id', $caracteristica->caracteristica_id]
            ])->where(function ($query) use ($caracteristica, $porcentagem) {
                $query->where('valor', '=', $caracteristica->valor);
                if ($caracteristica->descricao->tipo == 1 || $caracteristica->tipo == 2) {
                    $query->orWhere([
                        ['comparador', '<'],
                        ['valor', '<=', $caracteristica->valor]
                    ])->orWhere([
                        ['comparador', '>'],
                        ['valor', '>=', $caracteristica->valor]
                    ])->orWhere([
                        ['comparador', '~'],
                        ['valor', '>=', $caracteristica->valor - $caracteristica->valor * $porcentagem],
                        ['valor', '<=', $caracteristica->valor + $caracteristica->valor * $porcentagem],
                    ]);
                } elseif ($caracteristica->descricao->tipo == 0) {
                    $query->orWhereRaw("
                        comparador = '<'
                        AND '$caracteristica->valor' LIKE CONCAT(`valor`, '%')
                    ")->orWhereRaw("
                        comparador = '>'
                        AND '$caracteristica->valor' LIKE CONCAT('%', `valor`)
                    ")->orWhereRaw("
                        comparador = '~'
                        AND '$caracteristica->valor' LIKE CONCAT('%', `valor`, '%')
                    ");
                }
            })
                ->selectRaw('DISTINCT interesse_id, caracteristica_id')
                ->get();

            foreach ($encontradas as $int_car) {
                if (isset($matches[$int_car->interesse_id])) {
                    $matches[$int_car->interesse_id]['prioridade'] += $caracteristica->descricao->peso;
                    $matches[$int_car->interesse_id]['caracteristicas'][] = $int_car->caracteristica_id;
                } else {
                    $matches[$int_car->interesse_id] = [
                        'caracteristicas' => [$int_car->caracteristica_id],
                        'prioridade' => 1
                    ];
                }
            }

            if ($caracteristica->exclusoria) {
                foreach ($matches as $id => $_) {
                    $encontrou = false;
                    foreach ($encontradas as $int_car) {
                        if ($int_car->interesse_id == $id) {
                            $encontrou = true;
                            break;
                        }
                    }
                    if (!$encontrou) {
                        $matches[$id]--;
                    }
                }
            }
        }

        $found = Interesse::whereIn('id', array_keys($matches))->get()->toArray();

        $insert = [];
        foreach ($matches as $interesse_id => $dados) {
            if (array_search($interesse_id, array_map(function ($obj) {
                return $obj['id'];
            }, $found)) === false) {
                continue;
            }

            $insert[] = [
                'interesse_id' => $interesse_id,
                'estoque_id' => $estoque->id,
                'prioridade' => $dados['prioridade'],
                'caracteristicas' => json_encode($dados['caracteristicas']),
            ];
        }

        CaracteristicaInteresse::whereNotIn('interesse_id', $found)->delete();

        Match::where('estoque_id', $estoque->id)->delete();
        Match::insert($insert);

        return Match::with([
            'interesse.caracteristicas.descricao',
            'interesse.carro.marca',
            'interesse.cliente'
        ])
            ->where('estoque_id', $estoque->id)
            ->orderByDesc('prioridade')
            ->limit($quantidade)
            ->get();
    }

    static public function findEstoques(Interesse $interesse, $quantidade = 50)
    {
        $matches = [];
        $num_caracteristicas = count($interesse->caracteristicas) ?: 1;

        $est_carro = Estoque::where('carro_id', $interesse->carro_id)->get();
        foreach ($est_carro as $estoque) {
            $matches[$estoque->id] = [
                'prioridade' => $num_caracteristicas * 2,
                'caracteristicas' => ['carro', 'marca', 'categoria'],
            ];
        }

        if($interesse->carro && $interesse->carro->categoria_id){
            $est_carro = Estoque::whereHas('carro', function ($query) use ($interesse, $matches) {
                $query->where('categoria_id', $interesse->carro->categoria_id);
            })->whereNotIn('estoques.id', array_keys($matches))->get();
            foreach ($est_carro as $estoque) {
                if (isset($matches[$estoque->id])) {
                    $matches[$estoque->id]['prioridade']++;
                    $matches[$estoque->id]['caracteristicas'][] = 'categoria';
                } else {
                    $matches[$estoque->id] = [
                        'caracteristicas' => ['categoria'],
                        'prioridade' => 1
                    ];
                }
            }

            $encontradas = CaracteristicaEstoque::where([
                ['caracteristica_id', 2],
                ['valor', $interesse->carro->categoria_id]
            ])
                ->selectRaw('DISTINCT estoque_id, caracteristica_id')
                ->get();

            foreach ($encontradas as $est_car) {
                if (array_search('categoria', $matches[$est_car->estoque_id]['caracteristicas']) === false) {
                    if (isset($matches[$est_car->estoque_id])) {
                        $matches[$est_car->estoque_id]['prioridade']++;
                        $matches[$est_car->estoque_id]['caracteristicas'][] = 'categoria';
                    } else {
                        $matches[$est_car->estoque_id] = [
                            'caracteristicas' => ['categoria'],
                            'prioridade' => 1
                        ];
                    }
                }
            }
        }

        $regra_marca = Regra::where([
            ['grupo', 'match'],
            ['nome', 'marca']
        ])->first();

        $match_marca = $regra_marca ? !!$regra_marca->valor : 1;

        if ($match_marca && $interesse->carro && $interesse->carro->marca_id) {
            $est_marca = Estoque::join('carros', 'carros.id', 'estoques.carro_id')
                ->where('carros.marca_id', $interesse->carro->marca_id)
                ->whereNotIn('estoques.id', array_keys($matches))
                ->select(['estoques.id'])
                ->get();

            foreach ($est_marca as $estoque) {
                if (isset($matches[$estoque->id])) {
                    $matches[$estoque->id]['prioridade']++;
                    $matches[$estoque->id]['caracteristicas'][] = 'marca';
                } else {
                    $matches[$estoque->id] = [
                        'caracteristicas' => ['marca'],
                        'prioridade' => 1
                    ];
                }
            }
        }

        $regra = Regra::where([
            ['grupo', 'valor'],
            ['nome', 'porcentagem']
        ])->first();
        $porcentagem = $regra ? $regra->valor / 100 : 0.2;

        foreach ($interesse->caracteristicas as $caracteristica) {
            $encontradas = CaracteristicaEstoque::where([
                ['caracteristica_id', $caracteristica->caracteristica_id]
            ])->where(function ($query) use ($caracteristica, $porcentagem) {
                $query->where('valor', '=', $caracteristica->valor);
                if ($caracteristica->descricao->tipo == 1 || $caracteristica->descricao->tipo == 2) {
                    if ($caracteristica->comparador == '<') {
                        $query->orWhere('valor', '<=', $caracteristica->valor);
                    } elseif ($caracteristica->comparador == '>') {
                        $query->orWhere('valor', '>=', $caracteristica->valor);
                    } elseif ($caracteristica->comparador == '~') {
                        $query->orWhere([
                            ['valor', '>=', $caracteristica->valor - ($caracteristica->valor * $porcentagem)],
                            ['valor', '<=', $caracteristica->valor + ($caracteristica->valor * $porcentagem)],
                        ]);
                    }
                } elseif ($caracteristica->tipo == 0) {
                    if ($caracteristica->comparador == '<') {
                        $query->orWhere('valor', 'LIKE', "$caracteristica->valor%");
                    } elseif ($caracteristica->comparador == '>') {
                        $query->orWhere('valor', 'LIKE', "%$caracteristica->valor");
                    } elseif ($caracteristica->comparador == '~') {
                        $query->orWhere('valor', 'LIKE', "%$caracteristica->valor%");
                    }
                }
            })
                ->selectRaw('DISTINCT estoque_id, caracteristica_id')
                ->get();

            foreach ($encontradas as $est_car) {
                if (isset($matches[$est_car->estoque_id])) {
                    $matches[$est_car->estoque_id]['prioridade'] += $caracteristica->descricao->peso;
                    $matches[$est_car->estoque_id]['caracteristicas'][] = $est_car->caracteristica_id;
                } else {
                    $matches[$est_car->estoque_id] = [
                        'caracteristicas' => [$est_car->caracteristica_id],
                        'prioridade' => 1
                    ];
                }
            }

            if ($caracteristica->exclusoria) {
                foreach ($matches as $id => $_) {
                    $encontrou = false;
                    foreach ($encontradas as $est_car) {
                        if ($est_car->estoque_id == $id) {
                            $encontrou = true;
                            break;
                        }
                    }
                    if (!$encontrou) {
                        $matches[$id]--;
                    }
                }
            }
        }

        $found = Estoque::whereIn('id', array_keys($matches))->get()->toArray();

        $insert = [];
        foreach ($matches as $estoque_id => $dados) {
            if (array_search($estoque_id, array_map(function ($obj) {
                return $obj['id'];
            }, $found)) === false) {
                continue;
            }

            $insert[] = [
                'estoque_id' => $estoque_id,
                'interesse_id' => $interesse->id,
                'prioridade' => $dados['prioridade'],
                'caracteristicas' => json_encode($dados['caracteristicas']),
            ];
        }

        CaracteristicaEstoque::whereNotIn('estoque_id', $found)->delete();

        Match::where('interesse_id', $interesse->id)->delete();
        Match::insert($insert);

        return Match::with([
            'estoque.caracteristicas.descricao',
            'estoque.carro.marca',
        ])
            ->where('interesse_id', $interesse->id)
            ->orderByDesc('prioridade')
            ->limit($quantidade)
            ->get();
    }
}

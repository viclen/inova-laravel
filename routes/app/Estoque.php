<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Estoque extends Model
{
    protected $fillable = [
        'observacoes',
        'fipe_id',
        'carro_id',
    ];

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            $types[$item->Field] = $item->Type;
        }

        return $types;
    }

    public function dadosTabela(array $relacionamentos = ['carro', 'caracteristicas', 'categoria'], $ignorar = [])
    {
        $dados = [];

        foreach ($relacionamentos as $relacao) {
            if (strpos($relacao, 'carro') !== false && array_search('carro', $ignorar) === false) {
                if ($this->carro) {
                    $dados['carro'] = $this->carro->nome;
                    if (strpos($relacao, '.marca') !== false && array_search('marca', $ignorar) === false) {
                        if ($this->carro->marca_id) {
                            $dados['marca'] = $this->carro->marca->nome;
                        }
                    }
                }
            } elseif (strpos($relacao, 'caracteristicas') !== false && array_search('caracteristicas', $ignorar) === false) {
                foreach ($this->caracteristicas as $caracteristica) {
                    if (array_search($caracteristica->descricao->nome, $ignorar) === false) {
                        $valor = $caracteristica->valor;
                        try {
                            if ($caracteristica->descricao->tipo == 1) {
                                $valor = Formatter::mil($valor);
                            } elseif ($caracteristica->descricao->tipo == 2) {
                                $valor = Formatter::valor($valor);
                            } elseif ($caracteristica->descricao->tipo == 3) {
                                $valor = $caracteristica->valor_opcao->valor;
                            } elseif ($caracteristica->descricao->tipo == 4) {
                                $valor = Formatter::boolean($valor);
                            }
                        } catch (\Throwable $th) {
                            throw new Exception("Caracteristica: " . json_encode($caracteristica) . "\n" . $th->getMessage());
                        }

                        $dados[$caracteristica->descricao->nome] = $valor;
                    }
                }
            } elseif (strpos($relacao, 'categoria') !== false && array_search('categoria', $ignorar) === false) {
                if ($this->carro->categoria_id) {
                    $categoria = OpcaoCaracteristica::find($this->carro->categoria_id)->first();
                    if ($categoria) {
                        $dados['categoria'] = $categoria->valor;
                    }
                }
            }
        }

        return $dados;
    }

    public function caracteristicas()
    {
        return $this->hasMany(CaracteristicaEstoque::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CarroCliente extends Model
{
    protected $fillable = [
        'carro_id',
        'cliente_id',
        'observacoes'
    ];

    public function getTypes()
    {
        $types = [];
        $items = DB::select('describe ' . $this->getTable());

        foreach ($items as $item) {
            $types[$item->Field] = $item->Type;
        }

        return $types;
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function caracteristicas()
    {
        return $this->hasMany(CaracteristicaCarroCliente::class);
    }

    public function dadosTabela(array $relacionamentos = ['carro', 'caracteristicas'], $ignorar = [])
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
            }
        }

        return $dados;
    }
}

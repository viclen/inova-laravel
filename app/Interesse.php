<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Interesse extends Model
{
    protected $fillable = [
        'cliente_id',
        'carro_id',
        'observacoes',
        'origem',
    ];

    public const ORIGENS = [
        'Facebook',
        'Whatsapp',
        'Instagram',
        'Loja',
        'Telefone',
        'OLX',
        'Outro'
    ];

    public const COMPARADORES = [
        '<' => 'Menor que',
        '>' => 'Maior que',
        '=' => 'Igual a',
        '~' => 'Em torno de'
    ];

    public const COMPARADORES_TEXTO = [
        '<' => 'Começa com',
        '>' => 'Termina em',
        '=' => 'Igual a',
        '~' => 'Contém'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

    public function dadosTabela(array $relacionamentos = ['carro', 'cliente', 'caracteristicas', 'categoria'], array $ignorar = [])
    {
        $dados = [];

        if (count($ignorar)) {
            if (array_search('origem', $ignorar) !== false) {
                $dados['origem'] = Interesse::ORIGENS[$this->origem];
            }
        }

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
            } elseif (strpos($relacao, 'cliente') !== false && array_search('cliente', $ignorar) === false) {
                if ($this->cliente) {
                    $dados['cliente'] = $this->cliente->nome;
                }
            } elseif (strpos($relacao, 'caracteristicas') !== false && array_search('caracteristicas', $ignorar) === false) {
                foreach ($this->caracteristicas as $caracteristica) {
                    try {
                        if (array_search($caracteristica->descricao->nome, $ignorar) === false) {
                            $valor = $caracteristica->valor;
                            $comparador = "";

                            if ($caracteristica->descricao->tipo == 0) {
                                $comparador = Interesse::COMPARADORES_TEXTO[$caracteristica->comparador];
                            } elseif ($caracteristica->descricao->tipo == 1) {
                                $comparador = Interesse::COMPARADORES[$caracteristica->comparador];
                                $valor = Formatter::mil($valor);
                            } elseif ($caracteristica->descricao->tipo == 2) {
                                $valor = Formatter::valor($valor);
                                $comparador = Interesse::COMPARADORES[$caracteristica->comparador];
                            } elseif ($caracteristica->descricao->tipo == 3) {
                                $valor = $caracteristica->valor_opcao->valor;
                            } elseif ($caracteristica->descricao->tipo == 4) {
                                $valor = Formatter::boolean($valor);
                            }

                            $dados[$caracteristica->descricao->nome] = trim("$comparador $valor");
                        }
                    } catch (Exception $e) {
                    }
                }
            } elseif (strpos($relacao, 'categoria') !== false && array_search('categoria', $ignorar) === false) {
                if ($this->carro->categoria_id) {
                    $categoria = OpcaoCaracteristica::where([['caracteristica_id', 2], ['ordem', $this->carro->categoria_id]])->first();
                    if ($categoria) {
                        $dados['categoria'] = $categoria->valor;
                    }
                }
            }
        }

        return $dados;
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

    public function caracteristicas()
    {
        return $this->hasMany(CaracteristicaInteresse::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaInteresse extends Model
{
    protected $fillable = [
        'caracteristica_id',
        'interesse_id',
        'valor',
        'comparador',
    ];

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }

    public function interesse()
    {
        return $this->belongsTo(Interesse::class);
    }

    public function descricao()
    {
        return $this->caracteristica();
    }

    public function valor_opcao()
    {
        return $this->belongsTo(OpcaoCaracteristica::class, 'valor', 'ordem')->where('caracteristica_id', $this->caracteristica_id);
    }

    public function getValor()
    {
        if ($this->descricao->tipo == 3) {
            $this->opcao = OpcaoCaracteristica::where([
                ['caracteristica_id', $this->descricao->id],
                ['ordem', 'valor']
            ])->first();
            if ($this->opcao) {
                $this->valor = $this->opcao->valor;
                return $this->valor;
            }
        }
        return $this->valor;
    }
}

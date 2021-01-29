<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaEstoque extends Model
{
    protected $fillable = [
        'caracteristica_id',
        'estoque_id',
        'valor',
    ];

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }

    public function estoque()
    {
        return $this->belongsTo(Estoque::class);
    }

    public function descricao()
    {
        return $this->caracteristica();
    }

    public function valor_opcao()
    {
        return $this->belongsTo(OpcaoCaracteristica::class, 'valor', 'ordem')->where('caracteristica_id', $this->caracteristica_id);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaCarroCliente extends Model
{
    protected $fillable = [
        'caracteristica_id',
        'carro_cliente_id',
        'valor',
    ];

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class);
    }

    public function carro_cliente()
    {
        return $this->belongsTo(CarroCliente::class);
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

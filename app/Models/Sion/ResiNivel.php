<?php

namespace App\Models\Sion;

use App\Models\Gedeon\TablaBase;

class ResiNivel extends TablaBase
{
    protected $table = 'sion.resi_niveles';
    protected $primaryKey = 'id_nivel';

    protected $fillable = [
        'id_residencia',
        'nombre',
        'numero',
        'descripcion'
    ];

    // Relación con Residencia
    public function residencia()
    {
        return $this->belongsTo(ResiResidencia::class, 'id_residencia');
    }

    // Scope para ordenar niveles
    public function scopeOrdenado($query)
    {
        return $query->orderBy('numero');
    }

    // Validación única de número por residencia
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($modelo) {
            $existente = self::where('id_residencia', $modelo->id_residencia)
                ->where('numero', $modelo->numero)
                ->exists();

            if ($existente) {
                throw new \Exception('Ya existe un nivel con este número en la residencia');
            }
        });
    }
}

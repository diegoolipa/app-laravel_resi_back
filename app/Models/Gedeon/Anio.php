<?php

namespace App\Models\Gedeon;

class Anio extends TablaBase
{
    protected $table = 'gedeon.anios';
    protected $primaryKey = 'id_anio';

    protected $fillable = ['anio', 'es_activo'];
    protected $casts = ['es_activo' => 'boolean'];

    public static function cambiarAnioActivo($anioId)
    {
        self::where('es_activo', true)->update(['es_activo' => false]);
        return self::find($anioId)->update(['es_activo' => true]);
    }
}

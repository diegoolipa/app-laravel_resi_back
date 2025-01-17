<?php

namespace App\Traits;

trait AuditFields
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->fecha_creacion = now();
            $model->fecha_actualizacion = now();
            // $model->usuario_creacion = auth()->id();
        });

        static::updating(function ($model) {
            $model->fecha_actualizacion = now();
            // $model->usuario_actualizacion = auth()->id();
        });
    }
}

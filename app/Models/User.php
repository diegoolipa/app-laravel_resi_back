<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Salomon\Persona;
use App\Models\Salomon\UsuarioDepartamento;
use App\Models\Sion\UsuarioResidencia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con Persona
    public function persona()
    {
        return $this->hasOne(Persona::class, 'id_usuario'); // persona.id_usuario -> users.id
    }

    public function departamentoActual()
    {
       return $this->hasOne(UsuarioDepartamento::class, 'id_usuario')
           ->where('es_activo', true)
           ->where('estado', 1);
    }

    public function departamentos()
    {
       return $this->hasMany(UsuarioDepartamento::class, 'id_usuario')
           ->where('estado', 1);
    }
}

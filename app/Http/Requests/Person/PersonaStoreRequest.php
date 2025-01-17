<?php

namespace App\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;

class PersonaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Validación base de persona
            'tipo_persona' => 'required|in:N,J',
            'id_usuario' => 'nullable|exists:users,id',

            // Validaciones para Persona Natural
            'nombres' => 'required_if:tipo_persona,N|max:100',
            'apellido_paterno' => 'required_if:tipo_persona,N|max:100',
            'apellido_materno' => 'nullable|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'id_tipo_genero' => 'required_if:tipo_persona,N|exists:salomon.tipo_generos,id_tipo_genero',
            'id_tipo_estado_civil' => 'required_if:tipo_persona,N|exists:salomon.tipo_estado_civiles,id_tipo_estado_civil',

            // Validaciones para Persona Jurídica
            'razon_social' => 'required_if:tipo_persona,J|max:200',
            'nombre_comercial' => 'nullable|max:200',
            'sitio_web' => 'nullable|max:200|url',

            // Validaciones para Documentos
            'documentos' => 'nullable|array',
            'documentos.*.id_tipo_documento' => 'required|exists:salomon.tipo_documentos,id_tipo_documento',
            'documentos.*.numero_documento' => [
                'required',
                'max:50',
                'unique:salomon.persona_documentos,numero_documento'
            ],

            // Validaciones para Dirección
            'direccion' => 'nullable|array',
            'direccion.direccion' => 'required_with:direccion|max:255',
            'direccion.distrito' => 'nullable|max:100',
            'direccion.provincia' => 'nullable|max:100',
            'direccion.departamento' => 'nullable|max:100',
            'direccion.pais' => 'nullable|max:100',
            'direccion.es_principal' => 'boolean',

            // Validaciones para Celular
            'celular' => 'nullable|array',
            'celular.numero_celular' => [
                'required_with:celular',
                'max:20',
                'regex:/^[0-9+\-\s]+$/'
            ],
            'celular.es_principal' => 'boolean',

            // Validaciones para Correo
            'correo' => 'nullable|array',
            'correo.correo' => [
                'required_with:correo',
                'email:rfc,dns',
                'max:100',
                'unique:salomon.persona_correos,correo'
            ],
            'correo.es_principal' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'tipo_persona.required' => 'El tipo de persona es obligatorio',
            'tipo_persona.in' => 'El tipo de persona debe ser Natural (N) o Jurídica (J)',
            'nombres.required_if' => 'Los nombres son obligatorios para persona natural',
            'apellido_paterno.required_if' => 'El apellido paterno es obligatorio para persona natural',
            'razon_social.required_if' => 'La razón social es obligatoria para persona jurídica',
            'documentos.*.numero_documento.unique' => 'El número de documento ya está registrado',
            'correo.correo.unique' => 'El correo electrónico ya está registrado',
            'celular.numero_celular.regex' => 'El número de celular tiene un formato inválido'
        ];
    }
}

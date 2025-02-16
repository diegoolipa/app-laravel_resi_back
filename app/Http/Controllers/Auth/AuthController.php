<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function  login(Request $request)
    {

        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            //?autenticacion
            if (!Auth::attempt($credenciales)) {
                throw new Exception("Credenciales invalidas", 401);
            }
            //?generar token
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            $data = [
                'access_token' => $token,
                'usuario' => $request->user()
            ];
            return $this->successResponse($data, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }


    public function register(Request $request)
    {

        //? valida datos
        $request->validate([
            'name' => 'required|string|max:100|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|same:c_password',
        ]);
        try {
            //? registrar en la db
            $usuario = new User();
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            $usuario->password = Hash::make($request->password);
            $usuario->save();

            //? responder
            return $this->successResponse('Usuario creado con éxito', 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        };
    }
    public function profile(Request $request)
    {
        return $this->successResponse($request->user(), 200);
        //ll
    }
    public function logout(Request $request)
    {
        // Limpiar toda la sesión
        session()->forget('user_session');
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Sesión cerrada exitosamente');
    }
}

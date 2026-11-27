<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $result = DB::transaction(function () use ($request) {

            $user = User::create($request->validated());

            $categories = [
                'Estudos',
                'Trabalho',
                'Saúde',
                'Atividade física',
                'Pessoal',
                'Família',
                'Finanças',
                'Casa',
                'Espiritualidade',
                'Compras',
            ];

            foreach ($categories as $category) {
                $user->categories()->create([
                    'name' => $category,
                    'is_system' => true,
                    'is_active' => true,
                ]);
            }

            $token = $user->createToken('determinapp')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
                ],
        ], 201);
    }

        public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'E-mail ou senha inválidos.',
            ], 401);
        }

        $token = $user->createToken('determinapp')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

        public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
            'data' => null,
        ]);
    }
}

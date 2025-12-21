<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Listagem de perfis com filtro e paginação
     */
    public function index(Request $request)
    {
        $query = Profile::query();

        // Filtro por nome do perfil (ex: Admin, Operador)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
                // Filtro por Período de cadastro
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            // Converte '2025-12-01' para '2025/12/01' para coincidir com o banco
            $inicio = str_replace('-', '/', $request->data_inicio);
            $fim = str_replace('-', '/', $request->data_fim);

            $query->whereBetween('data_registration', [$inicio, $fim]);
        }

        // Retorna paginado (compatível com o seu componente Vue de paginação)
        return response()->json($query->paginate(5));
    }

    /**
     * Criar um novo perfil
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:profiles,name'
        ]);

        $profile = Profile::create($request->all());

        return response()->json([
            'message' => 'Perfil criado com sucesso!',
            'profile' => $profile
        ], 201);
    }

    /**
     * Mostrar detalhes de um perfil (e talvez os usuários vinculados)
     */
    public function show($id)
    {
        // Opcional: carregar quantos usuários estão vinculados a este perfil
        $profile = Profile::with('users')->withCount('users')->find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil não encontrado'], 404);
        }

        return response()->json($profile);
    }

    /**
     * Atualizar dados do perfil
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil não encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:profiles,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $profile->update($request->all());

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'profile' => $profile
        ]);
    }

    /**
     * Excluir perfil
     */
    public function destroy($id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil não encontrado'], 404);
        }

        // Regra de segurança: Não excluir perfil se houver usuários vinculados
        if ($profile->users()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir um perfil que possui usuários vinculados.'
            ], 422);
        }

        $profile->delete();

        return response()->json(['message' => 'Perfil excluído com sucesso']);
    }

    /**
     * Método auxiliar para carregar o select de perfis na tela de usuário
     */
    public function listAll()
    {
        return response()->json(Profile::all(['id', 'name', 'data_registration']));
    }
}

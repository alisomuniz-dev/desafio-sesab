<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Listagem e Pesquisa com Filtros
    public function index(Request $request)
    {
        $query = User::with(['profile', 'addresses']);

        // Filtro por Nome
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filtro por CPF
        if ($request->filled('cpf')) {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        // Filtro por Período de cadastro
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('created_at', [$request->data_inicio, $request->data_fim]);
        }

        return response()->json($query->get());
    }

    // Cadastro de Usuário e Endereços (N:N)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'profile_id' => 'required|exists:profiles,id',
        ], [
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.unique' => 'Este e-mail já está em uso.',
        ]);

        $user = User::create($request->all());

        // Gerencia múltiplos endereços
        if ($request->has('addresses')) {
            foreach ($request->addresses as $item) {
                // Busca endereço existente ou cria novo
                $address = Address::firstOrCreate([
                    'logradouro' => $item['logradouro'],
                    'cep' => $item['cep']
                ]);
                // Vincula na tabela pivô ]
                $user->addresses()->attach($address->id);
            }
        }

        return response()->json($user->load('addresses'), 201);
    }

    // Detalhar Usuário
    public function show($id)
    {
        return User::with(['profile', 'addresses'])->findOrFail($id);
    }

    // Alterar Cadastro
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        // Se houver novos endereços, você pode usar sync() para atualizar a lista
        if ($request->has('addresses')) {
            // Lógica de sincronização...
        }

        return response()->json($user);
    }

    // Excluir Cadastro
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->addresses()->detach(); // Remove vínculos antes de excluir
        $user->delete();

        return response()->json(null, 204);
    }
}

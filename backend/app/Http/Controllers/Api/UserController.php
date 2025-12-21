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
            // Converte '2025-12-01' para '2025/12/01' para coincidir com o banco
            $inicio = str_replace('-', '/', $request->data_inicio);
            $fim = str_replace('-', '/', $request->data_fim);

            $query->whereBetween('data_registration', [$inicio, $fim]);
        }

        return response()->json($query->paginate(5));
    }

    // Cadastro de Usuário e Endereços (N:N)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'profile_id' => 'required|exists:profiles,id',
            'data_registration' => now()->format('Y/m/d'),
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

        if ($request->has('addresses')) {
            $addressIds = [];

            foreach ($request->addresses as $item) {
                // Busca ou cria o endereço (sua lógica original)
                $address = Address::firstOrCreate([
                    'logradouro' => $item['logradouro'],
                    'cep' => $item['cep']
                ]);
                $addressIds[] = $address->id;
            }

            // 3. O sync() retorna um array com 'attached', 'detached' e 'updated'
            $changes = $user->addresses()->sync($addressIds);

            // IDs que foram removidos da relação deste usuário
            $detachedIds = $changes['detached'];

            // 4. Lógica de exclusão de endereços órfãos
            foreach ($detachedIds as $detachedId) {
                $address = Address::find($detachedId);

                // Verificamos se ainda existe algum OUTRO usuário vinculado a este endereço
                if ($address && !$address->users()->exists()) {
                    $address->delete();
                }
            }
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

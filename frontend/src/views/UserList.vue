<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-700">Listagem de Usuários</h2>
        <router-link to="/cadastro" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-gray-00">
          + Novo Usuário
        </router-link>
      </div>
      
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nome</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">CPF</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Data Cadastro</th>
            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th> -->
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.cpf }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.created_at }}</td>
            <!--<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.perfil }}</td> -->
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
              <button @click="detailUser(user.id)" class="text-blue-600 hover:text-blue-900 mx-2">Detalhar</button>
              <button @click="editUser(user.id)" class="text-yellow-600 hover:text-yellow-900 mx-2">Editar</button>
              <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900 mx-2">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Filtro de Usuários</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input
            v-model="filters.name"
            type="text"
            class="mt-1 block w-full
                bg-gray-100
                text-black
                border border-gray-300
                rounded-md
                shadow-sm
                focus:ring-blue-500
                focus:border-blue-500
                p-2"
            />
        </div>
        
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700">CPF</label>
            <input v-model="filters.cpf"
                type="text" 
                class="mt-1 block w-full 
                bg-gray-100
                text-black
                border border-gray-300 
                rounded-md shadow-sm 
                focus:ring-blue-500 
                focus:border-blue-500 
                p-2" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Período de Cadastro</label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
            <input v-model="filters.data_inicio" type="date" class="mt-1 block w-full bg-gray-400 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
            <input v-model="filters.data_fim" type="date" class="mt-1 block w-full bg-gray-400 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
          </div>
        </div>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <button @click="clearFilters" class="px-4 py-2 text-sm text-black bg-gray-200 rounded-md hover:bg-gray-400">Limpar</button>
        <button @click="search" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Aplicar Filtro</button>
      </div>
    </div>
  </div>
  <div v-if="selectedUser" class="fixed inset-0 bg-black text-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
        <h3 class="text-xl font-bold">Detalhes do Usuário</h3>
        <button @click="selectedUser = null" class="bg-gray-800 text-white px-6 py-2 rounded">Fechar</button>
        </div>
    
        <div class="grid grid-cols-2 gap-4">
        <p><strong>Nome:</strong> {{ selectedUser.name }}</p>
        <p><strong>E-mail:</strong> {{ selectedUser.email }}</p>
        <p><strong>CPF:</strong> {{ selectedUser.cpf }}</p>
        <p><strong>Perfil:</strong> {{ selectedUser.profile?.name }}</p>
        </div>

        <div class="mt-6">
        <h4 class="font-semibold border-b mb-2">Endereços Vinculados:</h4>
        <ul class="list-disc pl-5 space-y-1">
            <li v-for="end in selectedUser.addresses" :key="end.id">
            {{ end.logradouro }} - CEP: {{ end.cep }}
            </li>
        </ul>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'; // Importe o onMounted para carregar a lista ao abrir
import { useRouter } from 'vue-router'; // Para o botão editar funcionar
import api from '../services/api';

const router = useRouter();

const filters = reactive({
  name: '',
  cpf: '',
  data_inicio: '',
  data_fim: ''
});

const users = ref([]);
const selectedUser = ref(null);

// Função para buscar todos os usuários (com filtros)
const search = async () => {
  try {
    const response = await api.get('/usuarios', { params: filters });
    users.value = response.data;
  } catch (error) {
    console.error("Erro ao buscar usuários:", error);
  }
};

// Função para carregar os detalhes e abrir o modal
const detailUser = async (id) => {
    console.log("chegou aqui teste")
  try {
    const response = await api.get(`/usuarios/${id}`);
    selectedUser.value = response.data; // Ao preencher aqui, o v-if do modal ativa
    console.log("responsemodal", response)

  } catch (error) {
    alert("Erro ao carregar detalhes do usuário");
  }
};

// Função para redirecionar para a tela de edição
const editUser = (id) => {
  router.push(`/editar/${id}`);
};

const deleteUser = async (id) => {
  if (confirm("Deseja realmente excluir este cadastro?")) {
    try {
      await api.delete(`/usuarios/${id}`);
      search();
    } catch (error) {
      alert("Erro ao excluir usuário");
    }
  }
};

const clearFilters = () => {
  filters.name = '';
  filters.cpf = '';
  filters.data_inicio = '';
  filters.data_fim = '';
  search();
};

// Carregar a lista assim que a página abrir
onMounted(() => {
  search();
});
</script>
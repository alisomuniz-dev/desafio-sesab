<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
      <div class="flex flex-col w-full gap-4">
        <div class="flex justify-end">
          <router-link 
            to="/perfis" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm font-medium border border-transparent hover:bg-gray-200 hover:text-gray-800 hover:border-gray-400 transition-all"
          >
            Gerenciar Perfis
          </router-link>
        </div>        

        <div class="text-center">
          <h1 class="text-2xl font-bold mb-6 text-gray-800">Filtro de Usuários</h1>
        </div>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input
            v-model="filters.name"
            @input="filters.name = filters.name.replace(/[^a-zA-ZÀ-ÿ\s]/g, '')"
            type="text"
            class="mt-1 block w-full
                bg-gray-200
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
                v-mask="'###.###.###-##'"
                type="text" 
                class="mt-1 block w-full 
                bg-gray-200
                text-black
                border border-gray-300 
                rounded-md shadow-sm 
                focus:ring-blue-500 
                focus:border-blue-500 
                p-2" />
        </div>
        <div class="md:col-span-2">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="grid grid-cols-1 md:grid-cols-1">
              <label class="block text-sm font-medium text-gray-700">inicio do período</label>
              <input v-model="filters.data_inicio" type="date" class="mt-1 block w-full bg-gray-200 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-1">
              <label class="block text-sm font-medium text-gray-700">fim do Cadastro</label>
              <input v-model="filters.data_fim" type="date" class="mt-1 block w-full bg-gray-200 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <button @click="clearFilters" class="px-4 py-2 text-sm text-black bg-gray-200 rounded-md hover:bg-gray-200">Limpar</button>
        <button @click="search" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Aplicar Filtro</button>
      </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-700">Listagem de Usuários</h2>
          <router-link 
            to="/usuarios/cadastro" 
            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium border border-transparent hover:bg-gray-200 hover:text-gray-800 hover:border-gray-400 transition-all"
          >
            + Novo Usuário
          </router-link>
      </div>
      
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nome</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">CPF</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Data Cadastro</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">E-mail</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Perfil</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.id }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.name }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.cpf }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.data_registration.split('/').reverse().join('/') }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.profile.name }}</td>
            <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="detailUser(user.id)" class="bg-white border-gray-800 hover:bg-gray-300 text-blue-800 mx-2">Detalhar</button>
              <button @click="editUser(user.id)" class="bg-white border-gray-800 hover:bg-gray-300 text-yellow-600 mx-2">Editar</button>
              <button @click="prepareDeletion(user.id)" class="bg-white border-gray-800 hover:bg-gray-300 text-red-600 mx-2">Excluir</button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="8" class="px-6 py-8 text-center text-gray-500">Nenhum usuário encontrado.</td>
          </tr>
        </tbody>
      </table>
      <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Página <strong>{{ pagination.current_page }}</strong> de <strong>{{ pagination.last_page }}</strong>
        </div>
        
        <div class="flex gap-2">
          <button 
            @click="search(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 text-black bg-white border rounded text-sm disabled:opacity-50 hover:bg-gray-100"
          >
            Anterior
          </button>

          <button 
            @click="search(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 text-black bg-white border rounded text-sm disabled:opacity-50 hover:bg-gray-100"
          >
            Próximo
          </button>
        </div>
      </div>
    </div>
  </div>
  <div 
    v-if="selectedUser" 
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm"
    @click.self="selectedUser = null"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
      
      <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50 shrink-0">
        <h3 class="text-xl font-bold text-gray-800">
          Detalhes do Usuário
        </h3>
        <button 
          @click="selectedUser = null" 
          class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 p-2 rounded-full transition-colors focus:outline-none"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="p-6 overflow-y-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="flex items-start space-x-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Nome Completo</p>
              <p class="text-lg font-semibold text-gray-900 leading-tight">{{ selectedUser.name }}</p>
            </div>
          </div>
  
          <div class="flex items-start space-x-4">
            <div class="p-3 bg-green-100 text-green-600 rounded-lg shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">E-mail</p>
              <p class="text-lg font-semibold text-gray-900 leading-tight break-all">{{ selectedUser.email }}</p>
            </div>
          </div>
  
          <div class="flex items-start space-x-4">
            <div class="p-3 bg-purple-100 text-purple-600 rounded-lg shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">CPF</p>
              <p class="text-lg font-semibold text-gray-900 leading-tight">{{ selectedUser.cpf }}</p>
            </div>
          </div>
  
          <div class="flex items-start space-x-4">
            <div class="p-3 bg-yellow-100 text-yellow-700 rounded-lg shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Perfil de Acesso</p>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mt-1">
                {{ selectedUser.profile?.name || 'Sem Perfil' }}
              </span>
            </div>
          </div>
        </div>
  
        <div class="mt-8">
          <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-2 border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            Endereços Vinculados ({{ selectedUser.addresses?.length || 0 }})
          </h4>

          <div v-if="selectedUser.addresses && selectedUser.addresses.length > 0" class="space-y-3">
            <div v-for="end in selectedUser.addresses" :key="end.id" class="flex items-start p-4 bg-gray-50 border border-gray-200 rounded-lg">
              <div class="mr-4 text-red-500 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                  <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.493 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ end.logradouro }}</p>
                <p class="text-xs text-gray-500">CEP: {{ end.cep }}</p>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-500 text-sm">
            Nenhum endereço vinculado a este usuário.
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-4 flex justify-end shrink-0">
        <button 
          @click="selectedUser = null" 
          class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-200 hover:text-black  transition-colors"
        >
          Fechar
        </button>
      </div>

    </div>
  </div>
  <ConfirmationModal
    :isOpen="showDeleteModal"
    title="Excluir Usuário"
    message="Tem certeza que deseja excluir este usuário? Esta ação não poderá ser desfeita e removerá todos os dados vinculados."
    confirmText="Sim, excluir"
    cancelText="Cancelar"
    intent="danger"
    @cancel="showDeleteModal = false"
    @confirm="executeDeletion"
  />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'; // Importe o onMounted para carregar a lista ao abrir
import ConfirmationModal from '../components/ConfirmationModal.vue'; // Ajuste o caminho
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
const userIdToDelete = ref(null);
const showDeleteModal = ref(false);

// Novos estados para paginação
const pagination = reactive({
  current_page: 1,
  last_page: 1,
});

const search = async (page = 1) => {
  try {
    // Passamos a página atual para o backend
    const response = await api.get('/usuarios', { 
      params: { 
        ...filters, 
        page: page 
      } 
    });

    // O Laravel paginado retorna os dados dentro de .data.data
    users.value = response.data.data;
    
    // Atualizamos o estado da paginação
    pagination.current_page = response.data.current_page;
    pagination.last_page = response.data.last_page;
  } catch (error) {
    alert("Erro ao buscar usuários.");
  }
};

// Função para carregar os detalhes e abrir o modal
const detailUser = async (id) => {
  try {
    const response = await api.get(`/usuarios/${id}`);
    selectedUser.value = response.data; // Ao preencher aqui, o v-if do modal ativa
  } catch (error) {
    alert("Erro ao carregar detalhes do usuário");
  }
};

// Função para redirecionar para a tela de edição
const editUser = (id) => {
  router.push(`/usuarios/editar/${id}`);
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
  search(1); // Volta para a página 1 ao limpar
};

const prepareDeletion = (id) => {
  userIdToDelete.value = id;
  showDeleteModal.value = true;
};

const executeDeletion = async () => {
  if (!userIdToDelete.value) return;

  try {
    // Faz a chamada real para sua API
    await api.delete(`/usuarios/${userIdToDelete.value}`);
    
    // Fecha o modal e limpa o ID
    showDeleteModal.value = false;
    userIdToDelete.value = null;

    // Atualiza a lista para refletir a exclusão
    search(pagination.current_page);
    
  } catch (error) {
    alert("Não foi possível excluir o usuário.");
  }
};

// Carregar a lista assim que a página abrir
onMounted(() => {
  search();
});
</script>
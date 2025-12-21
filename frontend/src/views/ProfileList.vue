<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
      <div class="flex flex-col w-full gap-4">
        <div class="flex justify-end">
          <router-link 
            to="/" 
            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium border border-transparent hover:bg-gray-200 hover:text-gray-800 hover:border-gray-400 transition-all"
          >
            Gerenciar Usuários
          </router-link>
        </div>        

        <div class="text-center">
          <h1 class="text-2xl font-bold mb-6 text-gray-800">Filtro de Perfis</h1>
        </div>
      </div>      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-gray-700">Nome do Perfil</label>
            <input
            v-model="filters.name"
            @input="filters.name = filters.name.replace(/[^a-zA-ZÀ-ÿ\s]/g, '')"
            type="text"
            placeholder="Ex: Administrador"
            class="mt-1 block w-full bg-gray-200 text-black border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2"
            />
        </div>
        
        <div class="md:col-span-2">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Início do Período</label>
              <input v-model="filters.data_inicio" type="date" class="mt-1 block w-full bg-gray-200 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Fim do Período</label>
              <input v-model="filters.data_fim" type="date" class="mt-1 block w-full bg-gray-200 text-black border-gray-300 rounded-md shadow-sm border p-2 text-sm" />
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 flex justify-end gap-2">
        <button @click="clearFilters" class="px-4 py-2 text-sm text-black bg-gray-200 rounded-md hover:bg-gray-300">Limpar</button>
        <button @click="search(1)" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Aplicar Filtro</button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-700">Listagem de Perfis</h2>
        <router-link 
          to="/perfis/cadastro" 
          class="px-4 py-2 border bg-green-600 text-white rounded-md text-sm font-medium hover:bg-gray-200 hover:text-gray-800 hover:border-gray-400 transition-all"
        >
          + Novo Perfil
        </router-link>
      </div>
      
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome do Perfil</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data de Cadastro</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="profile in profiles" :key="profile.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ profile.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ profile.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ profile.data_registration.split('/').reverse().join('/') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
              <button @click="detailProfile(profile.id)" class="bg-white border-gray-800 hover:bg-gray-300 text-blue-800 mx-2">Detalhar</button>
              <button @click="prepareDeletion(profile.id)" class="bg-white border-gray-800 hover:bg-gray-300 text-red-600 mx-2">Excluir</button>
            </td>
          </tr>
          <tr v-if="profiles.length === 0">
            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Nenhum perfil encontrado.</td>
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
  <div v-if="selectedProfile" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-3xl w-full p-6 shadow-2xl">
        
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Detalhes do Perfil: {{ selectedProfile.name }}</h3>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z"></path></svg>
                Usuários com este perfil ({{ selectedProfile.users?.length || 0 }})
            </h4>

            <div class="max-h-60 overflow-y-auto border rounded-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nome</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">E-mail</th>
                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">CPF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in selectedProfile.users" :key="user.id" class="hover:bg-blue-50">
                        <td class="px-4 py-2 text-left text-sm text-gray-900">{{ user.name }}</td>
                        <td class="px-4 py-2 text-left text-sm text-gray-500">{{ user.email }}</td>
                        <td class="px-4 py-2 text-left text-sm text-gray-500">{{ user.cpf }}</td>
                        </tr>
                        <tr v-if="selectedProfile.users?.length === 0">
                        <td colspan="3" class="px-4 py-6 text-center text-gray-400 text-sm">Nenhum usuário vinculado a este perfil.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

            <div class="mt-6 flex justify-end">
            <button @click="selectedProfile = null" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-200 hover:text-black  transition-colors">
                Fechar
            </button>
            </div>
        </div>
    </div>

  <ConfirmationModal
    :isOpen="showDeleteModal"
    title="Excluir Perfil"
    message="Tem certeza que deseja excluir este perfil? Esta ação pode afetar usuários vinculados a ele."
    confirmText="Sim, excluir"
    cancelText="Cancelar"
    intent="danger"
    @cancel="showDeleteModal = false"
    @confirm="executeDeletion"
  />

  <AlertModal
    :is-open="showAlert"
    :title="alertConfig.title"
    :message="alertConfig.message"
    :type="alertConfig.type"
    @close="showAlert = false"
  />

  
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import ConfirmationModal from '../components/ConfirmationModal.vue';
import AlertModal from '../components/GenericModal.vue';

const router = useRouter();

// Estado dos Filtros
const filters = reactive({
  name: '',
  data_inicio: '',
  data_fim: ''
});

// Dados
const profiles = ref([]);
const profileIdToDelete = ref(null);
const showDeleteModal = ref(false);
const showAlert = ref(false);
const selectedProfile = ref(null); // Armazena o perfil e seus usuários

// Paginação
const pagination = reactive({
  current_page: 1,
  last_page: 1,
});

// Busca de dados (API)
const search = async (page = 1) => {
  try {
    const response = await api.get('/perfis', { 
      params: { 
        ...filters, 
        page: page 
      } 
    });
    // Adaptado para estrutura padrão de paginação do Laravel
    profiles.value = response.data.data;
    pagination.current_page = response.data.current_page;
    pagination.last_page = response.data.last_page;
  } catch (error) {
    alert("Erro ao buscar perfis.");
  }
};

const clearFilters = () => {
  filters.name = '';
  filters.data_inicio = '';
  filters.data_fim = '';
  search(1);
};

const detailProfile = async (id) => {
  try {
    const response = await api.get(`/perfis/${id}`);
    selectedProfile.value = response.data;
  } catch (error) {
    alert("Erro ao carregar detalhes do perfil");
  }
};

const prepareDeletion = (id) => {
  profileIdToDelete.value = id;
  showDeleteModal.value = true;
};

const executeDeletion = async () => {
  if (!profileIdToDelete.value) return;

  try {
    await api.delete(`/perfis/${profileIdToDelete.value}`);
    showDeleteModal.value = false;
    profileIdToDelete.value = null;
    
    // Recarrega a página atual ou volta para a 1 se a lista ficar vazia
    search(pagination.current_page);
  } catch (error) {
    showDeleteModal.value = false;
    alertConfig.title = "Ação Negada";
    alertConfig.message = error.response.data.message; // Mensagem vinda do Controller
    alertConfig.type = "error";
    showAlert.value = true;
  }
};

const alertConfig = reactive({
  title: '',
  message: '',
  type: 'error'
});

onMounted(() => {
  search();
});
</script>
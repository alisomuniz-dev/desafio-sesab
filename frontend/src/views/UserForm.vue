<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
          {{ isEditing ? 'Editar Usuário' : 'Novo Cadastro' }}
        </h1>
        <router-link to="/" class="text-blue-600 hover:underline">← Voltar para a lista</router-link>
      </div>

      <form @submit.prevent="saveUser">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nome*</label>
            <input v-model="form.name" type="text" required class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">E-mail*</label>
            <input v-model="form.email" type="email" required class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">CPF*</label>
            <input v-model="form.cpf" type="text" placeholder="000.000.000-00" required class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Perfil*</label>
            <select v-model="form.profile_id" required class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300">
              <option value="">Selecione um perfil</option>
              <option v-for="p in profiles" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
        </div>

        <div class="border-t pt-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Endereços</h2>
            <button type="button" @click="addAddress" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
              + Adicionar Endereço
            </button>
          </div>

          <div
            v-for="(addr, index) in form.addresses"
            :key="index"
            class="bg-gray-50 p-4 rounded-md mb-4 border border-gray-200"
          >
            <!-- Header do endereço -->
            <div class="flex justify-between items-center mb-3">
              <span class="text-sm font-semibold text-gray-600">
                Endereço {{ index + 1 }}
              </span>
              <button
                type="button"
                @click="removeAddress(index)"
                class="text-red-500 bg-black-200 hover:bg-red-700 hover:text-gray-100 text-sm font-bold"
              >
                Excluir
              </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div class="md:col-span-2">
                <label class="text-xs font-bold text-gray-500 uppercase">
                  Logradouro / Rua
                </label>
                <input
                  v-model="addr.logradouro"
                  type="text"
                  class="w-full bg-gray-200 text-black border rounded p-2 mt-1"
                />
              </div>

              <div>
                <label class="text-xs font-bold text-gray-500 uppercase">
                  CEP
                </label>
                <input
                  v-model="addr.cep"
                  type="text"
                  class="w-full bg-gray-200 text-black border rounded p-2 mt-1"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="mt-8 flex justify-end gap-3">
          <button type="button" @click="$router.push('/')" class="px-6 py-2 border rounded-md text-gray-100 hover:bg-red-800">Cancelar</button>
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md">
            {{ isEditing ? 'Atualizar Cadastro' : 'Salvar Cadastro' }}
          </button>
        </div>
      </form>
    </div>
    <ErrorModal 
      :is-open="modalConfig.show" 
      :title="modalConfig.title"
      :message="modalConfig.message"
      :type="modalConfig.type"
      @close="modalConfig.show = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import ErrorModal from '../components/GenericModal.vue';

const route = useRoute();
const router = useRouter();
const isEditing = ref(!!route.params.id);

// Estrutura do formulário conforme PDF 
const form = reactive({
  name: '',
  email: '',
  cpf: '',
  profile_id: '',
  addresses: [] // Suporte a múltiplos endereços 
});

const profiles = ref([]);

const addAddress = () => {
  form.addresses.push({ logradouro: '', cep: '' });
};

const removeAddress = (index) => {
  form.addresses.splice(index, 1);
};

const modalConfig = reactive({
  show: false,
  title: '',
  message: '',
  type: 'error'
});

const showModal = (title, message, type) => {
  modalConfig.title = title;
  modalConfig.message = message;
  modalConfig.show = true;
  modalConfig.type = type;
};

const saveUser = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/usuarios/${route.params.id}`, form); 
      showModal('Concluído', "Usuário Atualizado com Sucesso", 'success');
    } else {
      await api.post('/usuarios', form);
      showModal('Concluído', "Usuário Cadastrado com Sucesso", 'success');
    }
    // IMPORTANTE: Remova o router.push daqui. 
    // Agora o modal cuidará disso quando o usuário clicar no botão.

  } catch (error) {
    // Tratamento do erro de CPF duplicado (aquele que vimos no log)
    if (error.response?.status === 422) {
      const msg = error.response.data.errors.cpf?.[0] || "Este e-mail já está em uso.";
      showModal('Erro', msg, 'error');
    } else {
      showModal('Erro', "Ocorreu um erro inesperado", 'error');
    }
  }
};

onMounted(async () => {
  // Carrega perfis para o select
  const respProfiles = await api.get('/perfis');
  profiles.value = respProfiles.data;

  // Se for edição, carrega os dados do usuário 
  if (isEditing.value) {
    const respUser = await api.get(`/usuarios/${route.params.id}`);
    Object.assign(form, respUser.data);
  }
});
</script>
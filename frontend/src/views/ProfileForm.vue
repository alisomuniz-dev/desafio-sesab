<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
      <div class="flex flex-col justify-between items-center mb-6 gap-5">
        <h1 class="text-2xl font-bold text-gray-800">
          Novo Perfil
        </h1>
      </div>

      <form @submit.prevent="saveProfile">
        <div class="grid grid-cols-1 gap-6 mb-8">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nome do Perfil*</label>
            <input 
              v-model="form.name" 
              @input="form.name = form.name.replace(/[^a-zA-ZÀ-ÿ\s]/g, '')" 
              type="text" 
              required 
              placeholder="Ex: Administrador, Operador..."
              class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300" 
            />
            <p class="text-xs text-gray-500 mt-1">O nome deve ser único no sistema.</p>
          </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
          <button 
            type="button" 
            @click="$router.push('/perfis')" 
            class="px-6 py-2 border rounded-md text-white hover:bg-gray-200 hover:text-gray-900 transition-colors"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-gray-200 hover:text-gray-900 shadow-md transition-colors"
          >
            Salvar Perfil
          </button>
        </div>
      </form>
    </div>

    <GenericModal 
      :is-open="modalConfig.show" 
      :title="modalConfig.title"
      :message="modalConfig.message"
      :type="modalConfig.type"
      @close="handleModalClose"
    />
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import GenericModal from '../components/GenericModal.vue';

const route = useRoute();
const router = useRouter();

const profiles = ref([]);

const form = reactive({
  name: '',
  description: ''
});

const modalConfig = reactive({
  show: false,
  title: '',
  message: '',
  type: 'success'
});

const showModal = (title, message, type) => {
  modalConfig.title = title;
  modalConfig.message = message;
  modalConfig.type = type;
  modalConfig.show = true;
};

// Função para lidar com o fechamento do modal
const handleModalClose = () => {
  modalConfig.show = false;
  // Se a operação foi sucesso, volta para a lista após fechar o modal
  if (modalConfig.type === 'success') {
    router.push('/perfis');
  }
};

const saveProfile = async () => {
  try {
    await api.post('/perfis', form);
    showModal('Concluído', "Perfil cadastrado com sucesso!", 'success');
    router.push('/perfis');
  } catch (error) {
    if (error.response?.status === 422) {
      const msg = error.response.data.errors?.name?.[0] || "Este nome de perfil já existe.";
      showModal('Erro de Validação', msg, 'error');
    } else {
      showModal('Erro', "Ocorreu um erro ao salvar o perfil.", 'error');
    }
  }
};
</script>
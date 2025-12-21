<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
      <div class="flex flex-col justify-between items-center mb-6 gap-5">
        <h1 class="text-2xl font-bold text-gray-800">
          {{ isEditing ? 'Editar Perfil' : 'Novo Perfil' }}
        </h1>
      </div>

      <form @submit.prevent="saveProfile">
        <div class="grid grid-cols-1 gap-6 mb-8">
          <div v-if="!isEditing">
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

          <div v-else>
            <label class="block text-sm font-medium text-gray-700">Escolha o perfil desejado!*</label>
            <select 
              v-model="form.profile_id" 
              required 
              class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300"
            >
              <option value="">Selecione um perfil</option>
              <option v-for="p in profiles" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>

            <div class="flex flex-col items-center mt-3">
              <div class="relative group flex items-center justify-center cursor-help">
                
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                  class="w-7 h-7 text-blue-600 hover:text-blue-800 transition-all transform hover:scale-110">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>

                <div class="absolute top-full mt-2 flex-col items-center hidden group-hover:flex w-48 z-20">
                  <div class="w-3 h-3 -mb-1.5 rotate-45 bg-gray-800"></div>
                  
                  <span class="relative p-2 text-xm leading-tight text-black bg-gray-100 shadow-lg rounded-md text-center border border-blue-800">
                    Caso o perfil desejado não esteja presente no select, o usuário pode criar um perfil atráves da aba de perfis.
                  </span>
                </div>
                
              </div>
              <span class="text-[10px] text-gray-400 mt-1 uppercase font-bold">Saiba mais</span>
            </div>
          </div>
          

          <!-- <div>
            <label class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              class="mt-1 block w-full bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300"
              placeholder="Descreva as permissões ou finalidade deste perfil..."
            ></textarea>
          </div> -->
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
            {{ isEditing ? 'Atualizar Perfil' : 'Salvar Perfil' }}
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
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import GenericModal from '../components/GenericModal.vue';

const route = useRoute();
const router = useRouter();
const isEditing = ref(!!route.params.id);

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
    if (isEditing.value) {
      await api.put(`/perfis/${route.params.id}`, form);
      showModal('Concluído', "Perfil atualizado com sucesso!", 'success');
      router.push('/perfis');
    } else {
      await api.post('/perfis', form);
      showModal('Concluído', "Perfil cadastrado com sucesso!", 'success');
      router.push('/perfis');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      // Pega a mensagem de erro de "unique" vinda do Laravel
      const msg = error.response.data.errors?.name?.[0] || "Este nome de perfil já existe.";
      showModal('Erro de Validação', msg, 'error');
    } else {
      showModal('Erro', "Ocorreu um erro ao salvar o perfil.", 'error');
    }
  }
};

onMounted(async () => {
  if (isEditing.value) {
    try {
      const respProfiles = await api.get('/perfis');
      profiles.value = respProfiles.data.data;
    } catch (error) {
      showModal('Erro', "Não foi possível carregar os dados do perfil.", 'error');
    }
  }
});
</script>
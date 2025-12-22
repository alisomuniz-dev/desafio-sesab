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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nome*</label>
            <input v-model="form.name" @input="form.name = form.name.replace(/[^a-zA-ZÀ-ÿ\s]/g, '')" type="text" required class="mt-1 block w-full h-10 bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">E-mail*</label>
            <input v-model="form.email" type="email" required class="mt-1 block w-full h-10 bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300" />
          </div>
          <div>
            <div>
              <label class="block text-sm font-medium text-gray-700">CPF*</label>
              <input 
                v-model="form.cpf" 
                v-mask="'###.###.###-##'" 
                @blur="validateCPF"
                type="text" 
                :class="{'border-red-500': errors.cpf}"
                class="mt-1 block w-full h-10 bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300"
              />
              <span v-if="errors.cpf" class="text-red-500 text-xs mt-1">{{ errors.cpf }}</span>
            </div>          
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Perfil*</label>
            <select v-model="form.profile_id" required class="mt-1 block w-full h-10 bg-gray-200 text-black border rounded-md p-2 shadow-sm focus:ring-blue-500 border-gray-300">
              <option value="">Selecione um perfil</option>
              <option v-for="p in profiles" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <div class="flex flex-col items-end mt-1"> 
            <div class="relative group flex items-center justify-center cursor-help">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                class="w-7 h-7 text-blue-600 hover:text-blue-800 transition-all transform hover:scale-110">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>

              <div class="absolute bottom-full left-1/2 -translate-x-1/2 flex-col items-center hidden group-hover:flex w-48 z-20">
                <span class="relative p-2 text-xm leading-tight text-black bg-gray-100 shadow-lg rounded-md text-center border border-blue-800">
                  Caso o perfil desejado não esteja presente no select, o usuário pode criar um perfil através da aba de perfis.
                </span>
                <div class="w-3 h-3 -mt-1.5 rotate-45 bg-gray-800"></div>
              </div>
            </div>
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
                class="bg-white border-gray-800 text-red-600 hover:bg-red-500 hover:text-white mx-2"
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
                  required
                  class="w-full bg-gray-200 text-black border rounded p-2 mt-1"
                />
              </div>

              <div>
                <label class="text-xs font-bold text-gray-500 uppercase">CEP</label>
                <input
                  v-model="addr.cep"
                  v-mask="'#####-###'"
                  @blur="validateCEP(index)"
                  type="text"
                  required
                  :class="{'border-red-500': addr.error}"
                  class="w-full bg-gray-200 text-black border rounded p-2 mt-1"
                />
                <span v-if="addr.error" class="text-red-500 text-xs mt-1">{{ addr.error }}</span>
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
const errors = reactive({
  cpf: ''
});

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
  form.addresses.push({ logradouro: '', cep: '', error: '' });
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
  // 1. Validar CPF (Garante que tem 11 números)
  const cpfDigits = form.cpf.replace(/\D/g, '');
  if (cpfDigits.length !== 11) {
    validateCPF(); // Chama para mostrar a mensagem de erro no input
    showModal('Erro de Validação', 'O CPF deve ser preenchido completamente.', 'error');
    return;
  }

  // 2. Verificação de segurança: Pelo menos um endereço
  if (form.addresses.length === 0) {
    showModal('Atenção', 'O usuário deve possuir pelo menos um endereço cadastrado.', 'error');
    return;
  }

  // 3. Validar todos os CEPs (Garante que todos têm 8 números)
  for (let i = 0; i < form.addresses.length; i++) {
    const cepDigits = form.addresses[i].cep.replace(/\D/g, '');
    if (cepDigits.length !== 8) {
      validateCEP(i); // Mostra o erro no input específico
      showModal('Erro de Validação', `O CEP do endereço ${i + 1} está incompleto.`, 'error');
      return; // Interrompe o salvamento
    }
  }

  // 4. Se passou em todas as validações, prossegue para a API
  try {
    if (isEditing.value) {
      await api.put(`/usuarios/${route.params.id}`, form); 
      showModal('Concluído', "Usuário Atualizado com Sucesso", 'success');
    } else {
      await api.post('/usuarios', form);
      showModal('Concluído', "Usuário Cadastrado com Sucesso", 'success');
    }
    // Opcional: router.push('/') após o sucesso
  } catch (error) {
    if (error.response?.status === 422) {
      const errorsBackend = error.response.data.errors;
      const msg = errorsBackend.cpf?.[0] || errorsBackend.email?.[0] || "Erro de validação.";
      showModal('Erro', msg, 'error');
    } else {
      showModal('Erro', "Ocorreu um erro inesperado ao salvar.", 'error');
    }
  }
};

const validateCPF = () => {
  const digits = form.cpf.replace(/\D/g, '');
  if (digits.length > 0 && digits.length < 11) {
    errors.cpf = 'O CPF deve conter 11 números.';
  } else {
    errors.cpf = ''; // Limpa o erro se estiver correto ou vazio
  }
};

const validateCEP = (index) => {
  const addr = form.addresses[index];
  const digits = addr.cep.replace(/\D/g, '');
  if (digits.length > 0 && digits.length < 8) {
    addr.error = 'O CEP deve conter 8 números.';
  } else {
    addr.error = '';
  }
};

onMounted(async () => {
  // Carrega perfis para o select
  const respProfiles = await api.get('/perfis');
  profiles.value = respProfiles.data.data;

  // Se for edição, carrega os dados do usuário 
  if (isEditing.value) {
    const respUser = await api.get(`/usuarios/${route.params.id}`);
    Object.assign(form, respUser.data);
  }
});
</script>
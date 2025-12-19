<template>
  <div 
    v-if="isOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4"
  >
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md transform transition-all" @click.stop>
      <div class="p-6 text-center">
        <div 
          :class="[
            'mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4',
            type === 'success' ? 'bg-green-100' : 'bg-red-100'
          ]"
        >
          <svg v-if="type === 'success'" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
          
          <svg v-else class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
          </svg>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
          {{ title }}
        </h3>
        
        <p class="text-sm text-gray-500">
          {{ message }}
        </p>
      </div>

      <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-center">
        <button 
          @click="handleClose"
          :class="[
            'w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none',
            type === 'success' ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500'
          ]"
        >
          {{ type === 'success' ? 'Continuar' : 'Entendido' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const emit = defineEmits(['close']);
const router = useRouter();

const props = defineProps({
  isOpen: Boolean,
  title: String,
  message: String,
  type: {
    type: String,
    default: 'success'
  }
});

const handleClose = () => {
  emit('close'); // Fecha o modal no componente pai
  
  if (props.type === 'success') {
    router.push('/'); // Redireciona para a home
  }
};
</script>
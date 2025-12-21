<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-0"
    >
      <div
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"
        @click="handleCancel"
      ></div>

      <div
        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all w-full sm:my-8 sm:max-w-lg"
        @click.stop
      >
        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div
              class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10"
            >
              <svg
                class="h-6 w-6 text-yellow-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"
                />
              </svg>
            </div>

            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
              <h3
                class="text-lg font-semibold leading-6 text-gray-900"
                id="modal-title"
              >
                {{ title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  {{ message }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
          <button
            type="button"
            @click="handleConfirm"
            :class="[
              'w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto',
              intent === 'danger'
                ? 'bg-red-600 hover:bg-red-500'
                : 'bg-blue-600 hover:bg-blue-500',
            ]"
          >
            {{ confirmText }}
          </button>

          <button
            type="button"
            @click="handleCancel"
            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
          >
            {{ cancelText }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
// Definimos os eventos que este componente pode emitir para o pai
const emit = defineEmits(['cancel', 'confirm']);

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: 'Confirmar ação',
  },
  message: {
    type: String,
    default: 'Tem certeza que deseja prosseguir com esta operação?',
  },
  // Texto do botão de aceitar
  confirmText: {
    type: String,
    default: 'Confirmar',
  },
  // Texto do botão de cancelar
  cancelText: {
    type: String,
    default: 'Cancelar',
  },
  // Define o estilo do botão de confirmação: 'primary' (azul) ou 'danger' (vermelho)
  intent: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'danger'].includes(value),
  },
});

// Manipuladores de clique
const handleCancel = () => {
  emit('cancel');
};

const handleConfirm = () => {
  emit('confirm');
};
</script>
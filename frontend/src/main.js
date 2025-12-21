import { createApp } from 'vue'
import './style.css' // Certifique-se que o Tailwind está aqui
import App from './App.vue'
import router from './router/index' // Importa o arquivo de rotas que criamos
import VueTheMask from 'vue-the-mask'

const app = createApp(App)

app.use(router) // Ativa o sistema de rotas
app.use(VueTheMask)
app.mount('#app')
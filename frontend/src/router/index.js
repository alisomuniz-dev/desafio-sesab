import { createRouter, createWebHistory } from 'vue-router';
import UserList from '../views/UserList.vue';
import UserForm from '../views/UserForm.vue';
import ProfileList from '../views/ProfileList.vue';
import ProfileForm from '../views/ProfileForm.vue';

const routes = [
  { 
    path: '/', 
    name: 'Home', 
    component: UserList 
  },
  { 
    path: '/usuarios/cadastro', 
    name: 'Cadastro', 
    component: UserForm 
  },
  { 
    path: '/usuarios/editar/:id', 
    name: 'Editar', 
    component: UserForm, 
    props: true 
  },
  { 
    path: '/perfis', 
    name: 'Perfis', 
    component: ProfileList, 
  },
  { 
    path: '/perfis/cadastro', 
    name: 'PerfilCadastro', 
    component: ProfileForm, 
  },
  { 
    path: '/perfis/editar/:id', 
    name: 'PerfilEditar', 
    component: ProfileForm, 
    props: true 
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
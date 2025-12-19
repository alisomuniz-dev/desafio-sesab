import { createRouter, createWebHistory } from 'vue-router';
import UserList from '../views/UserList.vue';
import UserForm from '../views/UserForm.vue';

const routes = [
  { 
    path: '/', 
    name: 'Home', 
    component: UserList 
  },
  { 
    path: '/cadastro', 
    name: 'Cadastro', 
    component: UserForm 
  },
  { 
    path: '/editar/:id', 
    name: 'Editar', 
    component: UserForm, 
    props: true 
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
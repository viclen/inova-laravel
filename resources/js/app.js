require('./bootstrap');

import 'vue-select/dist/vue-select.css';

window.Vue = require('vue');

Vue.component('tabela-acoes', require('./components/TabelaAcoes.vue').default);
Vue.component('form-padrao', require('./components/FormPadrao.vue').default);
Vue.component('test-api', require('./components/TestAPI.vue').default);
Vue.component('regras-editor', require('./components/RegrasEditor.vue').default);
Vue.component('ver-dados', require('./components/VerDados.vue').default);
Vue.component('cadastro-interesse', require('./components/CadastroInteresse.vue').default);
Vue.component('caracteristica-input', require('./components/CaracteristicaInput.vue').default);

import Toasted from 'vue-toasted';
import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue)
Vue.use(Toasted);

import vSelect from "vue-select";
Vue.component("v-select", vSelect);

import draggable from 'vuedraggable';
Vue.component("draggable", draggable);

// font awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas)
Vue.component('fa-icon', FontAwesomeIcon)

const app = new Vue({
    el: '#app',
});

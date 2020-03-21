require('./bootstrap');

window.Vue = require('vue');

Vue.component('tabela-acoes', require('./components/TabelaAcoes.vue').default);
Vue.component('form-padrao', require('./components/FormPadrao.vue').default);
Vue.component('test-api', require('./components/TestAPI.vue').default);

import Toasted from 'vue-toasted';
import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue)
Vue.use(Toasted);

// font awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas)
Vue.component('fa-icon', FontAwesomeIcon)

const app = new Vue({
    el: '#app',
});

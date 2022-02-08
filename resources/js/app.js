import Vue from 'vue';
import VueRouter from 'vue-router';

import Index from './components/pages/Index';
//views
Vue.component('app-view', require('./components/App.vue').default);
Vue.component('app-login', require('./components/pages/Login.vue').default);
Vue.component('app-company-create', require('./components/pages/companies/Create.vue').default);
Vue.component('app-company-select', require('./components/pages/companies/Select.vue').default);

Vue.use(VueRouter);

window.axios = require('axios');

axios.defaults.headers.common['Accept'] = 'application/json' // for GET requests


const router = new VueRouter({
    mode: 'history',
    base: '/factura',
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Index
        },
        {
            path: '/company/create',
            name: 'CreateCompany',
            component: require('./components/pages/companies/Create.vue')
        }
        ,
        {
            path: '/company/select',
            name: 'SelectCompany',
            component: require('./components/pages/companies/Select.vue')
        }
    ],
});

const app = new Vue({
    el: '#app',
    router,
    data: () => ({
        adv: []
    }),
    created() {
        const t = this;
        t.getAPIData();
    },
    mounted() {

    },
    watch: {
        adv: {
            handler(newVal, oldVal) {
                // console.log(`adv data changed `, newVal);
            },
            immediate: true
        }
    },
    methods: {
        swalAlert: function(type, text) {
            if(text == 'The given data was invalid.')
              text = "Va rugam sa completati toate campurile!";
            Swal.fire({
                position: 'top-end',
                icon: type,
                title: text,
                showConfirmButton: false,
                timer: 3000
            })
        },
        getAPIData: async function() {
          let t = this;
          await axios.get(_PAGE_URL + '/api').then(function(response) {
            t.adv = response.data;
          });
          return t.adv;
        }
    }
});
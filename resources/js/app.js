import Vue from 'vue';
import VueRouter from 'vue-router';


//views
Vue.component('app-view', require('./components/App.vue').default);


//pages
import Index from './components/Index.vue';
import Login from './components/User/Login.vue';
import CompanyCreate from './components/Company/Create.vue';
import CompanySelect from './components/Company/Select.vue';

Vue.use(VueRouter);

window.axios = require('axios');


// axios.defaults.headers.post['Content-Type'] = 'application/json' // for POST requests
axios.defaults.headers.common['Accept'] = 'application/json' // for GET requests


const router = new VueRouter({
    mode: 'history',
    base: '/factura',
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
              requiresAuth: false,
              requiresCompany: false,
            }
        },
        {
            path: '/',
            name: 'Home',
            component: Index,
            meta: {
              requiresAuth: true,
              requiresCompany: true
            }
        },
        {
          path: '/company/create',
          name: 'company.create',
          component: CompanyCreate,
          meta: {
            requiresAuth: true,
            requiresCompany: false
          }
        },
        {
          path: '/company/select',
          name: 'company.select',
          component: CompanySelect,
          meta: {
            requiresAuth: true,
            requiresCompany: false
          }
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
        // get data from back-end about url, token, auth data
        const t = this;
        axios.get(_PAGE_URL + '/api').then(function(response) {
          t.adv = response.data;
          const adv = t.adv;
          console.log(t.adv);
        }).then(function() {
          if(t.adv.auth.check == false)
          {
            t.$router.push({name: 'login'}).catch((e)=>{
              // console.log(e)
            });
          } else if(t.adv.company == null && (t.$route.path != '/company/select' && t.$route.name != '/company/create')) {
            t.$router.push({path: 'company/create'});
          } else {

          }
        });
        router.beforeEach((to, from, next) => {
          console.log('page changed', to.path);
          if(t.adv && t.adv.auth.check == false && to.name != 'login')
            next({path: '/login'});
          else if(t.adv && t.adv.auth.check == true && t.adv.company == null && (to.path != '/company/select' && to.path != '/company/create'))
          {
            next({path: '/company/create'});
          } else {
            next();
          }
        }); 
    },
    mounted() {

    },
    watch: {
        adv: {
            handler(newVal, oldVal) {
                console.log(`adv data changed `, newVal);
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
        getAPIData: function() {
          let t = this;
          axios.get(_PAGE_URL + '/api').then(function(response) {
            t.adv = response.data;
          });
        }
    }
});
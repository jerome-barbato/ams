import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import VueAxios from 'vue-axios'

axios.defaults.baseURL = '//ams-gp.fr';
Vue.config.productionTip = false;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);


import MilitantsDashboard from './components/MilitantsDashboard.vue';
import GroupsDashboard from './components/GroupsDashboard.vue';

const routes = [
	{ path: '/militants', component: MilitantsDashboard },
	{ path: '/groups', component: GroupsDashboard },
];

const router = new VueRouter({
	mode: 'history',
	routes
});

new Vue({
	router,
	render: h => h(MilitantsDashboard)
}).$mount('#app');
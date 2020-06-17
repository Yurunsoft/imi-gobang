import Vue from 'vue'
import App from './App.vue'
import router from './router'
import global from '@/global'

Vue.config.productionTip = false

Vue.prototype.GLOBAL = global

global.userInfo = window.localStorage['userInfo']

new Vue({
  router,
  render: function (h) { return h(App) }
}).$mount('#app')

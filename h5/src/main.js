import Vue from 'vue'
import App from './App.vue'
import router from './router'
import global from '@/global'

Vue.config.productionTip = false

Vue.prototype.GLOBAL = global

if(window.localStorage['userInfo'])
{
  global.userInfo = JSON.parse(window.localStorage['userInfo'])
}

new Vue({
  router,
  render: function (h) { return h(App) }
}).$mount('#app')

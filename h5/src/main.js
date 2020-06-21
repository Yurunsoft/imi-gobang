import Vue from 'vue'
import App from './App.vue'
import router from './router'
import global from '@/global'

Vue.config.productionTip = false

Vue.prototype.GLOBAL = global

if(window.localStorage['userInfo'])
{
  try {
    global.userInfo = JSON.parse(window.localStorage['userInfo'])
  } catch (error) {
    console.error(error)
  }
}

new Vue({
  router,
  render: function (h) { return h(App) }
}).$mount('#app')

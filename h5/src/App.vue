<template>
  <div id="app">
    <div class="bg1"></div>
    <div class="bg-river"></div>
    <router-view class="router-view"/>
  </div>
</template>

<style lang="less">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  /* text-align: center;
  color: #2c3e50; */
  padding: 0 16px;
  background-color: #1B6353;
  height: 100vh;
  .router-view{
    z-index: 2;
    position: relative;
  }
}
/* #nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
} */
</style>

<script>
import "normalize.css/normalize.css"
export default {
  
}
</script>
<template>
  <div class="home">
    <div class="center">
      <img alt="Vue logo" src="../assets/logo.png" />
    </div>

    <h1>五子棋在线对战</h1>

    <div id="start">
      <div v-if="logined">
        <p>{{GLOBAL.userInfo.username}}</p>
        <a id="btn-start" @click="start">开始游戏</a>
      </div>
      <template v-else>
        <div><input id="input-username" type="text" placeholder="请输入昵称" v-model="username"/></div>
        <div><input id="input-password" type="password" placeholder="请输入密码" v-model="password"/></div>
        <div>
          <a id="btn-login" @click="login">登录</a>
        </div>
        <div>
          <a id="btn-register" @click="register">注册</a>
        </div>
      </template>
    </div>

    <p>基于 Vue 和 imi 分别开发前后端。</p>
    <p>本教程主要着重于后端，所以前端简陋勿怪~</p>
    <p>
      视频教程免费连载更新地址：
      <a href="https://space.bilibili.com/768718" target="_blank">https://space.bilibili.com/768718</a>
    </p>
    <h2>技术架构</h2>

    <h3>后端</h3>
    <p>
      <a href="https://www.imiphp.com">imi 框架</a> (Http + WebSocket)
    </p>
    <p>
      <a href="https://www.swoole.com">Swoole</a> (imi 框架基于 Swoole 开发)
    </p>

    <h3>前端</h3>
    <p>Vue + Less</p>
  </div>
</template>

<script>
// @ is an alias to /src
import request from '@/plugin/axios'
export default {
  name: "home",
  components: {
  },
  data() {
    return {
      logined: false,
      username: '',
      password: '',
    };
  },
  mounted() {
    this.loadStatus()
  },
  methods: {
    // 加载登录状态
    loadStatus(){
      request({
        url: '/member/status',
        excludeCodes: [1001],
      }).then((result) => {
        this.GLOBAL.userInfo = result.data;
        this.logined = 0 === result.code;
      })
    },
    // 注册
    register(){
      request({
        method: 'POST',
        url: '/member/register',
        data: {
          username: this.username,
          password: this.password,
        }
      }).then((result) => {
        if(0 === result.code)
        {
          this.password = '';
          alert('注册成功，请再次输入密码登录');
        }
      })
    },
    // 登录
    login(){
      request({
        method: 'POST',
        url: '/member/login',
        data: {
          username: this.username,
          password: this.password,
        }
      }).then((result) => {
        if(this.logined = 0 === result.code)
        {
          window.localStorage['sessionId'] = result.token
        }
        this.loadStatus();
        alert('登录成功');
      })
    },
    // 开始游戏
    start() {

      // this.$router.push({name:'rooms'});
    },
  }
};
</script>

<style lang="less" scoped>
@import "../style/style.less";
h1 {
  margin-top: 0;
}
h1,
h2,
h3 {
  text-align: center;
}
#start:extend(.center) {
  margin: 36px 0;
  #input-username, #input-password{
    border-color: #e6e6e6;
    height: 38px;
    line-height: 1.3;
    line-height: 38px\9;
    border-width: 1px;
    border-style: solid;
    background-color: #fff;
    border-radius: 2px;
    display: block;
    width: 100%;
    padding-left: 10px;
    box-sizing: border-box;
    width: 250px;
    max-width: 100%;
    margin: 0 auto;
    margin-bottom: 6px;
  }
  #btn-login,#btn-register,#btn-start {
    display: inline-block;
    text-decoration: none;
    border-radius: 8px;
    padding: 8px 24px;
    font-size: 24px;
    background: #1e9fff;
    margin-top: 16px;
    color: #fff;
  }
}
</style>
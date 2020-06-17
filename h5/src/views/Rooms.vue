<template>
  <div class="rooms">
    <p class="hello" v-if="GLOBAL.userInfo">
      <img class="logined-thumb" src="../assets/thumb.png"/>
      你好，{{GLOBAL.userInfo.username}}！
    </p>

    <div>
      <h1>房间列表</h1>
      <button id="btn-create-room" @click="openCreateRoom"></button>
    </div>

    <!-- 房间列表 -->
    <ul class="room-list">
      <li v-for="(item, index) in rooms" :key="index">
        <div>
          <span class="title">{{item.title}}</span>
        </div>

        <div class="username">房主：{{item.creator.username}}</div>

        <div class="box1">
          <span class="person">人数：({{item.person}}/2)</span>
          <div class="room-btn-box">
            <a class="join" v-if="item.person < 2" @click="join(item)">加入</a>
            <a class="watch" @click="watch(item)">观战</a>
          </div>
        </div>
      </li>
    </ul>

    <!-- 聊天 -->
    <chat room="rooms" class="chat-box" :rows="5" style="margin-top:16px"></chat>

    <!-- 创建房间层 -->
    <div v-if="showCreateRoomLayer">
      <div class="layer-mask" @click="closeCreateRoom"></div>
      <div id="create-room-layer">
        <p class="layer-title">创建房间</p>
        <a class="btn-close-create-room" @click="closeCreateRoom"></a>
        <div><input id="input-room-name" class="input" type="text" placeholder="请输入房间名称" v-model="roomName"/></div>
        <div>
          <a class="big-btn" id="btn-submit-create-room" @click="createRoom">创建</a>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Chat from "@/components/Chat.vue";
import global from '../global';
export default {
  components: {
    Chat,
  },
  data() {
    return {
      rooms: [
        // 测试数据：
        // {
        //   "title": "快来人！！！快来人！！！快来人！！！快来人！！！", // 标题
        //   "creator": "测试昵称", // 创建者
        //   "person": 1, // 人数
        //   "status": 1, // 状态
        //   "statusText": "等待中", // 状态文本
        // },
        // {
        //   "title": "快来人！！！",
        //   "creator": "测试昵称",
        //   "person": 2,
        //   "status": 2,
        //   "statusText": "对弈中",
        // }
      ],
      showCreateRoomLayer: false,
      roomName: '',
    };
  },
  mounted(){
    if(!this.GLOBAL.userInfo)
    {
      this.$router.replace("/")
      return;
    }
    try {
      this.GLOBAL.websocketConnection.open(process.env.VUE_APP_GAME_WEBSOCKET_URL, ()=>{
        this.loadRoomList();
      });
    } catch(e) {
      alert(e)
    }
    this.GLOBAL.websocketConnection.onAction('room.list', this.onRoomList)
    this.GLOBAL.websocketConnection.onAction('room.create', this.onCreateRoom)
    this.GLOBAL.websocketConnection.onAction('room.join', this.onJoinRoom)
    this.GLOBAL.websocketConnection.onAction('room.watch', this.onWatchRoom)
    this.GLOBAL.websocketConnection.onClose((evt)=>{
      alert('断线了')
      location.reload()
    })
  },
  methods: {
    // 加载房间列表
    loadRoomList(){
      this.GLOBAL.websocketConnection.sendEx('room.list');
    },
    // 打开创建房间窗口
    openCreateRoom(){
      this.showCreateRoomLayer = true;
    },
    // 关闭创建房间窗口
    closeCreateRoom(){
      this.showCreateRoomLayer = false;
    },
    // 创建房间
    createRoom(){
      this.GLOBAL.websocketConnection.sendEx('room.create', {
        title: this.roomName,
      });
    },
    // 加入
    join(room){
      this.GLOBAL.websocketConnection.sendEx('room.join', {
        roomId: room.roomId,
      });
    },
    // 观战
    watch(room){
      this.GLOBAL.websocketConnection.sendEx('room.watch', {
        roomId: room.roomId,
      });
    },
    // 房间列表回调
    onRoomList(data){
      this.rooms = data.list;
    },
    // 创建房间回调
    onCreateRoom(data){
      this.$router.replace({
        name: 'gobang',
        params: {
          roomInfo: data.roomInfo,
        },
      });
    },
    // 加入房间回调
    onJoinRoom(data){
      this.$router.replace({
        name: 'gobang',
        params: {
          roomInfo: data.roomInfo,
        },
      });
    },
    // 观战回调
    onWatchRoom(data){
      this.$router.replace({
        name: 'gobang',
        params: {
          roomInfo: data.roomInfo,
          watchMode: true,
        },
      });
    },
  },
};
</script>

<style lang="less" scoped>
h1{
  display: inline;
  line-height: 40px;
  font-size: 22px;
}
.rooms {
  height: 100vh;
  display: flex;
  align-content: flex-start;
  flex-direction:column;
  .hello{
    color: #fff;
    font-size: 20px;
  }
  .logined-thumb{
    width: 36px;
    height: 36px;
    vertical-align: middle;
    padding-right: 8px;
    padding-bottom: 6px;
  }
  .chat-box{
    margin-bottom: 8px;
  }
  #btn-create-room {
    display: inline-block;
    background-color:linear-gradient(-18deg,rgba(233,236,243,1) 0%,rgba(255,255,255,1) 100%);
    border:none;
    border-radius:30px;
    width: 100px;
    line-height: 40px;
    height: 40px;
    float: right;
    &::after{
      content: ' ';
      display: block;
      background-image: url(../assets/create-room.png);
      background-size: 90%;
      background-position: center;
      background-repeat: no-repeat;
      width: 100%;
      height: inherit;
    }
  }
  .room-list{
    flex: auto;
    font-size: 18px;
    overflow: auto;
    padding: 0;
    margin-bottom: 0;
    li{
      background:rgba(243,244,248,0.5);
      border:1px solid rgba(255,255,255,1);
      border-radius:20px;
      padding: 16px 18px;
      position: relative;
      margin-bottom: 12px;
      .title{
        display:inline-block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        vertical-align: middle;
        width: -webkit-fill-available;
        color: #0D3129;
        font-size: 24px;
        font-weight:bold;
      }
      .username{
        margin-top: 20px;
        color: #323F49;
      }
      .box1{
        margin-top: 20px;
        color: #323F49;
      }
      .person{
        margin-right: 1em;
      }
      .join,.watch{
        background:rgba(243,244,248,1);
        border-radius:30px;
        width: 92px;
        line-height: 36px;
        text-align: center;
      }
      .join{
        display: inline-block;
        text-decoration: none;
        color: #43BB43;
        margin-right: 4px;
      }
      .watch{
        display: inline-block;
        text-decoration: none;
        color: #1E9FFF;
      }
      .room-btn-box{
        float:right;
        margin-top: -10px;
      }
    }
  }
}
#create-room-layer{
  background-color: #fff;
  border-radius:30px;
  width: 500px;
  height: 260px;
  max-width: 100%;
  position:absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 0 40px 0 40px;
  box-sizing: border-box;
  .layer-title{
    color:#323F49;
    font-size: 22px;
    text-align: center;
    margin-bottom: 0;
  }
  .btn-close-create-room{
    display: block;
    width: 16px;
    height: 16px;
    background-image: url(../assets/close.png);
    background-size: cover;
    position: absolute;
    right: 20px;
    top: 20px;
  }
  #input-room-name{
    margin-top: 28px;
  }
  #btn-submit-create-room{
    margin-top: 10px;
  }
}
</style>
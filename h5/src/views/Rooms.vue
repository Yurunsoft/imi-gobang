<template>
  <div class="rooms">
    <p v-if="GLOBAL.userInfo">你好，{{GLOBAL.userInfo.username}}!</p>

    <div>
      <a id="btn-create-room" @click="createRoom">创建房间</a>
    </div>

    <p>房间列表：</p>
    <ul class="room-list">
      <li v-for="(item, index) in rooms" :key="index">
        <div>
          <span class="title">{{item.title}}</span>
        </div>

        <div class="username">房主：{{item.creator.username}}</div>

        <div>
          <span class="person">人数：({{item.person}}/2)</span>
          <a class="join" v-if="item.person < 2" @click="join(item)">加入</a>
          <a class="watch" @click="watch(item)">观战</a>
        </div>
      </li>
    </ul>

    <chat room="rooms" class="chat-box"></chat>

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
  },
  methods: {
    // 加载房间列表
    loadRoomList(){
      this.GLOBAL.websocketConnection.sendEx('room.list');
    },
    // 创建房间
    createRoom(){
      this.GLOBAL.websocketConnection.sendEx('room.create', {
        title: 'test',
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
.rooms {
  height: 100vh;
  display: flex;
  // flex-flow: column;
  align-content: flex-start;
  flex-direction:column;
  .chat-box{
    flex: auto;
  }
  #btn-create-room {
    display: inline-block;
    text-decoration: none;
    border-radius: 8px;
    padding: 8px 24px;
    font-size: 24px;
    background: #1e9fff;
    margin-top: 16px;
    color: #fff;
  }
  .room-list{
    flex: auto;
    font-size: 18px;
    padding: 1em;
    overflow: auto;
    li{
      margin-bottom: 16px;
      line-height: 32px;
    }
    .title{
      display:inline-block;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      vertical-align: middle;
      width: -webkit-fill-available;
    }
    .person{
      margin-right: 1em;
    }
    .join{
      display: inline-block;
      text-decoration: none;
      border-radius: 4px;
      padding: 4px 8px;
      background: #1e9fff;
      color: #fff;
      margin-right: 4px;
      line-height: 24px;
    }
    .watch{
      display: inline-block;
      text-decoration: none;
      border-radius: 4px;
      padding: 4px 8px;
      background: #1e9fff;
      color: #fff;
      line-height: 24px;
    }
  }
}
</style>
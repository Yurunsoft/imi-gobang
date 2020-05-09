<template>
  <div class="chat">
    <textarea class="chat-content" readonly v-text="chatContent" ></textarea>
    <form class="input-area" @submit.prevent="sendContent">
      <input type="text" v-model="inputContent"/>
      <button>发送</button>
    </form>
  </div>
</template>

<script>
import WS from '../utils/ws';
export default {
  name: "Chat",
  props: {
    room: {
      type: String,
    },
    rows: {
      type: Number,
      default: 10,
    },
  },
  data() {
    return {
      // WebSocket 连接对象
      wsConn: null,
      // 聊天区内容
      chatContent: '',
      // 输入区内容
      inputContent: '',
    };
  },
  mounted() {
    this.wsConn = new WS
    this.wsConn.open(process.env.VUE_APP_IM_WEBSOCKET_URL, ()=>{
      this.wsConn.sendEx('im.joinRoom', {
        roomId: this.room,
      });
    });
    this.wsConn.onAction('im.receive', this.onReceive)
  },
  beforeDestroy(){
    this.wsConn.close();
  },
  methods: {
    // 发送内容
    sendContent(){
      if('' === this.inputContent)
      {
        return;
      }
      this.wsConn.sendEx('im.send', {
        content: this.inputContent,
        roomId: this.room,
      });
      this.inputContent = '';
    },
    // 接收内容
    onReceive(data){
      var content = data.sender + ": " + data.content + "\r\n";
      this.chatContent += content;
    },
  },
};
</script>

<style lang="less" scoped>
.chat {
  display: flex;
  // flex-flow: column;
  // align-content: flex-start;
  flex-direction:column;
  .chat-content{
    width: 100%;
    box-sizing: border-box;
    line-height: 24px;
    flex:auto;
  }
  .input-area{
    display: flex;
    flex-flow: row wrap;
    align-content: flex-start;
    flex-direction:row;
    input{
      flex: auto;
    }
  }
}
</style>

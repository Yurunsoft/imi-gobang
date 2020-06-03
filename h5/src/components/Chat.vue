<template>
  <div class="chat">
    <!-- <textarea class="chat-content" contentEditable="true" readonly v-text="chatContent" ></textarea> -->
    <ul ref="chatContent" class="chat-content" :style="chatContentStyle">
      <li v-for="(item, index) in chatRecords">
        <span class="sender"><span v-text="item.sender"></span>: </span>
        <span v-text="item.content"></span>
      </li>
    </ul>
    <form class="input-area" @submit.prevent="sendContent">
      <div class="send-input-box">
        <input type="text" v-model="inputContent"/>
        <button class="btn-send"></button>
      </div>
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
      // 输入区内容
      inputContent: '',
      // 聊天记录
      chatRecords: [],
      // 聊天内容区样式
      chatContentStyle: '',
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
    console.log(this.rows)
    this.chatContentStyle = 'height:' + (24 * this.rows + 20) + 'px';
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
      const chatContent = this.$refs.chatContent;
      console.log(chatContent.scrollHeight - chatContent.scrollTop, chatContent.clientHeight)
      const willScroll = chatContent.scrollHeight - chatContent.scrollTop === chatContent.clientHeight;
      this.chatRecords.push(data);
      if(willScroll)
      {
        this.$nextTick(function(){
          chatContent.scrollTop = chatContent.scrollHeight;
        })
      }
    },
  },
};
</script>

<style lang="less" scoped>
.chat {
  display: flex;
  flex-direction:column;
  .chat-content{
    width: 100%;
    box-sizing: border-box;
    // flex:auto;
    border:none;
    background:rgba(0,0,0,0.5);
    border-radius:8px;
    outline: none;
    padding: 10px;
    list-style: none;
    color: #fff;
    overflow: auto;
    margin-bottom: 6px;
    li{
      word-break: break-word;
      line-height: 24px;
      span{
        &.sender{
          color: #FFA81E;
        }
      }
    }
  }
  .input-area{
    .send-input-box{
      line-height: 54px;
      display: flex;
      flex-flow:row;
      border-radius:12px;
      background-color: #E9ECF3;
      input{
        flex: 1;
        width: 100%;
        line-height: 54px;
        outline: none;
        border:none;
        font-size: 24px;
        color: #323F49;
        border-radius:12px;
        background-color: #E9ECF3;
        box-sizing: border-box;
        padding-left: 20px;
        padding-right: 0;
      }
      .btn-send{
        width: 64px;
        border:none;
        outline: none;
        background-image: url(../assets/send.png);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 32px;
        border-radius:12px;
        background-color: #E9ECF3;
      }
    }
  }
}
</style>

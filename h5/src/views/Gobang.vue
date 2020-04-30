<template>
  <div>
    <template v-if="roomInfo">
      <p>[<span v-text="roomInfo.statusText"></span>]<span v-text="roomInfo.title"></span></p>
      <gobang :disable="gobang.disable"></gobang>
      <div id="player-info-box">
        <div>
          玩家1
        </div>
        <div>
          玩家2
        </div>
        <div id="player1-box">
          <p v-if="roomInfo.player1"><span v-text="roomInfo.player1.username"></span><span v-if="roomInfo.player1Ready" class="readyed">🙋</span></p>
          <p v-else>等待加入...</p>
        </div>
        <div id="player2-box">
          <p v-if="roomInfo.player2"><span v-text="roomInfo.player2.username"></span><span v-if="roomInfo.player2Ready" class="readyed">🙋</span></p>
          <p v-else>等待加入...</p>
        </div>
      </div>
      <!-- 等待开始 -->
      <div v-if="1 == roomInfo.status" class="center">
        <button v-if="isReady" @click="cancelReady">取消准备</button>
        <button v-else @click="ready">准备</button>
      </div>
    </template>
  </div>
</template>

<script>
import Gobang from "@/components/Gobang.vue";
export default {
  components: {
    Gobang,
  },
  data() {
    return {
      gobang: {
        disable: false,
      },
      roomInfo: null,
      isReady: false,
    };
  },
  mounted(){
    const params = this.$route.params;
    console.log(params)
    if(!params.roomInfo)
    {
      this.$router.replace('/');
      return;
    }
    this.roomInfo = params.roomInfo;
    this.GLOBAL.websocketConnection.onAction('room.info', this.onJoinInfo)
    this.GLOBAL.websocketConnection.onAction('room.ready', this.onRoomReady)
    this.GLOBAL.websocketConnection.onAction('room.cancelReady', this.onRoomCancelReady)
    this.GLOBAL.websocketConnection.onAction('gobang.resultNotify', this.onGobangResultNotify)
    console.log(params.roomInfo);
  },
  methods: {
    // 房间信息回调
    onJoinInfo(data){
      this.roomInfo = data.roomInfo;
      console.log(data.roomInfo)
    },
    // 准备
    ready(){
      this.GLOBAL.websocketConnection.sendEx('room.ready', {
        roomId: this.roomInfo.roomId,
      });
    },
    // 准备回调
    onRoomReady(data){
      this.isReady = true;
    },
    // 取消准备
    cancelReady(){
      this.GLOBAL.websocketConnection.sendEx('room.cancelReady', {
        roomId: this.roomInfo.roomId,
      });
    },
    // 取消准备回调
    onRoomCancelReady(data){
      this.isReady = false;
    },
    // 对战结果回调
    onGobangResultNotify(data){
      if(data.map)
      {

      }
      if(data.winner)
      {
        alert(data.winner.username + ' 赢啦！');
        this.isReady = false;
      }
    },
  },
};
</script>

<style lang="less" scoped>
.center{
  text-align: center;
}
.readyed{
  color: green;
  font-weight: bold;
}
#player-info-box{
  display: flex;
  // justify-content: space-between;
  flex-flow: row wrap;
  align-content: flex-start;
  div{
    flex: 0 0 50%;
  }
}
</style>

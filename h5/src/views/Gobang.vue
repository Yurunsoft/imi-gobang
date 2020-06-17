<template>
  <div class="gobang-box">
    <template v-if="roomInfo">
      <p class="title">房间<span v-text="roomInfo.title"></span></p>
      <div class="room-info-box">
        <div class="left-box">
          <p class="status" v-text="roomInfo.statusText"></p>
          <p>观战：{{roomInfo.watchMemberIds.length}}</p>
        </div>
        <div class="player-box fr">
          <div class="info-box fl">
            <p class="username text-right" v-text="playerOther.username"></p>
            <p class="ready-status text-right">
              <template v-if="playerOther.playerId">
                <span v-if="2 == roomInfo.status">对方颜色：{{playerOther.colorText}}</span>
                <span v-else-if="playerOther.ready">已准备</span>
                <span v-else>未准备</span>
              </template>
              <span v-else>等待加入</span>
            </p>
          </div>
          <img class="player-thumb fr" style="margin-left: 12px" src="../assets/thumb.png"/>
        </div>
      </div>
      <!-- 棋盘 -->
      <div class="gobang-area">
        <gobang ref="gobang" :disable="gobang.disable" v-on:go="onGo" :lastGoX="gameInfo.lastGoX" :lastGoY="gameInfo.lastGoY"></gobang>
        <img class="img-wait" src="../assets/wait.png" v-if="1 == roomInfo.status"/>
      </div>
      <div class="bottom-box">
        <div class="player-box fl">
          <div class="info-box fr">
            <p class="username" v-text="playerMine.username"></p>
            <p class="ready-status">
              <template v-if="playerMine.playerId">
                <span v-if="2 == roomInfo.status">你的颜色：{{playerMine.colorText}}</span>
                <span v-else-if="playerMine.ready">已准备</span>
                <span v-else>未准备</span>
              </template>
              <span v-else>等待加入</span>
            </p>
          </div>
          <img class="player-thumb fl" style="margin-right: 12px;margin-top: 14px;" src="../assets/thumb.png"/>
        </div>
        <div class="btn-box">
          <template v-if="1 == roomInfo.status" class="center">
            <button class="btn-cancel-ready" v-if="isReady" @click="cancelReady">取消准备</button>
            <button class="btn-ready" v-else @click="ready">准备</button>
          </template>
          <button class="btn-leave" @click="leave"></button>
        </div>
      </div>
    </template>
    <!-- 聊天 -->
    <chat class="chat-box" v-if="roomInfo" :room="roomInfo.roomId" :rows="3"></chat>
    <!-- 游戏结果 -->
    <div v-if="showGameResultLayer">
      <div class="layer-mask" @click="closeGameResultLayer"></div>
      <div id="create-room-layer">
        <div class="title">
          <img src="../assets/victory.png" v-if="youWin"/>
          <img src="../assets/defeat.png" v-else/>
        </div>
        <div class="win-box">
          <div class="left">
            <img class="player-thumb" src="../assets/thumb.png"/>
            <p class="username" v-text="playerMine.username"></p>
            <img v-if="youWin" class="right-top" src="../assets/win.png"/>
            <img v-else class="right-top" src="../assets/lose.png"/>
          </div>
          <div class="right">
            <img class="player-thumb" src="../assets/thumb.png"/>
            <p class="username" v-text="gameResultOtherUsername"></p>
            <img v-if="youWin" class="right-top" src="../assets/lose.png"/>
            <img v-else class="right-top" src="../assets/win.png"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Gobang from "@/components/Gobang.vue";
import Chat from "@/components/Chat.vue";
import piece from '../utils/piece';
export default {
  components: {
    Gobang,
    Chat,
  },
  data() {
    return {
      gobang: {
        disable: true,
      },
      roomInfo: null,
      gameInfo: {
        lastGoX: null,
        lastGoY: null,
      },
      isReady: false,
      watchMode: false,
      playerOther: {
        playerId: null,
        username: '-',
        ready: false,
        color: null,
        colorText: '',
      },
      playerMine: {
        playerId: null,
        username: '-',
        ready: false,
        color: null,
        colorText: '',
      },
      win: false,
      showGameResultLayer: false,
      youWin: false,
      gameResultOtherUsername: '',
    };
  },
  mounted(){
    const params = this.$route.params;
    if(!params.roomInfo)
    {
      this.$router.replace('/');
      return;
    }
    this.watchMode = !!params.watchMode;
    this.roomInfo = params.roomInfo;
    this.GLOBAL.websocketConnection.onAction('room.info', this.onJoinInfo)
    this.GLOBAL.websocketConnection.onAction('room.ready', this.onRoomReady)
    this.GLOBAL.websocketConnection.onAction('room.cancelReady', this.onRoomCancelReady)
    this.GLOBAL.websocketConnection.onAction('room.destory', this.onRoomDestory)
    this.GLOBAL.websocketConnection.onAction('room.leave', this.onLeave)
    this.GLOBAL.websocketConnection.onAction('gobang.info', this.onGobangInfo)
    this.GLOBAL.websocketConnection.sendEx('room.info', {
      roomId: this.roomInfo.roomId,
    });
  },
  methods: {
    // 房间信息回调
    onJoinInfo(data){
      this.roomInfo = data.roomInfo;
      this.updatePlayer();
      this.updateGobangDisable();
    },
    // 准备
    ready(){
      this.GLOBAL.websocketConnection.sendEx('room.ready', {
        roomId: this.roomInfo.roomId,
      });
    },
    // 离开房间
    leave(){
      this.GLOBAL.websocketConnection.sendEx('room.leave', {
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
    // 房间销毁回调
    onRoomDestory(data){
      alert('房间被销毁')
      this.$router.replace("/rooms")
    },
    // 对战信息回调
    onGobangInfo(data){
      if(data.game)
      {
        const game = data.game;
        this.gameInfo = game;
        this.$refs.gobang.setMap(game.gobangMap);
        this.updatePlayer();
        this.updateGobangDisable();
      }
      if(data.winner)
      {
        // alert(data.winner.username + ' 赢啦！');
        this.gameResultOtherUsername = this.playerOther.username;
        this.youWin = (data.winner.id === this.playerMine.playerId);
        this.openGameResultLayer();
        this.isReady = false;
      }
    },
    onLeave(data){
      this.$router.replace("/rooms")
    },
    updatePlayer(){
      if(!this.roomInfo)
      {
        return;
      }
      if(this.GLOBAL.userInfo.id === this.roomInfo.playerId1)
      {
        this.playerMine.playerId = this.roomInfo.playerId1;
        this.playerOther.playerId = this.roomInfo.playerId2;

        this.playerMine.username = this.roomInfo.player1.username;
        if(this.roomInfo.player2)
        {
          this.playerOther.username = this.roomInfo.player2.username;
        }
        else
        {
          this.playerOther.username = '';
        }

        this.playerMine.ready = this.roomInfo.player1Ready;
        this.playerOther.ready = this.roomInfo.player2Ready;

        if(this.gameInfo)
        {
          this.playerMine.color = this.gameInfo.player1Color;
          this.playerOther.color = this.gameInfo.player2Color;
        }
      }
      else if(this.GLOBAL.userInfo.id === this.roomInfo.playerId2)
      {
        this.playerMine.playerId = this.roomInfo.playerId2;
        this.playerOther.playerId = this.roomInfo.playerId1;

        this.playerMine.username = this.roomInfo.player2.username;
        if(this.roomInfo.player1)
        {
          this.playerOther.username = this.roomInfo.player1.username;
        }
        else
        {
          this.playerOther.username = '';
        }

        this.playerMine.ready = this.roomInfo.player2Ready;
        this.playerOther.ready = this.roomInfo.player1Ready;

        if(this.gameInfo)
        {
          this.playerMine.color = this.gameInfo.player2Color;
          this.playerOther.color = this.gameInfo.player1Color;
        }
      }
      else
      {
        this.playerMine.color = this.playerOther.color = null;
      }
      // 颜色文字
      switch(this.playerMine.color)
      {
        case piece.BLACK_PIECE:
          this.playerMine.colorText = '黑';
          break;
        case piece.WHITE_PIECE:
          this.playerMine.colorText = '白';
          break;
        default:
          this.playerMine.colorText = '';
          break;
      }
      switch(this.playerOther.color)
      {
        case piece.BLACK_PIECE:
          this.playerOther.colorText = '黑';
          break;
        case piece.WHITE_PIECE:
          this.playerOther.colorText = '白';
          break;
        default:
          this.playerOther.colorText = '';
          break;
      }
      if(this.$refs.gobang)
      {
        this.$refs.gobang.setCurrentPiece(this.playerMine.color)
      }
    },
    updateGobangDisable(){
      if(this.gameInfo)
      {
        this.gobang.disable = 1 === this.roomInfo.status || !(this.gameInfo.currentPiece === this.playerMine.color)
      }
    },
    onGo(point){
      this.gobang.disable = true;
      this.GLOBAL.websocketConnection.sendEx('gobang.go', {
        roomId: this.roomInfo.roomId,
        x: point.x,
        y: point.y,
      });
    },
    openGameResultLayer(win){
      this.win = win;
      this.showGameResultLayer = true;
    },
    closeGameResultLayer(){
      this.showGameResultLayer = false;
    },
  },
};
</script>

<style lang="less" scoped>
.gobang-box{
  height: 100vh;
  display: flex;
  // flex-flow: column;
  align-content: flex-start;
  flex-direction:column;
  .chat-box{
    flex: auto;
    margin-bottom: 6px;
  }
}
.title{
  margin: 14px 0 0 0;
  color: #fff;
  font-weight: bold;
  font-size: 20px;
}
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
.button-box{
  button{
    margin: 0 4px;
  }
}
.room-info-box{
  color: #fff;
  .left-box{
    float:left;
    .status{
      color: #FFEA00;
    }
  }
  p{
    margin: 10px 0;
  }
}
.player-box{
  color: #fff;
  .ready-status{
    color: #DEDEDE;
  }
  .player-thumb{
    width: 56px;
    height: 56px;
    margin-top: 4px;
  }
  .username{
    font-weight: bold;
  }
}
.btn-box{
  float:right;
  display:flex;
  padding-top: 18px;
}
.btn-ready, .btn-cancel-ready{
  background: #f3f4f8;
  border:none;
  outline: none;
  border-radius: 30px;
  width: 122px;
  line-height: 48px;
  text-align: center;
  font-size: 20px;
}
.btn-ready{
  color: #43BB43;
}
.btn-cancel-ready{
  color: #F24242;
}
.btn-leave{
  border:none;
  outline: none;
  border-radius: 8px;
  width: 50px;
  height: 50px;
  background-image: url(../assets/leave.png);
  background-position: center center;
  background-size: 32px;
  background-repeat: no-repeat;
  margin-left: 10px;
}
#create-room-layer{
  background-color: #fff;
  border-radius:30px;
  width: 500px;
  max-width: 100%;
  position:absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  .title{
    margin: 0;
    padding: 18px 55px;
    img{
      max-width: 100%;
    }
  }
  .win-box{
    display: flex;
    text-align: center;
    height: 164px;
    .left{
      width: 50%;
      background:rgba(233,236,243,1);
      border-radius:0px 0px 0px 30px;
      color: #323F49;
      font-weight: bold;
      position: relative;
    }
    .right{
      width: 50%;
      background:rgba(74,82,99,1);
      border-radius:0px 0px 30px 0px;
      color: #fff;
      font-weight: bold;
      position: relative;
    }
    .player-thumb{
      width: 56px;
      height: 56px;
      margin-top: 40px;
      border: 1px solid rgba(0, 0, 0, 0.25);
      border-radius: 100%;
    }
    .right-top{
      width: 60px;
      position: absolute;
      top: 0;
      right: 0;
    }
  }
}
.gobang-area{
  position: relative;
  .img-wait{
    position:absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 284px;
  }
}
</style>

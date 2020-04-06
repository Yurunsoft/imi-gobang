<template>
  <div class="rooms">
    <p>你好，{{GLOBAL.nickName}}!</p>

    <div>
      <a id="btn-create-room">创建房间</a>
    </div>

    <p>房间列表：</p>
    <ul class="room-list">
      <li v-for="(item, index) in rooms" :key="index">
        <div>
          <span class="title">{{item.title}}</span>
        </div>

        <div class="nickname">房主：{{item.creator}}</div>

        <div>
          <span class="person">人数：({{item.person}}/2)</span>
          <a class="join" v-if="item.person < 2" @click="join(item)">加入</a>
          <a class="watch" @click="watch(item)">观战</a>
        </div>
      </li>
    </ul>

  </div>
</template>

<script>
export default {
  data() {
    return {
      rooms: [
        // 测试数据：
        {
          "title": "快来人！！！快来人！！！快来人！！！快来人！！！", // 标题
          "creator": "测试昵称", // 创建者
          "person": 1, // 人数
          "status": 1, // 状态
          "statusText": "等待中", // 状态文本
        },
        {
          "title": "快来人！！！",
          "creator": "测试昵称",
          "person": 2,
          "status": 2,
          "statusText": "对弈中",
        }
      ],
    };
  },
  mounted(){

  },
  methods: {
    // 加入
    join(room){

    },
    // 观战
    watch(room){

    },
  },
};
</script>

<style lang="less" scoped>
.rooms {
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
    font-size: 18px;
    padding: 1em;
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
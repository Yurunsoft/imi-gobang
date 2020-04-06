<template>
  <div class="gobang">
    <canvas class="gobang-canvas" ref="canvas"></canvas>
  </div>
</template>

<script>
export default {
  name: "Gobang",
  props: {
    // 棋盘横向线条数
    width: {
      type: Number,
      default: 15
    },
    // 棋盘竖向线条数
    height: {
      type: Number,
      default: 15
    }
  },
  data() {
    return {};
  },
  mounted() {
    this.paint()
  },
  methods: {
    paint() {
      Promise.all([
        this.loadImage(require("@/assets/bg.jpg")),
      ]).then(imgs => {
        console.log(imgs)
        const context = this.$refs.canvas.getContext("2d");
        // 绘制底图
        context.drawImage(imgs[0], 0, 0, 800, 600)
        // 绘制横向线条
        for (let i = 0; i < this.width; ++i) {}
        // 绘制竖向线条
        for (let i = 0; i < this.height; ++i) {}
        // 绘制棋子
      });
    },
    loadImage(url) {
      return new Promise(resolve => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.src = url;
      });
    }
  }
};
</script>

<style lang="less" scoped>
.gobang {
  .gobang-canvas {
  }
}
</style>

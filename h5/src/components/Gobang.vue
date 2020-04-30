<template>
  <div class="gobang">
    <canvas class="gobang-canvas" ref="canvas" @click="canvasClick"></canvas>
  </div>
</template>

<script>
import Piece from "@/utils/piece.js"
export default {
  name: "Gobang",
  props: {
    // 棋盘线条数
    size: {
      type: Number,
      default: 15
    },
    // 线条颜色
    lineColor: {
      type: String,
      default: '#333',
    },
    // 是否禁用
    disable:{
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      canvasPadding: 16,
      canvasObj: null,
      // 格子宽度
      cellWidth: 0,
      // 格子高度
      cellHeight: 0,
      // 格子 x 坐标起始坐标
      xBegin: 0,
      // 格子 y 坐标起始坐标
      yBegin: 0,
      // 二维数组；0-无棋子；1-黑棋；2-白棋
      gobangMap: [],
      // 当前出子颜色
      currentPiece: Piece.BLACK_PIECE,
    };
  },
  mounted() {
    this.initMap()
    this.paint()
  },
  methods: {
    initMap(){
      this.gobangMap = [];
      for(let i = 0; i < this.size; ++i)
      {
        let item = [];
        for(let j = 0; j < this.size; ++j)
        {
          item.push(0)
        }
        this.gobangMap.push(item)
      }
    },
    paint() {
      Promise.all([
        this.loadImage(require("@/assets/bg.jpg")),
      ]).then(imgs => {
        this.canvasObj = this.$refs.canvas;
        const canvasRect = this.canvasObj.getBoundingClientRect();
        this.canvasObj.width = canvasRect.width;
        this.canvasObj.height = canvasRect.height;

        const context = this.canvasObj.getContext("2d");
        this.drawBg(context, imgs[0]);
        this.drawLines(context);
        this.drawPieces(context);
      });
    },
    // 绘制底图
    drawBg(context, img){
      const canvasRect = this.canvasObj.getBoundingClientRect();
      context.drawImage(img, 0, 0, canvasRect.width, canvasRect.height)
    },
    // 绘制线条
    drawLines(context){
      const canvasRect = this.canvasObj.getBoundingClientRect();
      console.log(canvasRect)
      this.cellWidth = (canvasRect.width - (this.canvasPadding * 2)) / (this.size - 1);
      this.cellHeight = (canvasRect.height - (this.canvasPadding * 2)) / (this.size - 1);
      // 绘制横向线条
      this.xBegin = parseInt(this.canvasPadding) + 0.5;
      const hX2 = parseInt(canvasRect.width - this.canvasPadding) + 0.5;
      for (let i = 0; i < this.size; ++i)
      {
        for (let j = 0; j < this.size; ++j)
        {
          const y = parseInt(this.canvasPadding + j * (this.cellHeight)) + 0.5
          context.beginPath();
          context.moveTo(this.xBegin, y)
          context.lineTo(hX2, y)
          context.strokeStyle = this.lineColor
          context.lineWidth = 1;
          context.stroke()
        }
      }
      // 绘制竖向线条
      this.yBegin = parseInt(this.canvasPadding) + 0.5;
      const hY2 = parseInt(canvasRect.height - this.canvasPadding) + 0.5;
      for (let i = 0; i < this.size; ++i)
      {
        for (let j = 0; j < this.size; ++j)
        {
          const x = parseInt(this.canvasPadding + j * (this.cellWidth)) + 0.5
          context.beginPath();
          context.moveTo(x, this.yBegin)
          context.lineTo(x, hY2)
          context.strokeStyle = this.lineColor
          context.lineWidth = 1;
          context.stroke()
        }
      }
    },
    // 绘制棋子
    drawPieces(context){
      for(let i = 0; i < this.size; ++i)
      {
        for(let j = 0; j < this.size; ++j)
        {
          switch(this.gobangMap[i][j])
          {
            case 1:
              // 黑棋
              var point = this.getPointXY(i, j);
              var fillStyle = '#000';
              break;
            case 2:
              // 白棋
              var point = this.getPointXY(i, j);
              var fillStyle = '#fff';
              break;
            default:
              continue;
          }
          context.beginPath();
          context.fillStyle = fillStyle;
          context.arc(point.x, point.y, 10, 0, 2 * Math.PI);
          context.fill();
        }
      }
    },
    // 根据格子编号获取点的XY坐标
    getPointXY(i, j){
      return {
        x: this.xBegin + i * this.cellWidth,
        y: this.yBegin + j * this.cellHeight,
      }
    },
    // canvas 点击
    canvasClick(e){
      if(this.disable)
      {
        return;
      }
      let x = Math.round((e.offsetX - this.canvasPadding) / this.cellWidth)
      let y = Math.round((e.offsetY - this.canvasPadding) / this.cellHeight)
      console.log(x, y)
      console.log(this.gobangMap)
      if(0 == this.gobangMap[x][y])
      {
        this.gobangMap[x][y] = this.currentPiece;
        this.$emit('go', {
          x: x,
          y: y,
        });
        this.paint();
      }
    },
    loadImage(url) {
      return new Promise(resolve => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.src = url;
      });
    },
    setMap(map){
      this.gobangMap = map;
      this.paint();
    },
    setCurrentPiece(currentPiece){
      this.currentPiece = currentPiece;
    },
  }
};
</script>

<style lang="less" scoped>
.gobang {
  padding-top: 14px;
  .gobang-canvas {
    width: calc(100vw - 32px);
    height: calc(100vw - 32px);
  }
}
</style>

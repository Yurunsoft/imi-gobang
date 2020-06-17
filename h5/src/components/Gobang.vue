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
      default: '#AC9D6A',
    },
    // 是否禁用
    disable:{
      type: Boolean,
      default: false,
    },
    lastGoX:{
      type: Number,
    },
    lastGoY:{
      type: Number,
    },
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
        this.loadImage(require("@/assets/black.png")),
        this.loadImage(require("@/assets/white.png")),
      ]).then(imgs => {
        this.canvasObj = this.$refs.canvas;
        const canvasRect = this.canvasObj.getBoundingClientRect();
        this.canvasObj.width = canvasRect.width;
        this.canvasObj.height = canvasRect.height;

        const context = this.canvasObj.getContext("2d");
        this.drawBgColor(context);
        this.drawLines(context);
        this.drawPieces(context, imgs[0], imgs[1]);
      });
    },
    // 画底色
    drawBgColor(context){
      const canvasRect = this.canvasObj.getBoundingClientRect();
      context.fillStyle = '#C0AF75';
      context.fillRect(0, 0, canvasRect.width, canvasRect.height);
    },
    // 绘制线条
    drawLines(context){
      const canvasRect = this.canvasObj.getBoundingClientRect();
      this.cellWidth = (canvasRect.width - (this.canvasPadding * 2)) / (this.size - 1);
      this.cellHeight = (canvasRect.height - (this.canvasPadding * 2)) / (this.size - 1);
      this.xBegin = parseInt(this.canvasPadding) + 0.5;
      const hX2 = parseInt(canvasRect.width - this.canvasPadding) + 0.5;
      this.yBegin = parseInt(this.canvasPadding) + 0.5;
      const hY2 = parseInt(canvasRect.height - this.canvasPadding) + 0.5;
      // 绘制格子底色
      context.fillStyle = '#F4EAC8';
      context.fillRect(this.xBegin, this.yBegin, hX2 - this.canvasPadding, hY2 - this.canvasPadding);
      // 绘制横向线条
      for (let i = 0; i < this.size; ++i)
      {
        for (let j = 0; j < this.size; ++j)
        {
          const y = parseInt(this.canvasPadding + j * (this.cellHeight))
          context.beginPath();
          context.moveTo(this.xBegin, y)
          context.lineTo(hX2, y)
          context.strokeStyle = this.lineColor
          context.lineWidth = 2;
          context.stroke()
        }
      }
      // 绘制竖向线条
      for (let i = 0; i < this.size; ++i)
      {
        for (let j = 0; j < this.size; ++j)
        {
          const x = parseInt(this.canvasPadding + j * (this.cellWidth))
          context.beginPath();
          context.moveTo(x, this.yBegin)
          context.lineTo(x, hY2)
          context.strokeStyle = this.lineColor
          context.lineWidth = 2;
          context.stroke()
        }
      }
    },
    // 绘制棋子
    drawPieces(context, black, white){
      for(let i = 0; i < this.size; ++i)
      {
        for(let j = 0; j < this.size; ++j)
        {
          switch(this.gobangMap[i][j])
          {
            case 1:
              // 黑棋
              var img = black;
              break;
            case 2:
              // 白棋
              var img = white;
              break;
            default:
              continue;
          }
          var point = this.getPointXY(i, j);
          const pieceSize = 24;
          // 画最后下子的圈圈
          if(this.lastGoX == i && this.lastGoY == j)
          {
            context.beginPath();
            context.fillStyle = '#70c769';
            context.arc(point.x - 1, point.y - 1, pieceSize / 2 + 2, 0, 2 * Math.PI);
            context.fill();
          }
          // 画棋子
          context.drawImage(img, point.x - pieceSize / 2, point.y - pieceSize / 2, pieceSize, pieceSize)
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
  .gobang-canvas {
    width: calc(100vw - 32px);
    height: calc(100vw - 32px);
    border-radius: 14px;
  }
}
</style>

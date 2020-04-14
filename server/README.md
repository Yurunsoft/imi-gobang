# 说明

imi 框架：https://www.imiphp.com

这是一个 WebSocket、Http 共存服务的示例

WebSocket:`ws://127.0.0.1:8081/ws`

Http:<http://127.0.0.1:8081/>、<http://127.0.0.1:8081/api>

`test-html/index.html` 文件可以连接 WebSocket 进行调试

## 安装

### 方法一

* git 拉取下本项目

* 在本项目目录中，执行命令：`composer update`

### 方法二

* `composer create-project imiphp/project-websocket`

## 启动命令

在本项目目录中，执行命令：`vendor/bin/imi server/start`

## 权限

`.runtime` 目录需要有可写权限

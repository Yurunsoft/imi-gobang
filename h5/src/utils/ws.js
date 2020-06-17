export default class WS {
    constructor(){
        this.ws = null;
        this.connected = false;
        this.incr = 0;
        this.onActions = {};
    }
    open(url, callback = null){
        if(this.connected)
        {
            return;
        }
        let sessionId = window.localStorage['sessionId'];
        if(sessionId)
        {
            this.ws = new WebSocket(url + '?_sessionId=' + sessionId);
        }
        else
        {
            throw '未登录';
        }
 
        this.ws.onopen = (evt) => {
            this.connected = true;
            callback();
        };
        
        this.ws.onmessage = (evt) => {
            const data = JSON.parse(evt.data);
            const action = undefined !== data.action ? data.action : '';
            const code = undefined !== data.code ? data.code : 0;
            let eventParam = {
                break: false,
                parseCode: true,
            };
            if(this.onActions[action])
            {
                for(let callback of this.onActions[action])
                {
                    callback(data, eventParam);
                    if(eventParam.break)
                    {
                        break;
                    }
                }
            }
            if(eventParam.parseCode)
            {
                if(code !== 0)
                {
                    alert(undefined !== data.message ? data.message : '未知错误');
                }
            }
            // messageCache.push(getNowFormatDate() + "\r\n" + fuckXSS(evt.data.substring(0, 128)) + "\r\n\r\n");
        };
        
        this.ws.onclose = (evt) => {
            // messageCache.push("Connection closed\r\n");
            this.connected = false;
        };
        
        this.ws.onerror = (evt) => {
            // messageCache.push("Connection closed\r\n");
        };
    }
    close(){
        this.ws.close();
        this.connected = false;
    }
    check(){
        return this.connected;
    }
    getInstance(){
        return this.ws;
    }
    send(content){
        this.ws.send(content)
    }
    sendEx(action, data = {}){
        let realData = JSON.parse(JSON.stringify(data));
        realData.action = action;
        const incr = ++this.incr;
        realData.messageId = incr;
        this.send(JSON.stringify(realData));
        return incr;
    }
    onAction(action, callback){
        if(!this.onActions[action])
        {
            this.onActions[action] = [];
        }
        this.onActions[action].push(callback);
    }
}
  
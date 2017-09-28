<?php
namespace Game\Core;


/**
 *  游戏策略类，每个游戏逻辑，都算一个游戏策略， 采用策略模式实现
 */ 
  
 abstract class AStrategy {
    /**
     * 参数
     */         
    protected $_params = array();
    
    /**
     * 构造函数，协议传输过来的数据
     */             
    public function __construct($params) {
        $this->_params = $params;
    }
    
    /**
     * 执行方法，每条游戏协议，实现这个方法就行
     */         
    abstract public function exec();
    
    /**
     * 服务器广播消息， 此方法是给所有的连接客户端， 广播消息
     */         
    protected function Broadcast($serv, $data) {
        foreach($serv->connections as $fd) {
        	$serv->push($fd, $data, WEBSOCKET_OPCODE_BINARY);
        } 
    }
}
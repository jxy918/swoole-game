<?php
namespace Game\App\http;

use Game\Core\AStrategy;
use Game\Core\Packet;
use Game\conf\MainCmd;
use Game\conf\SubCmdSys;

/**
 *  http测试逻辑
 */ 
  
 class HttpTest extends AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//处理扣金币逻辑，暂时不处理原封不动发回去
        $data['stats'] = $this->_params['serv']->stats();
        $data['server'] = $this->_params['serv'];
        $data['client_list'] = $this->getClientListInfo($this->_params['serv']);

        //广播消息
        //echo "当前服务器共有 ".count($this->_params['serv']->connections). " 个连接\n";

        $msg = Packet::packFormat('OK', 0, array('data'=>'hello kitty 你好啊'));
        $msg = Packet::packEncode($msg, MainCmd::CMD_SYS, SubCmdSys::CHAT_MSG_RESP);
        $this->BroadCast2($this->_params['serv'], $msg);
        return json_encode($data);
	}

	public function getClientListInfo($serv) {
	    $client = array();
        foreach($serv->getClientList() as $fd) {
            $client_info = $serv->getClientInfo($fd);
            $client[$fd] = $client_info;
        }
        return $client;
    }
}
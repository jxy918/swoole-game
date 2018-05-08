<?php
namespace Game\App;

use Game\Core\AStrategy;
use Game\Core\Packet;
use Game\Lib\MainCmd;
use Game\Lib\SubCmdSys;

/**
 *  聊天消息回复
 */ 
  
 class ChatMsg extends AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//原封不动发回去    
		$data = Packet::packFormat('OK', 0, $this->_params['data']);
		$data = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmdSys::CHAT_MSG_RESP);
		return $data; 
	}
}
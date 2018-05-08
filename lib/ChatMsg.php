<?php
namespace Game\Lib;

/**
 *  聊天消息回复
 */ 
  
 class ChatMsg extends \Game\Core\AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//原封不动发回去    
		$data = \Game\Core\Packet::packFormat('OK', 0, $this->_params['data']);
		$data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::CHAT_MSG_RESP);
		return $data; 
	}
}

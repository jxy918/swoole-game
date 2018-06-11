<?php
namespace Game\App\websock;

use Game\Core\AStrategy;
use Game\Core\Packet;
use Game\conf\MainCmd;
use Game\conf\SubCmdSys;

/**
 *  取消翻倍
 */ 
  
 class CancelCard extends AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//处理扣金币逻辑，暂时不处理原封不动发回去    
		$data = Packet::packFormat('OK', 0, $this->_params['data']);
		$data = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmdSys::CANCEL_CARD_RESP);
		return $data; 
	}
}
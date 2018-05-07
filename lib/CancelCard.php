<?php
namespace Game\Lib;

/**
 *  取消翻倍
 */ 
  
 class CancelCard extends \Game\Core\AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//处理扣金币逻辑，暂时不处理原封不动发回去    
		$data = \Game\Core\Packet::packFormat('OK', 0, $this->_params['data']);
		$data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::CANCEL_CARD_RESP);
		return $data; 
	}
}
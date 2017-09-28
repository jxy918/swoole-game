<?php
namespace Game\Lib;


/**
 *  翻倍处理
 */ 
  
 class IsDouble extends \Game\Core\AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		$card = isset($this->_params['data']['card']) ? $this->_params['data']['card'] : 2; ;//明牌
		$pos = isset($this->_params['data']['pos']) && ($this->_params['data']['pos'] < 4) ? intval($this->_params['data']['pos']) : 2;//我选中的位置 0123
		$res = \Game\Core\JokerPoker::getIsDoubleCard($card, $pos);
		$res['bean'] = 0;
		$res['bet'] = 0;		
		$data = \Game\Core\Packet::packFormat('OK', 0, $res);
		$data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::IS_DOUBLE_RESP);
		return $data;		    
	}
}
<?php
namespace Game\Lib;

/**
 *  翻牌处理
 */ 
  
 class TurnCard extends \Game\Core\AStrategy {
	/**
	* 执行方法
	*/         
	public function exec() {
		$card = isset($this->_params['data']['card']) ? $this->_params['data']['card'] : array(); 
		$card = \Game\Core\JokerPoker::getFiveCard($card);
		$res = \Game\Core\JokerPoker::getCardType($card);		
		$data = \Game\Core\Packet::packFormat('OK', 0, $res);
		$data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::TURN_CARD_RESP);
		return $data;		    
	}
}
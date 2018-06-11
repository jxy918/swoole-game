<?php
namespace Game\App\websock;

use Game\Core\AStrategy;
use Game\Core\Packet;
use Game\Lib\JokerPoker;
use Game\conf\MainCmd;
use Game\conf\SubCmdSys;

/**
 *  翻牌处理
 */ 
  
 class TurnCard extends AStrategy {
	/**
	* 执行方法
	*/         
	public function exec() {
		$card = isset($this->_params['data']['card']) ? $this->_params['data']['card'] : array(); 
		$card = JokerPoker::getFiveCard($card);
		$res = JokerPoker::getCardType($card);		
		$data = Packet::packFormat('OK', 0, $res);
		$data = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmdSys::TURN_CARD_RESP);
		return $data;		    
	}
}
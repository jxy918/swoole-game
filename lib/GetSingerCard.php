<?php
namespace Game\Lib;

/**
 *  翻倍处理
 */ 
  
 class GetSingerCard extends \Game\Core\AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		$card = \Game\Core\JokerPoker::getOneCard();
		$data = array('card'=>$card);	
		$data = \Game\Core\Packet::packFormat('OK', 0, $data);
		$data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::GET_SINGER_CARD_RESP);
		return $data;		    
	}
}
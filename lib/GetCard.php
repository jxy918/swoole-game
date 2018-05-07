<?php
namespace Game\Lib;

/**
 *  获取卡牌信息
 */ 
  
 class GetCard extends \Game\Core\AStrategy {
    /**
     * 执行方法
     */         
    public function exec() {		
        $data = \Game\Core\JokerPoker::getFiveCard();    
        $data = \Game\Core\Packet::packFormat('OK', 0, $data);
        $data = \Game\Core\Packet::packEncode($data, \Game\Core\MainCmd::CMD_SYS, \Game\Core\SubCmdSys::GET_CARD_RESP);
		
		//$this->testDB();
		//$this->testRedis();
		
		return $data;  
    }
	
	/**
     * 测试db，根据自己情况使用
     */ 
	public function testDB() {
		$sql = 'Call sp_account_get_by_uid(1000006)';
		$sql_data = $this->_params['db']->queryAll($sql, '');	
		var_dump(__CLASS__.'=======>',$sql_data);		
	}
	
	/**
     * 测试redis，，根据自己情况使用
     */ 
	public function testRedis() {
		$res = $this->_params['redis']->set('test', 'aaaaaaaaceshithis 这是一个测试程序');
		var_dump(__CLASS__.'=======>redis',$res);			
	}
}
<?php
namespace Game\App\tcp;

use Game\Core\AStrategy;
use Game\Core\Packet;
use Game\Lib\protobuf\Person;

/**
 *  测试tcp逻辑处理
 */ 
  
 class TcpTest extends AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {
        //解开protobuff数据，并输出
        $data = $this->_params['data'];
        $obj = new Person();
        $obj->parseFromString($data);
        echo $obj->getName() ."\n";
        echo $obj->getEmail() ."\n";
        echo $obj->getMoney() ."\n";
        echo $obj->getId() . "\n";

        //原样返回protobuf数据
        $data = Packet::packEncode($data, 1, 1, 'protobuf');
        return $data;
    }
}
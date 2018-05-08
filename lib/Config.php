<?php
namespace Game\Lib;
/** 
 * 系统配置文件， 可以通过增加方法的形式载入 
 */
class Config {	
	public static function getDbConf() {
		//db配置，需要用时可以启用
		$db = array(
			'dbms'=>'mysql', 
			'host'=>'192.168.1.85:3308', 
			'user'=>'web',
			'passwd'=>'aG98asg!($',
			'name'=>'accounts_mj',
			'setname'=>true,
			'charset'=> 'utf8',
			'persistent'=>false	
		);
		return $db; 
	}
	
	public static function getRedisConf() {
		//redis配置，需要用时可以启用
		$redis = array(
			'host'=>'192.168.7.196',
			'port'=>6379	
		);
		return $redis; 
	}
}
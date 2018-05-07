<?php
namespace Game\Core;

/**
 * 调度运行游戏逻辑策略
 */ 
class Dispatch {
    /**
     * 策略对象
     * @var object
     */
    private $_strategy = null;
    
    /**
     * 参数配置文件
     * @var array
     */
    private $_params = array();
    
    
    /**
     * 构造你要使用的策略
     * @param $config array
     */
    public function __construct($params = array()) {
    	$this->_params = $params;
    	//获取路由策略
		$route = Route::$map;
		//获取策略类名
		$classname = isset($route[$this->_params['cmd']][$this->_params['scmd']]) ? $route[$this->_params['cmd']][$this->_params['scmd']] : '';
		$classname = 'Game\Lib\\'.$classname;
		if (class_exists($classname)) {
			$this->_strategy = new $classname($this->_params);
		} else {
    		echo 'Error:'.$classname." is not support \n";
    	}
    }
    
    /**
     * 获取策略
     */         
    public function getStrategy() {
        return $this->_strategy;
    }
    
    /**
     * 执行策略
     */
    public function exec() {
    	return $this->_strategy->exec();
    }

}
?>
<?php
namespace Game\Core;

use Game\Conf\Route;

/**
 * 调度运行游戏逻辑策略,分别调度到不同协议目录里，策略模式容器
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
     * Dispatch constructor.
     * @param array $params
     */
    public function __construct($params = array()) {
        $protocol = isset($params['protocol']) ? $params['protocol'] : 0;
        $this->_params = $params;
        switch ($protocol) {
            case GameConst::GM_PROTOCOL_WEBSOCK:
                $this->webSockStrategy();
                break;
            case GameConst::GM_PROTOCOL_TCP:
                $this->tcpStrategy();
                break;
            case GameConst::GM_PROTOCOL_HTTP:
                $this->httpStrategy();
                break;
            default:
                Log::show('protocol is not foound', GameConst::GM_LOG_LEVEL_ERROR);
                break;
        }
    }

    /**
     * websocket逻辑处理策略路由转发
     */
    public function webSockStrategy() {
        //获取路由策略
        $route = Route::$websock_map;
        //获取策略类名
        $classname = isset($route[$this->_params['cmd']][$this->_params['scmd']]) ? $route[$this->_params['cmd']][$this->_params['scmd']] : '';
        //转发到对应目录处理逻辑
        $classname = 'Game\App\websock\\'.$classname;
        if (class_exists($classname)) {
            $this->_strategy = new $classname($this->_params);
            Log::show("Class: $classname");
        } else {
            Log::show("Websockt Error: class is not support,cmd is {$this->_params['cmd']},scmd is {$this->_params['scmd']}", GameConst::GM_LOG_LEVEL_ERROR);
        }
    }

    /**
     * http逻辑处理策略路由转发
     */
    public function httpStrategy() {
        //获取路由策略
        $route = Route::$http_map;
        $cmd = $this->_params['cmd'];
        //获取策略类名
        $classname = in_array($cmd, $route) ? $cmd : '';
        //转发到对应目录处理逻辑
        $classname = 'Game\App\http\\'.$classname;
        if (class_exists($classname)) {
            $this->_strategy = new $classname($this->_params);
            Log::show("Class: $classname");
        } else {
            Log::show("Http Error: class is not support,cmd is {$this->_params['cmd']}", GameConst::GM_LOG_LEVEL_ERROR);
        }
    }

    /**
     * tcp逻辑处理策略路由转发
     */
    public function tcpStrategy() {
        //获取路由策略
        $route = Route::$tcp_map;
        //获取策略类名
        $classname = isset($route[$this->_params['cmd']][$this->_params['scmd']]) ? $route[$this->_params['cmd']][$this->_params['scmd']] : '';
        //转发到对应目录处理逻辑
        $classname = 'Game\App\tcp\\'.$classname;
        if (class_exists($classname)) {
            $this->_strategy = new $classname($this->_params);
            Log::show("Class: $classname");
        } else {
            Log::show("Tcp Error: class is not support,cmd is {$this->_params['cmd']},scmd is {$this->_params['scmd']}", GameConst::GM_LOG_LEVEL_ERROR);
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
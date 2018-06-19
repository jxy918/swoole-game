<?php
namespace Game\Core;

/**
 * mysql连接池类
 * Class MysqlPool
 * @package Game\Core
 */
class MysqlPool {
    protected $pool;
    protected $conf;

    function __construct($conf) {
        $this->pool = new \SplQueue;
        $this->conf = $conf;
    }

    function put($db) {
        $this->pool->push($db);
    }

    function get() {
        //有空闲连接
        if (count($this->pool) > 0) {
            return $this->pool->pop();
        }

        //无空闲连接，创建新连接
        $db = new \Swoole\Coroutine\MySQL();
        $res = $db->connect([
            'host' => $this->conf['host'],
            'port' => $this->conf['port'],
            'user' => $this->conf['user'],
            'password' => $this->conf['password'],
            'database' => $this->conf['database'],
        ]);
        if ($res == false) {
            return false;
        } else {
            return $db;
        }
    }
}
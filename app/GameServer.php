<?php
namespace Game\App;

use Game\Core\BaseServer;
use Game\Core\GameConst;
use Game\Core\Dispatch;
use Game\Core\Packet;

/**
 * websocket服务器
 */ 
class GameServer extends BaseServer {
    /**
     * 是否开启http服务监听
     * @var bool
     */
    protected $is_open_http = true;

    /**
     * 是否开启websocket监听
     * @var bool
     */
    protected $is_open_tcp = true;
		
	/** 	
     * 全局db对象
     */
	protected $db = null;
	
	/**
     * 	
     * 全局redis对象
     */
	protected $redis = null;

	/**
	 * 附件服务器初始化，例如：such as swoole atomic table or buffer 可以放置swoole的计数器，table等
	*/
	protected function init($serv){

	}
	
	/**
	 * WorkerStart时候可以调用， //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
	 * 比如需要动态加载的东西，可以做到无缝重启逻辑
	*/
	protected function initReload($server, $worker_id) {
		//自动加载， 可以通过reload更新
		spl_autoload_register(function($classname) {
			if(file_exists(__DIR__.'/../lib/' . $classname . '.php')) {
				require_once __DIR__.'/../lib/' . $classname . '.php';
			}
			if(file_exists(__DIR__.'/../app/' . $classname . '.php')) {
                require_once __DIR__ . '/../app/' . $classname . '.php';
            }
		});
	}


	
	public function initDB() {		
		//初始化db
		//$this->db = new PdoDB(Config::getDbConf());
		//$this->db->connect();
		$conf = array(
			'host' => '192.168.1.85',
			'port' => 3308,
			'user' => 'web',
			'password' => 'aG98asg!($',
			'database' => 'accounts_mj'
		);
		$this->db = new MysqlPool($conf);
		$this->db = $this->db->get();
	}
	
	public function initRedis() {
		//初始化redis
		$redis_conf = Config::getRedisConf();
		$this->redis = new \Redis();
		$this->redis->connect($redis_conf['host'], $redis_conf['port']);
	}
}


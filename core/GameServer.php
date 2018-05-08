<?php
namespace Game\Core;

use Game\Lib\Config;

/**
 * websocket服务器
 */ 
class GameServer extends BaseServer {
	/**
     * 服务器进程名的前缀
     */
	protected $process_name_prefix = 'game';
		
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
     * tcp端口，配置里的话会，会启动tcp端口
     */    
//	protected $tcpserver_port = 20000;
    	
	/**
	 * 附件服务器初始化，例如：such as swoole atomic table or buffer 可以放置swoole的计数器，table等
	*/
	protected function init($serv){
		$this->initDB();
		$this->initRedis();	
	}
	
	/**
	 * WorkerStart时候可以调用， //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
	 * 比如需要动态加载的东西，可以做到无缝重启逻辑
	*/
	protected function initReload($server, $worker_id) {
		//require_once __DIR__."/../lib/Const.php";
		//require_once __DIR__."/Dispatch.php";

		//require_once __DIR__."/AStrategy.php";
		//require_once __DIR__."/JokerPoker.php";		
	}
	
	public function doWork($serv, $task_id, $src_worker_id, $data) {		
		if($data['protocol'] == 'ws') {		
			$back = array();
			$data = Packet::packDecode($data['data']);
			if(isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
				echo date('Y-m-d H:i:s').'  DEBUG  Recv <<<  cmd='.$data['cmd'].'  scmd='.$data['scmd'].'  len='.$data['len'].'  data='.json_encode($data['data'])."\n";
				//转发请求，代理模式处理
				$data['db'] = $this->db;
				$data['redis'] = $this->redis;
				$data['serv'] = $serv;
				$obj = new Dispatch($data);
				if(!empty($obj->getStrategy())) {
					$back = $obj->exec();
					return $back;
				} else {
					echo "get strategy fail\n";
				} 
			} else {
				echo $data['msg']."\n";
			}
		} else {
			return 'http protocol';
		}			
	}
<<<<<<< HEAD
	
	public function initDB() {		
		//初始化db
		$this->db = new PdoDB(Config::getDbConf());
		$this->db->connect();
	}
	
	public function initRedis() {
		//初始化redis
		$redis_conf = Config::getRedisConf();
		$this->redis = new \Redis();
		$this->redis->connect($redis_conf['host'], $redis_conf['port']);	
	}
}
=======
}
>>>>>>> 9e809254aa5906608fe3c7e567346c598e77568a

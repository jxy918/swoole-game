<?php
namespace Game\Core;

/**
 * 服务器基类，主服务器为websocket，同时可以监听tcp和http服务器
 *
 */  
abstract class BaseServer {
	/**
	 * 单例对象
	 */         
	private static  $_instance = null;
    
	/**
	 * websocket服务器对象
	 */              
	protected $server = null;
    
	/**
	 * tcp服务器对象
	 */         
	protected $tcpserver = null;

	/**
	 * 服务器ip
	 */         
	protected $server_ip = "0.0.0.0";
    
	/**
	 * websocket服务器端口
	 */         
	protected $server_port = 9502;
    
	/**
	 * tcp服务器端口，此端口子类继承有赋值， 将会监听tcp端口
	 */         
	protected $tcpserver_port = 0;
	
	/**
	 * http服务器端口，此端口子类继承有赋值， 将会监听tcp端口
	 */         
	protected $httpserver_port = 0;
	
	/**
	 * 服务器进程名的前缀
	 */
	protected $process_name_prefix = 'game';

	/**
	 * websocket服务器配置
	 */         
	protected $config = array(
		'dispatch_mode' => 3,
		'open_length_check' => 1,
		'package_length_type' => 'N',
		'package_length_offset' => 0,
		'package_body_offset' => 4,

		'package_max_length' => 2097152, // 1024 * 1024 * 2,
		'buffer_output_size' => 3145728, //1024 * 1024 * 3,
		'pipe_buffer_size' => 33554432, // 1024 * 1024 * 32,		
		
		'heartbeat_check_interval' => 30,
		'heartbeat_idle_time' => 60,
		
//		'open_cpu_affinity' => 1,

//		'reactor_num' => 32,//建议设置为CPU核数 x 2 新版会自动设置 cpu个数
		'max_conn'=>2000,
		'worker_num' => 1,
		'task_worker_num' => 2,//生产环境请加大，建议1000

		'max_request' => 0, //必须设置为0，否则会导致并发任务超时,don't change this number
		'task_max_request' => 2000,
		
//		'daemonize'=>1, 
//		'log_level' => 2, //swoole 日志级别 Info
		'backlog' => 3000,
		'log_file' => '../log/sw_server.log',//swoole 系统日志，任何代码内echo都会在这里输出
//		'task_tmpdir' => '/dev/shm/swtask/',//task 投递内容过长时，会临时保存在这里，请将tmp设置使用内存

//		'document_root' => '/data/web/test/myswoole/poker/client',
//		'enable_static_handler' => true,
	);

	/**
	 * tcp服务器设置
	 */         
	protected $tcp_config = array(

	);
	
	/**
	* 单例模式，防止对象被克隆
	*/
	private function __clone() {}

	/**
	* 单例模式，防止对象被克隆
	*/
	private function __construct() {}

	/**
	* 获取单例对象
	* @param int uid 用户UID
	* @param string token 用户Token
	* @return object
	*/
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new static();           
		}
		return self::$_instance;
	}

	/**
	 * 初始化服务器
	 */              
	public function initServer() {
		
		//开启websocket服务器
		$this->server = new \Swoole\Websocket\Server($this->server_ip, $this->server_port);	
				
		//如果tcp端口有设置， 将开启tcp协议
		if(!empty($this->tcpserver_port)) {			
			//tcp server
			$this->tcpserver = $this->server->listen($this->server_ip, $this->tcpserver_port, SWOOLE_SOCK_TCP);
			//tcp只使用这几个个事件
			$this->tcpserver->on('Connect', array($this, 'onConnect'));
			$this->tcpserver->on('Receive', array($this, 'onReceive'));
		}
		
		//如果http端口有设置， 将开启http协议
		if(!empty($this->httpserver_port)) {			
			//tcp server
			$this->tcpserver = $this->server->listen($this->server_ip, $this->httpserver_port, SWOOLE_SOCK_TCP);
			//http服务器只使用这个事件
			$this->server->on('Request', array($this, 'onRequest'));
		}
		
		//init websocket server
		$this->server->on('Start', array($this, 'onStart'));
		$this->server->on('ManagerStart', array($this, 'onManagerStart'));
		$this->server->on('ManagerStop', array($this, 'onManagerStop'));
		//websocket服务器
		$this->server->on('Open', array($this, 'onOpen'));
		$this->server->on('Message', array($this, 'onMessage'));		
		$this->server->on('WorkerStart', array($this, 'onWorkerStart'));
		$this->server->on('WorkerError', array($this, 'onWorkerError'));
		$this->server->on('Task', array($this, 'onTask'));
		$this->server->on('Finish', array($this, 'onFinish'));
		$this->server->on('Close', array($this, 'onClose'));
		$this->init($this->server);
		return self::$_instance;
	}

	/**
	 * 附件服务器初始化，例如：such as swoole atomic table or buffer 可以放置swoole的计数器，table等
	*/
	abstract protected function init($serv);

	/**
	 * WorkerStart时候可以调用， //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
	 * 比如需要动态加载的东西，可以做到无缝重启逻辑
	*/
	abstract protected function initReload($server, $worker_id);
	
	/**
	 * 此业务主要是开启tcp协议时才有用，业务实际处理代码仍这里，return the result 使用return返回处理结果//throw new Exception("asbddddfds",1231);
	*/
	abstract protected function doWork($serv, $task_id, $src_worker_id, $data); 
    
	/**
     * 服务器启动
     */         
	public function start() {
		$this->server->set($this->config);
		//如果tcp端口有设置， 将开启tcp协议
		if(!empty($this->tcpserver_port)) {
			//注意， 监听多端口，一定需要各自调用set方法，recive回调方法才会生效， 如果不设置， 回调不生效
			$this->tcpserver->set($this->tcp_config);
		}
		$this->server->start();
	}
	
	//服务开始回调
	public function onStart($serv) {
		swoole_set_process_name($this->process_name_prefix."_master_".get_called_class());
		echo "MasterPid={$serv->master_pid}\n";
		echo "ManagerPid={$serv->manager_pid}\n";
		echo "Server: start.Swoole version is [" . SWOOLE_VERSION . "]\n";	
	}
	
	//管理进程启动回调
	public function onManagerStart($serv) {
		swoole_set_process_name($this->process_name_prefix."_manager_".get_called_class());
		echo "onManagerStart:\n";	
	}
	
	//管理进程关闭回调
	public function onManagerStop($serv) {
		$serv->shutdown();
		echo "onManagerStop:\n";	
	}
	
	//ws连接回调
	public function onOpen($serv, $frame) {
		echo "onOpen connection open: ".$frame->fd."\n";
	}
	
	//tcp连接回调
	public function onConnect($serv, $fd) {
		echo "onConnect: connected...\n";
	}
	
	//ws投递任务
	public function onMessage($serv, $frame) {
		$send['protocol'] = 'ws';
		$send['data'] = $frame->data;
		$send['fd'] = $frame->fd;
		$taskid = $this->server->task($send, -1, function ($serv, $task_id, $data) use ($frame) {
			if(!empty($data)) {
				$serv->push($frame->fd, $data, WEBSOCKET_OPCODE_BINARY);
			}				
		});	
	}
	
	//http投递任务
	public function onRequest($request, $response) {
		$send['protocol'] = 'http';
		$send['data'] = $request;
		$send['response'] = $response;
		$taskid = $this->server->task($send, -1, function ($serv, $task_id, $data) use ($response) {
			if(!empty($data)) {
				$response->end($data);
			}				
		});	
		echo "Request: Start";
	}
	
	//tcp投递任务
	public function onReceive($serv, $fd, $from_id, $data) {
		$send['protocol'] = 'tcp';
		$send['data'] = $data;
		$send['fd'] = $fd;
		$taskid = $this->server->task($send, -1, function ($serv, $task_id, $data) use ($fd) {
			if(!empty($data)) {
				$serv->send($fd, $data);
			}				
		});	
		echo "onReceive: ".$data;
	}
	
	//worker进程开启回调
	public function onWorkerStart($server, $worker_id) {
		$istask = $server->taskworker;	
        if ($istask) {
			$this->initReload($server, $worker_id);
			swoole_set_process_name($this->process_name_prefix."_task{$worker_id}_".get_called_class());
			echo "Task work_id is {$worker_id}\n"; 	
        } else {
			swoole_set_process_name($this->process_name_prefix."_worker{$worker_id}_".get_called_class());
			echo "Worker work_id is {$worker_id}\n"; 
		}
		echo "onWorkerStart:\n";	
	}
	
	//worker进程错误回调
	public function onWorkerError($server, $worker_id, $worker_pid, $exit_code) {
		echo "onWorkerError: worker_id={$worker_id}  worker_pid={$worker_pid}  exit_code={$exit_code}\n";	
	}
	
	//任务进程回调
	public function onTask($serv, $task_id, $src_worker_id, $data) {
		$data = $this->doWork($serv, $task_id, $src_worker_id, $data);
		echo "onTask: task_id={$task_id}	woker_id={$src_worker_id}\n";
		return $data;
	}
	
	//任务结束后回调处理， 高版本可以自定会回调函数
	public function onFinish($serv, $task_id, $data) {
		echo "onFinish:\n";	
	}
	
	//服务器关闭回调
	public function onClose($serv, $fd) {
		echo "onClose connection close: ".$fd."\n";
	}
	
	public function __destruct() {
        echo "Server Was Shutdown..." . PHP_EOL;
        //shutdown
        $this->server->shutdown();
    }
}

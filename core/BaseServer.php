<?php
namespace Game\Core;

/**
 * 游戏服务器基类，主服务器为WEBSOCKET, 同时可以开启监听TCP, HTTP等协议服务器
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
     * 是否开启websocket监听
     * @var bool
     */
	protected $is_open_tcp = false;

    /**
     * 是否开启http服务监听
     * @var bool
     */
    protected $is_open_http = false;

	/**
	 * 服务器默认配置
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
		'worker_num' => 2,
		'task_worker_num' => 4,//生产环境请加大，建议1000

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
	protected $tcp_config = array();

    /**
     * http服务器设置
     */
    protected $http_config = array();

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
     * 设置tcp的配置文件，默认不设置
     * @param array $config
     * @return null
     */
	public function setTcpConf($config = array()) {
	    $this->tcp_config = $config;
	    return self::$_instance;
    }

    /**
     * 设置Http设置
     * @param array $config
     * @return null
     */
    public function setHttpConf($config = array()) {
        $this->http_config = $config;
        return self::$_instance;
    }

    /**
     * 设置websocke设置，默认不设置
     * @param array $config
     * @return null
     */
    public function setWebsockConf($config = array()) {
        $this->config = $config;
        return self::$_instance;
    }

	/**
	 * 初始化服务器
	 */              
	public function initServer() {
		//开启websocket服务器
		$this->server = new \Swoole\Websocket\Server(GameConst::GM_SERVER_IP, GameConst::GM_PROTOCOL_WEBSOCK_PORT);
        $this->server->set($this->config);
        //如果http端口有设置， 将开启http协议
        if($this->is_open_http) {
            //http server
            $httpserver = $this->server->listen(GameConst::GM_SERVER_IP, GameConst::GM_PROTOCOL_HTTP_PORT, SWOOLE_SOCK_TCP);
        }
		//如果tcp端口有设置， 将开启tcp协议
		if($this->is_open_tcp) {
			//tcp server
			$tcpserver = $this->server->listen(GameConst::GM_SERVER_IP, GameConst::GM_PROTOCOL_TCP_PORT, SWOOLE_SOCK_TCP);
            $tcpserver->on('Receive', array($this, 'onReceive'));
            $tcpserver->set($this->tcp_config);
		}

		//init websocket server
		$this->server->on('Start', array($this, 'onStart'));
		$this->server->on('ManagerStart', array($this, 'onManagerStart'));
		$this->server->on('ManagerStop', array($this, 'onManagerStop'));
		//websocket服务器
		$this->server->on('Open', array($this, 'onOpen'));
		$this->server->on('Message', array($this, 'onMessage'));
        //http服务器只使用这个事件
        $this->server->on('Request', array($this, 'onRequest'));
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
     * 服务器启动
     */         
	public function start() {
		$this->server->start();
	}
	
	//服务开始回调
	public function onStart($serv) {
		swoole_set_process_name(GameConst::GM_PROCESS_NAME_PREFIX."_master_".$this->getClassNanme());
        Log::show("MasterPid={$serv->master_pid}");
        Log::show("ManagerPid={$serv->manager_pid}");
        Log::show("Server: start.Swoole version is [" . SWOOLE_VERSION . "]");
	}
	
	//管理进程启动回调
	public function onManagerStart($serv) {
        Log::show("onManagerStart:");
		swoole_set_process_name(GameConst::GM_PROCESS_NAME_PREFIX."_manager_".$this->getClassNanme());
	}
	
	//管理进程关闭回调
	public function onManagerStop($serv) {
        Log::show("onManagerStop:");
		$serv->shutdown();
	}
	
	//ws连接回调
	public function onOpen($serv, $frame) {
        Log::show("onOpen connection open: ".$frame->fd);
	}
	
	//tcp连接回调
	public function onConnect($serv, $fd) {
        Log::show("onConnect: connected...");
	}
	
	//ws投递任务
	public function onMessage($serv, $frame) {
        Log::show("Message: Start");
		$send['protocol'] = GameConst::GM_PROTOCOL_WEBSOCK;
		$send['data'] = $frame->data;
		$send['fd'] = $frame->fd;
		$this->server->task($send, -1, function ($serv, $task_id, $data) use ($frame) {
			if(!empty($data)) {
				$serv->push($frame->fd, $data, WEBSOCKET_OPCODE_BINARY);
			}				
		});
	}
	
	//http投递任务
	public function onRequest($request, $response) {
        Log::show("Request: Start");
		$send['protocol'] = GameConst::GM_PROTOCOL_HTTP;
        $send['data'] = $request->server;
        $send['fd'] = $request->fd;
        $this->server->task($send, -1, function ($serv, $task_id, $data) use ($response) {
            if(!empty($data)) {
                $response->end($data);
            }
        });
	}
	
	//tcp投递任务
	public function onReceive($serv, $fd, $from_id, $data) {
        Log::show("onReceive: start");
		$send['protocol'] = GameConst::GM_PROTOCOL_TCP;
		$send['data'] = $data;
		$send['fd'] = $fd;
		$this->server->task($send, -1, function ($serv, $task_id, $data) use ($fd) {
			if(!empty($data)) {
				$serv->send($fd, $data);
			}				
		});
	}
	
	//worker进程开启回调
	public function onWorkerStart($server, $worker_id) {
		$istask = $server->taskworker;
        Log::show("onWorkerStart:");
        if ($istask) {
			$this->initReload($server, $worker_id);
			swoole_set_process_name(GameConst::GM_PROCESS_NAME_PREFIX."_task{$worker_id}_".$this->getClassNanme());
            Log::show("Task work_id is {$worker_id}");
        } else {
			swoole_set_process_name(GameConst::GM_PROCESS_NAME_PREFIX."_worker{$worker_id}_".$this->getClassNanme());
            Log::show("Worker work_id is {$worker_id}");
		}
	}
	
	//worker进程错误回调
	public function onWorkerError($server, $worker_id, $worker_pid, $exit_code) {
        Log::show("onWorkerError: worker_id={$worker_id}  worker_pid={$worker_pid}  exit_code={$exit_code}");
	}
	
	//任务进程回调
	public function onTask($serv, $task_id, $src_worker_id, $data) {
		$data = $this->doWork($serv, $task_id, $src_worker_id, $data);
        Log::show("onTask: task_id={$task_id}	woker_id={$src_worker_id}");
		return $data;
	}
	
	//任务结束后回调处理， 高版本可以自定会回调函数
	public function onFinish($serv, $task_id, $data) {
        Log::show("onFinish");
	}
	
	//服务器关闭回调
	public function onClose($serv, $fd) {
        Log::show("onClose connection close: {$fd}");
	}
	
	public function __destruct() {
        Log::show('Server Was Shutdown...');
        $this->server->shutdown();
    }

    /**
     * 获取被调用的类名称
     * @return mixed
     */
    public function getClassNanme() {
        $classname = get_called_class();
        $classname =  str_replace("\\", '_', $classname);
        return $classname;
    }

    /**
     * 此业务主要是开启tcp协议时才有用，业务实际处理代码仍这里，return the result 使用return返回处理结果//throw new Exception("asbddddfds",1231);
     * 根据协议来进行相关逻辑处理,增加tcp,webscoket,http协议的路由转发处理，自理可以继承重写方法
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $from_data
     * @return array|string
     */
    public function doWork($serv, $task_id, $src_worker_id, $from_data) {
        $protocol = isset($from_data['protocol']) ? $from_data['protocol'] : 0;
        switch ($protocol) {
            case GameConst::GM_PROTOCOL_WEBSOCK:
                $back = $this->webSockWork($serv, $task_id, $src_worker_id, $from_data);
                break;
            case GameConst::GM_PROTOCOL_TCP:
                $back = $this->tcpWork($serv, $task_id, $src_worker_id, $from_data);
                break;
            case GameConst::GM_PROTOCOL_HTTP:
                $back = $this->httpWork($serv, $task_id, $src_worker_id, $from_data);
                break;
            default:
                $back = 'protocol is not foound';
                break;
        }
        return $back;
    }

    /**
     * 处理websock数据
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $from_data
     * @return array
     */
    public function websockWork($serv, $task_id, $src_worker_id, $from_data) {
        $back = array();
        $data = Packet::packDecode($from_data['data']);
        if(isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
            Log::show('Recv <<<  cmd='.$data['cmd'].'  scmd='.$data['scmd'].'  len='.$data['len'].'  data='.json_encode($data['data']));
            //转发请求，代理模式处理,websocket路由到相关逻辑
            $data['serv'] = $serv;
            $data['protocol'] = GameConst::GM_PROTOCOL_WEBSOCK;
            $back = $this->dispatch($data);
            Log::show('Tcp Strategy <<<  data='.$back, GameConst::GM_LOG_LEVEL_DEBUG);
            if(!empty($back)) {
                return $back;
            }
        } else {
            Log::show($data['msg']);
        }
    }

    /**
     * 处理tcp数据
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $from_data
     * @return array
     */
    public function tcpWork($serv, $task_id, $src_worker_id, $from_data) {
        $back = array();
        $data = Packet::packDecode($from_data['data'], 'protobuf');
        if(isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
            Log::show('Recv <<<  cmd='.$data['cmd'].'  scmd='.$data['scmd'].'  len='.$data['len'].'  data='.json_encode($data['data']));
            //转发请求，代理模式处理,websocket路由到相关逻辑
            //$data['serv'] = $serv;
            $data['protocol'] = GameConst::GM_PROTOCOL_TCP;
            $back = $this->dispatch($data);
            Log::show('Tcp Strategy <<<  data='.$back, GameConst::GM_LOG_LEVEL_DEBUG);
            if(!empty($back)) {
                return $back;
            }
        } else {
            Log::show($data['msg']);
        }
    }

    /**
     * 处理http数据
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $from_data
     * @return array
     */
    public function httpWork($serv, $task_id, $src_worker_id, $from_data) {
        //处理路由设置
        $cmd = isset($from_data['data']['path_info']) ?  trim($from_data['data']['path_info']) : '';
        $query_string = isset($from_data['data']['query_string']) ? trim($from_data['data']['query_string']) : '';
        parse_str($query_string, $param);
        //转发请求，代理模式处理,websocket路由到相关逻辑
        $data['cmd'] = str_replace('/','',$cmd);
        $data['param'] = $param;
        $data['serv'] = $serv;
        $data['protocol'] = GameConst::GM_PROTOCOL_HTTP;
        $back = $this->dispatch($data);
        Log::show('Http Strategy <<<  data='.$back, GameConst::GM_LOG_LEVEL_DEBUG);
        return  $back;
    }

    /**
     * 根据路由策略处理逻辑，并返回数据
     * @param $data
     * @return string
     */
    public function dispatch($data) {
        $obj = new Dispatch($data);
        $back = '';
        if(!empty($obj->getStrategy())) {
            $back = $obj->exec();
        } else {
            if ($data['protocol'] == GameConst::GM_PROTOCOL_HTTP) {;
                $back = "<center><h1>404 Not Found </h1></center><hr><center>swoole/2.1.3</center>\n";
            }
        }
        return $back;
    }
}

<?php
namespace Game\Conf;

use Game\Core\GameConst;
/** 
 * 系统配置文件， 可以通过增加方法的形式载入 
 */
class Config {
    /**
     * 监听服务器配置,可以通过配置来设置参数
     * @return mixed
     */
    public static function getPortConf() {
        $config = array(
            array(
                'port'=>array(
                    'socket_type' => GameConst::GM_PROTOCOL_WEBSOCK,
                    'socket_name' => GameConst::GM_SERVER_IP,
                    'socket_port' => GameConst::GM_PROTOCOL_WEBSOCK_PORT,
                ),
                'set'=>array(
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

//		            'open_cpu_affinity' => 1,

//		            'reactor_num' => 32,//建议设置为CPU核数 x 2 新版会自动设置 cpu个数
                    'max_conn'=>2000,
                    'worker_num' => 2,
                    'task_worker_num' => 4,//生产环境请加大，建议1000

                    'max_request' => 0, //必须设置为0，否则会导致并发任务超时,don't change this number
                    'task_max_request' => 2000,

//		            'daemonize'=>1,
//		            'log_level' => 2, //swoole 日志级别 Info
                    'backlog' => 3000,
                    'log_file' => '../log/sw_server.log',//swoole 系统日志，任何代码内echo都会在这里输出
//		            'task_tmpdir' => '/dev/shm/swtask/',//task 投递内容过长时，会临时保存在这里，请将tmp设置使用内存

//		            'document_root' => '/data/web/test/myswoole/poker/client',
//		            'enable_static_handler' => true,
                )
            ),
           array(
               'port'=>array(
                   'socket_type' => GameConst::GM_PROTOCOL_TCP,
                   'socket_name' => GameConst::GM_SERVER_IP,
                   'socket_port' => GameConst::GM_PROTOCOL_TCP_PORT,
               ),
               'set'=>array(

               ),
           ),
            array(
                'port'=>array(
                    'socket_type' => GameConst::GM_PROTOCOL_HTTP,
                    'socket_name' => GameConst::GM_SERVER_IP,
                    'socket_port' => GameConst::GM_PROTOCOL_HTTP_PORT,
                ),
                'set'=>array(

                ),
            ),
        );
        return $config;
    }

    /**
     * 获取db配置
     * @return array
     */
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

<?php
namespace Game\Conf;

/** 
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理 
 *
 */
class Route {
    /**
     * websocket路由配置，websocke配置和tcp配置需要先去配置（MainCmd)主命令子和(SubCmdSys)子主命令字配置文件
     * @var array
     */
	public static $websock_map = array(
		MainCmd::CMD_SYS=>array(
			SubCmdSys::GET_CARD_REQ=>'GetCard',
			SubCmdSys::SEND_CARD_REQ=>'SendCard',
			SubCmdSys::TURN_CARD_REQ=>'TurnCard',
			SubCmdSys::CANCEL_CARD_REQ=>'CancelCard',
			SubCmdSys::IS_DOUBLE_REQ=>'IsDouble',
			SubCmdSys::GET_SINGER_CARD_REQ=>'GetSingerCard',
			SubCmdSys::CHAT_MSG_REQ=>'ChatMsg',
		),
	);

    /**
     * TCP路由配置，websocke配置和tcp配置需要先去配置（MainCmd)主命令子和(SubCmdSys)子主命令字配置文件
     * @var array
     */
    public static $tcp_map = array(
        MainCmd::CMD_TCP_SYS=>array(
            SubCmdSys::TCP_GET_HEART_ASK=>'TcpTest',
        ),
    );

    /**
     * HTTP简单路由配置，注意http不区分主命令字和子命令字，直接用类名来转发路由逻辑
     * @var array
     */
    public static $http_map = array(
        'HttpTest',
		'HttpTestIndex'
    );
}                                                                                                       ;
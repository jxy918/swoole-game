<?php
namespace Game\Lib;

/** 
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理 
 *
 */
class Route {
	public static $map = array(
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
}                                                                                                       ;
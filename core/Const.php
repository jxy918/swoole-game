<?php
namespace Game\Core;

/** 
 * 主命令字定义，（可以有多个主命令字，每个主命令字对应一个子命令字） 
 */
 
Class MainCmd {
    const CMD_SYS = 1;      //系统主命令字，（主命令字）- 客户端使用
}

/** 
 *子命令字定义，h5客户端也应有一份对应配置，REQ结尾一般是客户端请求过来的子命令字， RESP服务器返回给客户端处理子命令字
 */
 
class SubCmdSys {
	const GET_CARD_REQ = 1;				//获取卡牌请求，客户端使用 
	const GET_CARD_RESP = 2;			//获取卡牌响应，服务端使用  
	const SEND_CARD_REQ = 3;			//发送卡牌请求，客户端使用  
	const SEND_CARD_RESP = 4;			//发送卡牌响应，服务端使用
	const TURN_CARD_REQ = 5;			//翻开卡牌请求，客户端使用 
	const TURN_CARD_RESP = 6;			//翻开卡牌响应，服务端使用
	const CANCEL_CARD_REQ = 7;			//取消卡牌请求，客户端使用
	const CANCEL_CARD_RESP = 8;			//取消卡牌响应，服务端使用
	const IS_DOUBLE_REQ = 9;			//结果是否翻倍请求，客户端使用 
	const IS_DOUBLE_RESP = 10;			//结果是否翻倍响应，服务端使用
	const GET_SINGER_CARD_REQ = 11;		//获取翻倍单张卡牌请求，客户端使用
	const GET_SINGER_CARD_RESP = 12;	//获取翻倍单张卡牌响应，服务端使用 
	const CHAT_MSG_REQ = 14;			//聊天消息请求，客户端使用
	const CHAT_MSG_RESP = 15;			//聊天消息响应，服务端使用
}

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
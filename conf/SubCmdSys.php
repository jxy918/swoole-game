<?php
namespace Game\Conf;

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
	
	//tcp协议子命令字，此处举例处理
	const TCP_GET_HEART_ASK = 1	;		//处理心跳响应
}                                                                                                      ;
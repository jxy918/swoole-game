/** 
 * 主命令字定义，（可以有多个主命令字，每个主命令字对应一个子命令字） 
 */
var MainCmdID = {
    CMD_SYS               :   1, /** 系统类（主命令字）- 客户端使用 **/
}//MainCmdID


/** 
 *子命令字定义，h5客户端也应有一份对应配置，REQ结尾一般是客户端请求过来的子命令字， RESP服务器返回给客户端处理子命令字
 */
var SUB_CMD_SYS = {
    GET_CARD_REQ :  1, 
    GET_CARD_RESP :  2, 
    SEND_CARD_REQ :  3, 
    SEND_CARD_RESP : 4,
    TURN_CARD_REQ  :  5, 
    TURN_CARD_RESP  :  6,
    CANCEL_CARD_REQ  :  7,
    CANCEL_CARD_RESP :  8,
    IS_DOUBLE_REQ :  9, 
    IS_DOUBLE_RESP : 10,
    GET_SINGER_CARD_REQ  : 11,
    GET_SINGER_CARD_RESP : 12,
    HEART_BEAT_ASK_REQ : 13,
	CHAT_MSG_REQ : 14,
	CHAT_MSG_RESP : 15

}//SUB_CMD_SYS




/** 
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理 
 *
 */
 var Route = {
    1 : {
        2 : 'getCard',    //获取卡牌
        4 : 'sendCard',
        6 : 'turnCard',
        8 : 'cancelCard',
        10 : 'isDouble',
        12 : 'getSingerCard',
		15 : 'chatMsg'
    },    
 }
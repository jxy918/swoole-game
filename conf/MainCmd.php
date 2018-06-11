<?php
namespace Game\Conf;

/** 
 * 主命令字定义，（可以有多个主命令字，每个主命令字对应一个子命令字） 
 */
 
class MainCmd {
    const CMD_SYS = 1;      //websocket系统主命令字，（主命令字）- 客户端使用

    //tcp协议子命令字，此处举例处理
	const CMD_TCP_SYS = 1;  //tcp协议系统主命令字，（主命令字）- 客户端使用
}                                                                                                       ;
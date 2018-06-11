<?php
namespace Game\Core;

/**
 * 游戏常量
 * Class GameConst
 * @package Game\Core
 */
class GameConst {
    const GM_PROTOCOL_TCP = 1;      //TCP协议
    const GM_PROTOCOL_WEBSOCK = 2;  //WEBSCOKET协议
    const GM_PROTOCOL_HTTP = 3;     //HTTP协议

    const GM_SERVER_IP = '0,0,0,0';  //服务器IP
    const GM_PROTOCOL_TCP_PORT = 9502;      //TCP协议默认端口
    const GM_PROTOCOL_WEBSOCK_PORT = 9503;  //WEBSCOKET协议默认端口
    const GM_PROTOCOL_HTTP_PORT = 9501;     //HTTP协议默认端口

    const GM_PROCESS_NAME_PREFIX = 'game';   //进程命名前缀

    const GM_LOG_LEVEL_INFO = 1;    //打印日志错误登录信息
    const GM_LOG_LEVEL_DEBUG = 2;
    const GM_LOG_LEVEL_ERROR = 3;
}


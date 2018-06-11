<?php
namespace Game\Core;

class Log {
    /**
     * 日志登录类型
     * @var array
     */
    protected static $level_info = array(
        1 => 'INFO',
        2 => 'DEBUG',
        3=>'ERROR'
    );

    /**
     * 日志等级，1表示大于等于1的等级的日志，都会显示，依次类推
     * @var int
     */
    protected static $level = 1;

    /**
     *  显示日志
     * @param string $centent
     * @param int $level
     */
    public static function show($centent = '', $level = 1) {
        if($level >= self::$level) {
            echo '[' . date('Y-m-d H:i:s') . ']  [' . self::$level_info[$level] . ']  ' . $centent . "\n";
        }
    }
}





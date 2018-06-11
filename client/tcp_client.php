<?php


/**
 * 解包，压缩包处理逻辑
 */
class Packet {
    /**
     * 根式化数据
     */
    public static function packFormat($msg = "OK", $code = 0, $data = array()) {
        $pack = array(
            "code" => $code,
            "msg" => $msg,
            "data" => $data,
        );
        return $pack;
    }

    /**
     * 打包数据，固定包头，4个字节为包头（里面存了包体长度），包体前2个字节为
     */
    public static function packEncode($data, $cmd = 1, $scmd = 1, $format='msgpack', $type = "tcp") {
        if ($type == "tcp") {
            if($format == 'msgpack') {
                $sendStr = msgpack_pack($data);
            } else {
                $sendStr = $data;
            }
            $sendStr = pack('N', strlen($sendStr) + 2) . pack("C2", $cmd, $scmd). $sendStr;
            return $sendStr;
        } else {
            return self::packFormat("packet type wrong", 100006);
        }
    }

    /**
     * 解包数据
     */
    public static function packDecode($str, $format='msgpack') {
        $header = substr($str, 0, 4);
        if(strlen($header) != 4) {
            return self::packFormat("packet length invalid", 100007);
        } else {
            $len = unpack("Nlen", $header);
            $len = $len["len"];
            $cmd = unpack("Ccmd/Cscmd", substr($str, 4, 6));
            $result = substr($str, 6);
            if ($len != strlen($result) + 2) {
                //结果长度不对
                return self::packFormat("packet length invalid", 100007);
            }

            if($format == 'msgpack') {
                $result = msgpack_unpack($result);
            }

            if(empty($result)) {
                //结果长度不对
                return self::packFormat("packet data is empty", 100008);
            }

            $result = self::packFormat("OK", 0, $result);
            $result['cmd'] = $cmd['cmd'];
            $result['scmd'] = $cmd['scmd'];
            $result['len']  = $len + 4;
            return $result;
        }
    }
}



/**
 * Person message
 */
class Person extends \ProtobufMessage
{
    /* Field index constants */
    const NAME = 1;
    const ID = 2;
    const EMAIL = 3;
    const MONEY = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::NAME => array(
            'name' => 'name',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::ID => array(
            'name' => 'id',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::EMAIL => array(
            'name' => 'email',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::MONEY => array(
            'name' => 'money',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_DOUBLE,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::NAME] = null;
        $this->values[self::ID] = null;
        $this->values[self::EMAIL] = null;
        $this->values[self::MONEY] = null;
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        $value = $this->get(self::NAME);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'id' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setId($value)
    {
        return $this->set(self::ID, $value);
    }

    /**
     * Returns value of 'id' property
     *
     * @return integer
     */
    public function getId()
    {
        $value = $this->get(self::ID);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'email' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setEmail($value)
    {
        return $this->set(self::EMAIL, $value);
    }

    /**
     * Returns value of 'email' property
     *
     * @return string
     */
    public function getEmail()
    {
        $value = $this->get(self::EMAIL);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'money' property
     *
     * @param double $value Property value
     *
     * @return null
     */
    public function setMoney($value)
    {
        return $this->set(self::MONEY, $value);
    }

    /**
     * Returns value of 'money' property
     *
     * @return double
     */
    public function getMoney()
    {
        $value = $this->get(self::MONEY);
        return $value === null ? (double)$value : $value;
    }
}

//测试发送protobuf 发送请求
$client = new swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9502, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}

$obj = new Person();
$obj->setName('hellojammy');
$obj->setId(2);
$obj->setEmail('helloxxx@foxmail.com');
$obj->setMoney(1988894.995);
$packed = $obj->serializeToString();
$data = Packet::packEncode($packed, 1, 1,'protobuf','tcp');

$client->send($data);

//接受服务器返回数据
$res =  $client->recv();
$back = Packet::packDecode($res,'protobuf');
//解析格式化数据
$data = $back['data'];
$obj->parseFromString($data);
echo $obj->getName() ."\n";
echo $obj->getEmail() ."\n";
echo $obj->getMoney() ."\n";
echo $obj->getId() . "\n";
print_r($data);
$client->close();

<?php
namespace Game\Core;

return array(	
	//db配置，需要用时可以启用
	'db'=>array(
		'dbms'=>'mysql', 
		'host'=>'192.168.1.85:3308', 
		'user'=>'web',
		'passwd'=>'aG98asg!($',
		'name'=>'accounts_mj',
		'setname'=>true,
		'charset'=> 'utf8',
		'persistent'=>false	
	),
	
	//redis配置，需要用时可以启用
	'redis'=>array(
		'host'=>'192.168.7.196',
		'port'=>6379	
	)
);

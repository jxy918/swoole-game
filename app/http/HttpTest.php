<?php
namespace Game\App\http;

use Game\Core\AStrategy;

/**
 *  http测试逻辑
 */ 
  
 class HttpTest extends AStrategy {
	/**
	 * 执行方法
	 */         
	public function exec() {		
		//处理扣金币逻辑，暂时不处理原封不动发回去
        return 'http test is ok!';
	}
}
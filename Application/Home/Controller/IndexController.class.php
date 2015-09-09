<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends BaseController {
    public function index(){
    	// if(session('?studentnum')) {
			$this->display('user_info_modify/index');
		// }
    }
    public function _empty() {
        $this->display('Errors/index');
    }
}
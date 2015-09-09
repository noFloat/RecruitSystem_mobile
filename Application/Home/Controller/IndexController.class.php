<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends BaseController {
    public function index(){
    	// if(session('?studentnum')) {
			$this->display('User/process');
		// }
    }

    private function userInit(){
    	$openuser = M('useropen');
    	$condition = array(
    			"open_id" => session('open_id')
    		);
    	$stu = $openuser->where($condition)->find();
    	$this->checkState($stu);
    }

    private function checkState($stu){
    	if($stu = NULL){
    		$this->display('User/infoModify');
    	}elseif(1){

    	}
    }
    public function _empty() {
        $this->display('Errors/index');
    }
}
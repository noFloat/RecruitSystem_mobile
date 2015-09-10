<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends BaseController {

	private $users;
	private $reservation;

    public function index(){
    	// if(session('?studentnum')) {
    		$this->sqlInit();
    		$this->checkLogin();
			$this->userInit();
		// }
    }

    private function sqlInit(){
    	$this->users = M('users');
    	$this->reservation = M('reservation');
    }

    private function checkLogin(){
    	// $condition = array(
    	// 		"studentnum" => I('post.studentnum'),
    	// 		"password" => I('post.password')
    	// 	);
    	$condition = array(
    			"studentnum" => 1,
    			"password" => 1
    		);
    	$stu = $this->users->where($condition)->find();
    	if($stu) {
            if($stu['state'] == '1') {
            	session('user_id',$stu['id']);
            	session('studentnum',$stu['studentnum']);
                $this->userInit($stu);
            } else {
                $this->error('账号已跪');
            }
    	} else if(session('testnum') == 5) {
    		if(!session('?lasttime')) {
    			session('lasttime',$nowtime);
    		} else if($nowtime - session('lasttime') < 600) {
                echo 0;
    		} else {
    			session('testnum',0);
    		}
            $this->error('超过尝试次数');
    	} else {
    		session('testnum',session('testnum') + 1);
            $this->error('密码错误');
    	}
    }

    private function userInit($stu){
    	$condition = array(
    		"user_id" => session('user_id'),
    		);
    	$applyState = $this->reservation->where($condition)->find();
        $this->checkState($stu,$applyState);
    }

    private function checkState($stu,$applyState){
    	if($stu['phone'] == NULL){
            $this->assign('stu',$stu);
    		$this->display('User/infoModify');exit;
    	}elseif($applyState == NULL){
    		$this->display('apply/my_apply');exit;
    	}else{
    		$this->display('User/apply');exit;
    	}
    }
    public function _empty() {
        $this->display('Errors/index');
    }
}
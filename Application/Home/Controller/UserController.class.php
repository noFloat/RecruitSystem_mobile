<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {

    private $users;
    public function index(){
    	$this->display('User/infoModify');
    }

    private function sqlInit(){
        $this->users = M('users');
    }

    public function infoModify(){
        $this->sqlInit();
        $user_phone = I('post.user_phone');
        if(!is_numeric($user_phone)||strlen($user_phone)!= 11){
            $this->ajaxReturn('');
            exit;
        }
        $content = array(
            "phone"=>$user_phone,
            "introduce"=>I('post.introduce')   
        );
        $condition = array(
            "id" =>session('user_id'),
        );
        $this->users->where($condition)->save($content);
        $this->display("orglist/orglist");
    }

    private function checkUser(){


    }
    public function addUser(){

    }
    public function _empty() {
        $this->display('Errors/index');
    }
}
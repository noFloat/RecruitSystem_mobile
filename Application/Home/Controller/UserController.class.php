<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {

    private $users;
    private $department;
    private $reservation;
    private $department_intro;
    private $organization;
    private $flow;

    private function sqlInit(){
        $this->users = M('users');
        $this->department = M('department');
        $this->reservation = M('reservation');
        $this->department_intro = M('intro');
        $this->organization = M('organization');
        $this->flow = M('flow');
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

    public function info(){
        $this->sqlInit();
        $condition = array(
            "id"=>session('user_id')
        );
        $stu = $this->users->where($condition)->find();
        $this->assign('stu',$stu);
        $this->display('User/info');
    }

    public function apply(){
        $this->sqlInit();
        $user_id = session('user_id');
        $condition = array(
            "user_id" =>$user_id,
        );
        $this->reservation->where($condition)->select();
        $flow = $this->reservation->where($condition)->join('flow ON reservation.depatment_id = flow.dept_id ')->select();
        $this->assign('flow',$flow);
        $this->display('User/apply');
    }

    public function infoModifyShow(){
        $this->sqlInit();
        $condition = array(
            "id"=>session('user_id')
        );
        $stu = $this->users->where($condition)->find();
        $this->assign('stu',$stu);
        $this->display('User/infoModify');
    }

    public function addUser(){

    }

    public function _empty() {
        $this->display('Errors/index');
    }
}
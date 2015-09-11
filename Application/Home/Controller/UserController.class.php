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
        if($stu['introduce'] == ''){
            $stu['introduce'] = '这个人很懒,什么都没有写';
        }
        $stu['academy'] = $this->checkAcademy($stu['academy_id']);
        $this->assign('stu',$stu);
        $this->display('User/info');
    }

    public function checkAcademy($i){
        $academy = array(
            "1" => "通信工程学院",
            "2" => "传媒艺术学院",
            "3" => "计算机技术学院",
            "4" => "软件工程学院",
            "5" => "经济管理学院",
            "6" => "理学院",
            "7" => "自动化学",
            "8" => "先进制造工程学院",
            "9" => "生物信息学院",
            "10" => "国际学院",
            "11" => "光电/半导体学院",
            "12" => "体育学院",
            "13" => "法学院"
        );
        $now_aca = $academy[$i];
        return $now_aca;
    }

    public function apply(){
        $this->sqlInit();
        $user_id = session('user_id');
        $condition = array(
            "user_id" =>$user_id,
        );
        $choose_dep = $this->reservation->where($condition)->field('depatment_id')->select();
        $choose_dep_num = $this->reservation->where($condition)->count();
        $all_dep = array();
        for($i = 0;$i < $choose_dep_num ; $i++){
            $condition = array(
                "dept_id"=> $choose_dep[$i]['depatment_id']
            );
            $condition2 = array(
                "id" => $choose_dep[$i]['depatment_id']
            );
            $condition3 = array(
                "depatment_id"=> $choose_dep[$i]['depatment_id'],
                "user_id" => session('user_id')
            );
            $all_dep[$i]['content'] = $this->flow->where($condition)->select();
            $dep_name = $this->department->where($condition2)->field('department,id')->find();
            $flow_num = $this->flow->where($condition)->count();
            $flow = $this->reservation->where($condition3)->select();
            $class_name = array(
                0 => 'red step',
                1 => 'ora step',
                2 => 'yell step',
                3 => 'gre step',
                4 => 'cyan step',
                5 => 'blue step',
            );
            for($z = 0 ;$z < $flow_num;$z++){
                if($z < $flow[0]['flow_id']){
                    $all_dep[$i]['content'][$z]['class'] = $class_name[$z];
                }else{
                    $all_dep[$i]['content'][$z]['class'] = $class_name[$z]." off";
                }
            }
            $all_dep[$i]['dep_id'] = $dep_name['id'];
            $all_dep[$i]['dep'] = $dep_name['department'];
        }
        var_dump($all_dep[0]);
        $this->assign('all_dep',$all_dep);
        $flow = $this->reservation->where($condition)->join('flow ON reservation.depatment_id = flow.dept_id ')->field('reservation.flow_id,reservation.depatment_id,flow.type')->select();
        $flow_num = $this->reservation->where($condition)->join('flow ON reservation.depatment_id = flow.dept_id ')->count();
        $this->set($flow,$flow_num);
        $this->assign('flow',$flow);
        $this->display('User/apply');
    }
    public function set($flow,$flow_num){
        $test = $flow;
        $dept = array();
        $num = array('1' => 0, '2' => 0);
        foreach($test as $key => $val) {
            $_type = $val['type'];
            if($_type == 1) {
                $dept[] = '第' . ++$num[$_type] . '轮面试';
            } else if ($_type == 2) {
                $dept[] = '第' . ++$num[$_type] . '轮笔试';
            }
        }
        if(count($dept) <= 2) {
            $dept = array_map(function($e) {
                return mb_substr($e, 3, 2, 'utf-8');
            }, $dept);
        }

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

    public function process(){
        $this->display('User/process');
    }

    public function _empty() {
        $this->display('Errors/index');
    }
}
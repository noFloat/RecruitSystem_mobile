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
        $choose_dep = $this->reservation->where($condition)->field('depatment_id,state')->select();
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
            $now_type = $this->set($all_dep[$i]['content']);  
            for($z = 0 ;$z < $flow_num;$z++){
                if($z < $flow[0]['flow_id']){
                    $all_dep[$i]['content'][$z]['class'] = $class_name[$z];
                }else{
                    $all_dep[$i]['content'][$z]['class'] = $class_name[$z]." off";
                }
                if($z+1 == $flow[0]['flow_id'] &&$choose_dep[$i]['state'] == 0){
                    $all_dep[$i]['content'][$z]['class'] = $class_name[$z]." off";
                    $all_dep[$i]['content'][$z]['type'] = '拒绝';
                }else{
                    $all_dep[$i]['content'][$z]['type'] = $now_type[$z];             
                }
            }      
            $all_dep[$i]['dep_id'] = $dep_name['id'];
            $all_dep[$i]['dep'] = $dep_name['department'];
        }
        $this->assign('all_dep',$all_dep);
        $this->display('User/apply');
    }
    public function set($flow){
        $test = $flow;
        $dept = array();
        $num = array('1' => 0, '2' => 0,'3'=>0);
        foreach($test as $key => $val) {
            $_type = $val['type'];
            if($_type == 1) {
                $dept[] = '第' . ++$num[$_type] . '轮面试';
            } elseif ($_type == 2) {
                $dept[] = '第' . ++$num[$_type] . '轮笔试';
            } elseif($_type == 3) {
                $dept[] = '录取';
            }
        }
        if(count($dept) <= 2) {
            $dept = array_map(function($e) {
                return mb_substr($e, 3, 2, 'utf-8');
            }, $dept);
        }
        return $dept;

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
        $this->sqlInit();
        $state = I('get.state');
        $this->assign('state',$state);
        $dep_id = I('get.dep_id');
        $condition = array(
            "user_id" => session('user_id'),
            "depatment_id" => $dep_id
        );
        $flow_id = $this->reservation->where($condition)->field('flow_id')->find();
        $condition2 = array(
            "id" => $flow_id['flow_id']
        );
        $flow = $this->flow->where($condition2)->find();
        $this->assign('flow',$flow);
        $condition3 = array(
            "id" => $dep_id
        );
        $now_dep = $this->department->where($condition3)->find();
        $this->assign('dep',$now_dep);
        $condition4 = array(
            "dept_id" => $dep_id
        );
        $all_flow = $this->flow->where($condition4)->select();
        $this->pic($all_flow,$dep_id);
        $this->display('User/process');
    }

    public function pic($all_flow,$dep_id){
        $user_id = session('user_id');
            $class_name = array(
                0 => 'red step',
                1 => 'ora step',
                2 => 'yell step',
                3 => 'gre step',
                4 => 'cyan step',
                5 => 'blue step',
            );
            $all_dep = array();
            $now_type = $this->set($all_flow);  
            $condition = array(
                "dept_id"=> $dep_id
            );
            $condition3 = array(
                "depatment_id"=> $dep_id,
                "user_id" => session('user_id')
            );
            $flow_num = $this->flow->where($condition)->count();
            $flow = $this->reservation->where($condition3)->select();
            for($z = 0 ;$z < $flow_num;$z++){
                if($z < $flow[0]['flow_id']){
                    $all_dep[$z]['class'] = $class_name[$z];
                }else{
                    $all_dep[$z]['class'] = $class_name[$z]." off";
                }
                if($z+1 == $flow[0]['flow_id'] &&$choose_dep[$i]['state'] == 0){
                    $all_dep[$z]['class'] = $class_name[$z]." off";
                    $all_dep[$z]['type'] = '拒绝';
                    $all_dep['state'] = "拒绝";
                }else{
                    $all_dep[$z]['type'] = $now_type[$z];  
                    $all_dep['state'] = $now_type[$z];           
                }
            }    
            $this->assign('all_dep',$all_dep);
        }

    public function _empty() {
        $this->display('Errors/index');
    }
}
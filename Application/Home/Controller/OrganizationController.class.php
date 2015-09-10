<?php
namespace Home\Controller;
use Think\Controller;

class OrganizationController extends Controller {
	private $department;
	private $reservation;
	private $department_intro;
	private $organization;
	private $flow;
	public function index(){
		$this->display('orglist/orglist');
	}

	private function sqlInit(){
		$this->department = M('department');
		$this->reservation = M('reservation');
		$this->department_intro = M('intro');
		$this->organization = M('organization');
		$this->flow = M('flow');
	}

	public function setDepartment(){
		$this->sqlInit();
		if(I('org_id') == NULL){
			$this->redirect('Organization/index');
		}
		$condition = array(
			"show" =>I('org_id')
		);
		$condition3 = array(
			"id" => I('org_id')
		);
		$org = $this->organization->where($condition3)->find();
		$this->assign('org',$org);
		$dep = $this->department->where($condition)->select();
		$dep_num = $this->department->where($condition)->count();
		$condition2 = array(
			"user_id"=>	session('user_id'),
		);
		$user_id = session('user_id');
		$choose = $this->reservation->where("user_id = $user_id ")->join('department ON reservation.depatment_id = department.id ')->select();
		$choose_num = $this->reservation->where("user_id = $user_id ")->join('department ON reservation.depatment_id = department.id ')->count();
		for($d = 0;$d < $choose_num;$d++){
			for($i = 0;$i < $dep_num;$i++){
				if($dep[$i]['department'] == $choose[$d]['department']){
					$dep[$i]['state'] = "已报名";
				}else{//变量赋值有坑
				}
			}
		}
		$this->assign('dep',$dep);
		$this->display('orglist/deparment');
	}

	public function orglist(){
		$this->display('orglist/orglist');
	}

	public function department(){
		
		$this->redirect('orglist/deparment');
	}

	public function signup(){//显示信息
		$this->sqlInit();
		$condition = array(
			'department_id' => I("get.dep_id")
		);
		$content = $this->department_intro->where($condition)->find();
		session('now_dep_id',I('get.dep_id'));
		$condition2 = array(
			"id" => I("get.dep_id")
		);
		$deparment = $this->department->where($condition2)->find();
		$this->assign('dep_name',$deparment);
		$this->assign('content',$content);

		$condition3 = array(
			'user_id' => session('user_id'),
			'depatment_id' =>session('now_dep_id')
		);

		$re = $this->reservation->where($condition3)->find();
		if($re){
			$this->assign('disabled','disabled');
		}else{
			$this->assign('disabled','');
		}

		$this->display('orglist/deparmentintro');
	}

	public function addPeo(){//添加人
		$this->sqlInit();
		$condition = array(
			"user_id" =>session('user_id'),
			"depament_id"=>session('now_dep_id')
		);
		$stu = $this->reservation->where($condition)->find();
		if($stu){
			$this->display('orglist/orglist');exit;
		}
		$condtion2 = array(
			"department_id"=>session('now_dep_id')
		);
		$flow = $this->flow->where($condtion2)->select();
		$content  = array(
			"user_id" =>session('user_id'),
			"flow_id" =>$flow[0]['id'],
			"department_id" => session('now_dep_id')
		);
		$this->reservation->data($content)->add();
		$user_id = session('user_id');
		$reservation = $this->reservation->where("user_id = '$user_id'")->join('flow ON reservation.depatment_id = flow.dept_id')->select();
		var_dump($reservation);exit;
		$this->display('User/process');
	}

    public function _empty() {
        $this->display('Errors/index');
    }
}
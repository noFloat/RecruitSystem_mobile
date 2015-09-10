<?php
namespace Home\Controller;
use Think\Controller;

class OrganizationController extends Controller {
	private $department;
	private $reservation;
	private $department_intro;

	public function index(){
		$this->display('orglist/orglist');
	}

	private function sqlInit(){
		$this->department = M('department');
		$this->reservation = M('reservation');
		$this->department_intro = M('departmen_intro');
	}

	public function setDepartment(){
		$this->sqlInit();
		if(I('org_id') == NULL){
			$this->redirect('Organization/index');
		}
		$condition = array(
			"show" =>I('org_id')
		);
		$dep = $this->department->where($condition)->field('department')->select();
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
					$site = ""; 
					$dep[$i]['upon'] = $site;
					$dep[$i]['state'] = "已报名";
				}else{//变量赋值有坑
					$site = " __URL__"; 
					$dep[$i]['upon'] = $site;
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

		$this->display('orglist/deparment');
	}

	public function signup(){
		$this->sqlInit();
		$condition = array(
			'department_id' => 3
		);
		$content = $this->department_intro->where($condition)->find();
		$this->assign('content',$content);
		$this->display('orglist/deparmentintro');
	}


    public function _empty() {
        $this->display('Errors/index');
    }
}
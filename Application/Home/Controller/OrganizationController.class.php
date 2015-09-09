<?php
namespace Home\Controller;
use Think\Controller;

class OrganizationController extends Controller {
	private $department;
	public function index(){

	}

	private function sqlInit(){
		$this->department = M('department');
	}

	public function setDepartment(){
		$this->sqlInit();
		$condition = array(
			"show" =>I('org_id')
		);
		$dep = $this->department->where($condition)->field('department')->select();
		$this->assign('dep',$dep);
		$this->display('orglist/deparment');
	}

    public function _empty() {
        $this->display('Errors/index');
    }
}
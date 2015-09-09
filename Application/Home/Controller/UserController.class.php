<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
    public function index(){
    	$this->display('User/infoModify');
    }

    

    public function infoModify(){
        $content = array(
            "open_id" =>I('post.open_id'),
        );

        $state = $this->checkUser(){

        }
    }

    private function checkUser(){


    }
    public function addUser(){

    }
    public function _empty() {
        $this->display('Errors/index');
    }
}
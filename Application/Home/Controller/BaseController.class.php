<?php
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller {

    private $status_code;
    private $status_msg;
    private $_user_role;

    public function _before_index(){
        // if(!session('?name')) {
        //     $this->assign(array(

        //     ));
        //     $this->display('Login/index');
        //     exit;
        // } else {

        //     }
        // }
    }
}
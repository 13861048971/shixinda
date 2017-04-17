<?php
use Think\Controller;

/**
 * 帮助
 *
 */
class HelpController extends PublicController {
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$this->display();
	}
	
	//后端编码规范
    public function backend(){
		$this->display();
    }
	
	//前端编码规范
	public function frontend(){
		$this->display();
	}
	
	//UI规范
	public function ui(){
		$this->display();
	}
	
	//产品设计规范
	public function goods(){
		$this->display();
	}
		
}
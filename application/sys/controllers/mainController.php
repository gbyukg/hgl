<?php
/* 模块: m01-系统模块
 * 功能：c002-系统主界面
 * 作成者：周义
 * 作成日：2010/07/05
 * 
 * 更新履历：
 */
class  mainController extends sys_controllers_baseController {
	
	public function loginAction(){
		$this->_view->display("login.php");
		
	}
	
    /*
     * 系统功能主界面-框架结构
     */
	public function indexAction(){
		$this->_view->display("sys_main_index.php");
	}
	
	/*
	 * 系统功能主界面-上部标题区域
	 */	
	public function headerAction(){
		
		$this->_view->display("sys_main_header.php");
	}
	
	/*
	 * 系统功能主界面-左部导航区
	 */
	public function menuAction(){
		
		$this->_view->display("sys_main_menu.php");
	}
	
	public function getmenudataAction(){
		$model = new sys_models_main();
		header("Content-type:text/xml");
		echo $model->getMenuData();
	}
	
	/*
	 * 系统功能主界面-右部工作领域
	 */
	public function workAction(){
		
		$this->_view->display("sys_main_work.php");
	}
	
	
	
	

}
<?php
/* 模块: 系统模块
 * 功能：用户管理
 * 作成者：周义
 * 作成日：2011/08/01
 * 
 * 更新履历：
 */
class  userController extends sys_controllers_baseController {
	/*
	 * 用户维护首页
	 */
	public function indexAction(){
		$this->_view->assign("title","系统管理-用户管理");
		$this->_view->display("user_01.php");
	}
	 /*
	 * 得到员工列表数据
	 */
	public function getlistdataAction() {
		//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby",1); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["user_sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["user_sortParams"]["direction"] = $filter ['direction'];
		
		if($this->_request->isPost()){
			$_SESSION['user_searchParams'] = $_POST;
		}
			
		$filter['searchParams'] = $_SESSION['user_searchParams'];  //查询条件
		$model = new sys_models_user ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 新建用户
	 */
	public function newAction(){
		$this->_view->assign("title","系统管理-用户管理-新建");
		$this->_view->assign("action","new");
		$model = new sys_models_user ( );
		$this->_view->assign("roles",$model->getAllRoles()); //可用权限信息
		$this->_view->display("user_02.php");
	}
	
    /*
	 * 编辑用户
	 */
	public function updateAction(){
		$yhid = $this->_getParam("yhid");//用户id
		$this->_view->assign("title","系统管理-用户管理-修改");
		$this->_view->assign("action","update");
		$model = new sys_models_user ( );
		$this->_view->assign("baseinfo",$model->getBaseInfo($yhid));//用户基本信息
		$this->_view->assign("roles",$model->getAllRoles($yhid));//可用权限信息
		$this->_view->assign("assignedroles",$model->getAssignedRoles($yhid));//已分配权限信息
		$this->_view->display("user_02.php");
	}
	
	/*
	 * 查看用户
	 */
	public function detailAction(){
		$yhid = $this->_getParam("yhid");//用户id
		$model = new sys_models_user ( );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign("title","系统管理-用户管理-查看");
		$this->_view->assign("baseinfo",$model->getBaseInfo($yhid));//用户基本信息
		$this->_view->assign("assignedroles",$model->getAssignedRoles($yhid));//已分配权限信息
		$this->_view->display("user_03.php");
	}
	/*
	 * 取得用户信息（上一条下一条）
	 */
	public function getdetailAction(){
		//取得列表页检索条件和排序条件
		$yhid = $this->_getParam("yhid");//用户id
		$flg = $this->_getParam ( 'flg', "current" );//检索方向
		$filter['searchParams'] = $_SESSION['user_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["user_sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["user_sortParams"]["direction"];//排序
	
		$model = new sys_models_user ( );
		$baseinfo = $model->getBaseInfo($yhid,$flg,$filter);		
		//没有找到记录
		if ($baseinfo == FALSE) {
			echo "false";
		} else {
		    $this->_view->assign("baseinfo",$baseinfo);//用户基本信息
		    $this->_view->assign("assignedroles",$model->getAssignedRoles($baseinfo["YHID"]));//已分配权限信息
	    	echo  $this->_view->fetchPage ( "user_03.php" ) ;
		}
	}
	/*
	 * 检查该员工编号对应的用户是否已经建立
	 */
	public function checkygbhAction(){
		$ygbh = $this->_getParam("ygbh");
		$model = new sys_models_user ( );
	    echo $model->checkYgbh($ygbh);
	}
	
	/*
	 * 自动分配单位用户ID
	 */
	public function getyhidAction(){
		$yhlx = $this->_getParam("yhlx","0");
		$yhbh = $this->_getParam("yhbh");
		$model = new sys_models_user ( );
		echo json_encode($model->getYhid($yhlx,$yhbh));
	}
	
	/*
	 * 保存用户信息
	 */
	public function saveAction(){

		$result = array (); //定义返回值

		try{
			$model = new sys_models_user ( );
			$model->beginTransaction();
			
			//必须输入项验证
			if(!$model->inputCheck($_POST)){
				$result['status'] = '3';  //必须输入项验证错误
			}elseif($_POST["action"]=="new" && $_POST["YHLX"]=="0" && $model->checkYgbh($_POST["YHBH"])!="0") {
				$result['status'] = '4';  //判断员工用户是否已经存在
			}else{
				$logicCheck = $model->logicCheck($_POST);
				if($logicCheck["status"]!="0"){
					$result["status"] = '5';  //项目合法性验证错误
					$result["message"] = $logicCheck["message"];
				}else{
					//新建
					if ($_POST ["action"] == "new") {
				        $model->createUser ($_POST);
						$result ['status'] = "0"; //登录成功
						Common_Logger::logToDb( "用户信息登录  用户编号：".$_POST ['YHID']);
					} else {
						//更新数据
						if ($model->modifyUser($_POST)) {
							$result ['status'] = "1"; //修改成功
							Common_Logger::logToDb( "用户信息修改  用户编号：".$_POST ['YHID']);
						}
					}
				}
			}
			$model->commit();	
			//返回处理结果
			echo json_encode ( $result );
		}catch (Exception $ex){
			$model->rollBack();
			throw $ex;
		}
	}
	
	/*
	 * 更改用户状态（锁定，解除锁定）
	 */
	public function changestatusAction(){
		$yhid = $_POST["yhid"];
		$action = $_POST["action"];
		$model = new sys_models_user ( );
		$model->changeStatus($yhid,$action);
	}
	
}
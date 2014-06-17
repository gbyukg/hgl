<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：    发货区信息(fhqxx)
 * 作成者：dltt-姚磊
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_fhqxxController extends cc_controllers_baseController {
	/*
	 * 初始化页面
	 */
	public function indexAction() {
		
		$this->_view->assign ( 'action', 'new' );  								
		$this->_view->assign ( "title", "仓储管理-发货区信息维护 " ); //标题
		$this->_view->display ( 'fhqxx_01.php' );
				
	}
	
	/*
     * 发货区新规画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$this->_view->assign ( 'title', '仓储管理-发货区信息新规' );
		$this->_view->assign ( "fhzht_opts", 'X');
		$this->_view->display ( 'fhqxx_03.php' );
	}
	
			
	/*
	 * 发货区信息修改 - 显示
	 */
	public function updateAction(){
		$this->_view->assign ( 'action', 'update' );
		$Model = new cc_models_fhqxx( );	
		$fhqbh = $this->_getParam ( "flg" );
		$rec = $Model->getmingxi( $fhqbh );	 //显示修改页面								
		$this->_view->assign ( 'title', '仓储管理-发货区信息修改' );
		$this->_view->assign ( "fhzht_opts", array ('9'=>'请选择','1' => '启用', 'X' => '禁用' ) );
		$this->_view->assign ( "rec", $rec );	
		$this->_view->display ( "fhqxx_03.php"  );
	}
	
	/*
	 * 查询 显示发货区信息
	 */
	public function getlistdataAction(){
		$Model = new cc_models_fhqxx( );
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", "2" ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fhqxx_searchParams'] = $_POST;
				unset($_SESSION['fhqxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fhqxx_filterParams'] = $_POST;
				unset($_SESSION['fhqxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['fhqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['fhqxx_searchParams'];  //固定查询条件
		//H01DB012422	
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $Model->getlistdata($filter);
	}
	/*
	 * 明细页面初始化
	 */
	public  function getmingxiAction(){
		
		$Model = new cc_models_fhqxx( );
		$filter ['orderby'] = $this->_getParam ( "orderby", "1" ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$this->_view->assign("title","仓储管理-发货区信息详情");
		$con = $this->_getParam ( "flg" );	
		$rec = $Model->getmingxi ($con,$filter);
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec ); //库区类型		
		$this->_view->display ( "fhqxx_02.php" );
	}
	/*
	 * 上下页
	 */
	
	public  function getsxbhAction(){
		
		$Model = new cc_models_fhqxx( );	
		$con = $this->_getParam ( "FHQBH" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向		
		$filter['filterParams'] = $_SESSION['fhqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['fhqxx_searchParams'];  //固定查询条件				
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序	
		$rec = $Model->getmingxi ($con,$filter,$flg);
		$this->_view->assign("title","仓储管理-发货区信息详情");
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "fhqxx_02.php" ) );
		}
	/*
	 * 删除发货区信息
	 */
	}
	public function deletefhxxAction(){
		
		$Model = new cc_models_fhqxx( );
		$fhqbh = $this->_getParam ( "flg" );//发货区编号
		$fhqzht = $this->_getParam ( "ckzht");//状态
	
		if($fhqzht == "1"){      // 禁用发货区：$fhqzht == "1"   
		
				$Model->deletefhxx($fhqbh,$fhqzht);
				Common_Logger::logToDb( "发货区维护  禁用发货区  发货区编号：".$fhqbh);
				//$result ['status'] = 0;  			
				
			}else{
				$Model->deletefhxx($fhqbh,$fhqzht);
				Common_Logger::logToDb( "发货区维护  启用发货区  发货区编号：".$fhqbh);
				//$result ['status'] = 1;
			}		
	}

	
	/**
	 * 保存发货区休息信息
	 */
	
	public function saveAction (){
		
		$result = array (); //定义返回值
		$result ['FHQBH'] = $_POST ['FHQBH']; //发货区编号
		$model = new cc_models_fhqxx();				
		if ($_POST ['action'] == 'new') {
		
			//插入新数据
			if ($model->insertFhqbh () == false) {
				$result ['status'] = 2; //发货区编号已存在
			} else {
				$result ['FHQBH'] = $_POST ['FHQBH']; //发货区编号
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "发货区信息新规  发货区编号：" . $_POST ['FHQBH']);
			}
		
		} else {
			//更新数据
			if ($model->updateFhqbh () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "发货区信息修改 发货区编号：" . $_POST ['FHQBH'] );
			}
		}
			
			
		echo Common_Tool::json_encode($result);
				
	}
	
	
	
	
	
	
	
	
	
}

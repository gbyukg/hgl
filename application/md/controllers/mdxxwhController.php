<?php
/******************************************************************
 ***** 模         块：       门店模块(MD)
 ***** 机         能：     门店信息维护(MDYGXXWH)
 ***** 作  成  者：        姚磊
 ***** 作  成  日：        2011/02/12
 ***** 更新履历：
 ******************************************************************/
class md_mdxxwhController extends md_controllers_baseController {
	/*
	 * 初始化页面
	 */
	public function indexAction() {
										
		$this->_view->assign ( "title", "门店管理-门店信息维护 " ); //标题
		$this->_view->display ( 'mdxxwh_01.php' );
				
	}
	
	/*
     * 门店信息初始化页面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$this->_view->assign ( "title", "门店管理-门店信息登录" ); //标题
		$this->_view->assign ( "phms_opts", array ('9'=>'请选择','0' => '精确管理', '1' => '模糊管理' ) );
    	$this->_view->display ( 'mdxxwh_03.php' );
	}
			
	/*
	 * 门店信息修改 - 显示
	 */
	public function updateAction(){
		$Model = new md_models_mdxxwh( );	
		$this->_view->assign ( 'action', 'update' );//修改						
		$this->_view->assign ( 'title', '门店管理-门店信息修改' );
		$this->_view->assign ( "phms_opts", array ('9'=>'请选择','0' => '精确管理', 'X' => '模糊管理' ) );
		$this->_view->assign ( "rec",$Model->getMdxx($this->_getParam('mdbh','')) );	
		$this->_view->display ( "mdxxwh_03.php"  );
	}
	
	/*
	 * 查询 显示门店信息
	 */
	public function getlistdataAction(){
	//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['mdxxwh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['mdxxwh_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['mdxxwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new md_models_mdxxwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	/*
	 * 明细页面初始化
	 */
	public  function detailAction(){
		
		$Model = new md_models_mdxxwh( );
		//画面项目赋值
		$this->_view->assign("title","门店管理-门店信息详情");
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $Model->getMdxx($this->_getParam('mdbh',''))); 
		$this->_view->assign ( "phms_opts", array ('9' => '','0' => '精确管理', 'X' => '模糊管理' ) );
		$this->_view->display ( "mdxxwh_02.php" );
	}
	/*
	 * 上下页
	 */
	
	public  function getmdxxAction(){
		
		$mdbh = $this->_getParam ('mdbh','' );//当前门店编号
		$flg = $this->_getParam ( 'flg','current' ); //检索方向	
		$filter['filterParams'] = $_SESSION['mdxxwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件	
		$filter ['orderby'] = $_SESSION["sortParams"]["orderby"]; //排序列
		$filter ['direction'] = $_SESSION["sortParams"]["direction"]; //排序方式
		$Model = new md_models_mdxxwh( );	
		$rec = $Model->getMdxx ($mdbh,$filter,$flg);
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "phms_opts", array ('9'=>'','0' => '精确管理', 'X' => '模糊管理' ) );
			$this->_view->assign ( "rec", $rec );
			echo $this->_view->fetchPage ( "mdxxwh_02.php" );
		}
	/*
	 * 删除门店信息
	 */
	}
	public function deleteAction(){
		
		$Model = new md_models_mdxxwh( );
		echo json_encode ($Model->deletemdxx($this->_getParam ( 'flg')));
	}
	/**
	 * 保存修改
	 *
	 */
	public function saveAction(){
	$result = array (); //定义返回值
	$result['mdbh'] = $_POST ['MDBH'];
	    try{
			$model = new md_models_mdxxwh ( );	
			$model->beginTransaction();
			
			//员工登录
			if ($_POST['action']=='new'){
				//插入新数据
			   if ($model->insertMdxx () == false) {
				$result ['status'] = 2; //编号已存在
			   }else{
					$result['status'] = 0;	//登录成功				
				    Common_Logger::logToDb ( "门店信息登录 门店 编号：" . $_POST ['MDBH']);
			   }
			}else{		
				   			    	  
			    if ($model->updateMdxx () == false) {
				$result ['status'] = 3; //时间戳已变化
			    }else{
					$result['status'] =1;					
				    Common_Logger::logToDb ( "门店信息修改  门店编号：" . $_POST ['MDBH']);

			    }
			}			
			$model->commit();	
			echo json_encode($result);	   			    
	    }catch (Exception $ex){
			$model->rollBack();
			throw $ex;
	    
	    }
	}
	
	/*
	 * 更改员工使用状态
	 */
	public function changestatusAction() {
		
		$model = new md_models_mdxxwh();
		$model->updateStatus ( $_POST ['mdbh'], $_POST ['yyzht'] );
		//写入日志
		Common_Logger::logToDb( ($_POST ['yyzht']=='X'? "门店禁用":"门店启用")." 门店编号：".$_POST ['mdbh']);
	
	}
	/*
	 * 判断员工编号是否存在
	 */
	public function checkAction() {

		$model = new md_models_mdxxwh ( );
		
		if ($model->getMdxx ( $this->_getParam ( 'mdbh')) == FALSE) {
			echo "0"; //不存在
		} else {
			echo "1"; //存在
		}
	}

	
	/**
	 *  门店信息保存
	 */
	public function savedateAction(){
	$result = array (); //定义返回值
	$result['data'] = $_POST ['MDBH'];
			$model = new md_models_mdxxwh ( );		  
			if ($model->insertMdxx () == false) {
				$result ['status'] = 2; //编号已存在
			}else{
					$result['status'] = 0;					
				    Common_Logger::logToDb ( "门店信息新规  编号：" . $_POST ['MDBH']);
			}		
				echo json_encode($result);	   			    

	}
	
}

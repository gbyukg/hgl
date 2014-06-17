<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   打包台信息(dbtxx)
 * 作成者：侯殊佳
 * 作成日：2011/05/12
 * 更新履历：
 *********************************/
class cc_dbtxxController extends cc_controllers_baseController {
	
	/*
     *  打包台信息列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-打包台信息维护' );
		$this->_view->display ( 'dbtxx_01.php' );
	}
	
	/*
     * 打包台信息登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$model = new cc_models_dbtxx ( );
		
		$cangku = $model->getCangkuList ();
		$this->_view->assign ( 'title', '仓储管理-打包台信息登陆' );
		$this->_view->assign ( "cangku", $cangku );
		$this->_view->display ( 'dbtxx_02.php' );
	}
	
	/*
     *  打包台信息详情画面
     */
	public function detailAction() {
		// 打包台信息信息取得
		$model = new cc_models_dbtxx ( );
		$dbtbh = $this->_getParam ( "dbtbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$rec = $model->getDbtxx ( $dbtbh,$ckbh );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '仓储管理-打包台信息详情' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'dbtxx_03.php' );
	}
	

	/*
	 *  打包台信息信息修改
	 */
	public function updateAction() {
		$model = new cc_models_dbtxx ( );
		
		$dbtbh = $this->_getParam ( "dbtbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$rec = $model->getDbtxx ( $dbtbh,$ckbh);
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '仓储管理-打包台信息修改' );
		$this->_view->assign ( "rec", $rec );	
		$this->_view->display ( 'dbtxx_02.php' );
	}
	
	/*
	 * 得到 打包台列表数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	

		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['dbtxx_searchParams'] = $_POST;
				unset($_SESSION['dbtxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['dbtxx_filterParams'] = $_POST;
				unset($_SESSION['dbtxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['dbtxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['dbtxx_searchParams'];  //固定查询条件
		$model = new cc_models_dbtxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 改变 打包台使用状态
	 */
	public function getckzhtAction() {	
	
			$model = new cc_models_dbtxx ( );
					//开始一个事务
			$ckzht = $model->getCkzht ( $_POST ['ckbh'] );
			if ($ckzht != '1') {
				$result ['status'] = 3; 
				echo Common_Tool::json_encode( $result );     //返回处理结果
			}else{
			try {
			$model -> beginTransaction ();		
			$model->updateStatus ( $_POST ['dbtbh'], $_POST ['dbtzht'] ,$_POST ['ckbh']);	
			Common_Logger::logToDb( "打包台信息维护 启用打包台 打包台编号：".$_POST ['dbtbh']);  		//写入日志
			$result ['status'] = 0; 
				echo Common_Tool::json_encode( $result );     //返回处理结果
			$model -> commit();            //事务提交
			}catch ( Exception $e ) {
			$model -> rollBack();			//事务回滚
     		throw $e;
		}
			
		} 
	}
	
	/*
	 *  打包台编号存在验证				
	 */
	public function checkAction() {
		
		$model = new cc_models_dbtxx ( );
		$dbtbh = $this->_getParam ( "dbtbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		if ($model->getDbtxx ( $dbtbh,$ckbh) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 *  打包台信息保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值

		$result ['dbtbh'] = $_POST ['DBTBH']; //编号
		$result ['ckbh'] = $_POST['CKBH'];
		
		try{
		$model = new cc_models_dbtxx ( );
		
		$model->beginTransaction();
		
		// 打包台信息登录
		if ($_POST ['action'] == 'new') {
		//插入新数据

			if ($model->insertDbtxx () == false) {
				$result ['status'] = 2; //编号已存在

			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb( "打包台信息登录  打包台编号：".$_POST ['DBTBH']);
			}
		
		} else {
			//更新数据
			if ($model->updateDbtxx () == false) {
					$result ['status'] = 3;
			}
			else{
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb( "打包台信息修改  打包台编号：".$_POST ['DBTBH']);
			}
		
		}
		$model->commit();
		

		echo Common_Tool::json_encode($result);
	
	}catch (Exception $ex){
			$model->rollBack();
			throw $ex;
		}
}
	
	/*
	 * 取得 打包台信息  上一条,下一条
	 */
	public function getdbtxxAction() {
		$dbtbh = $this->_getParam ( "dbtbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['dbtxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['dbtxx_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		
		
		
		$model = new cc_models_dbtxx ( );
		$rec = $model->getDbtxx ( $dbtbh,$ckbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "dbtxx_03.php" ) ;
		}
	}
	
	

/*
	 * 更改 打包台使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_dbtxx ( );
		
		$model->updateStatus ( $_POST ['dbtbh'], $_POST ['dbtzht'],$_POST['ckbh'] );
	
		Common_Logger::logToDb( ($_POST ['dbtzht']=='1'? "打包台信息维护  暂停打包台暂停 打包台编号：":"打包台信息维护  禁用打包台 打包台编号：")." 打包台编号：".$_POST ['dbtbh']);
		
		
	
	}
	
	
	
	
}
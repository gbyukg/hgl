<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   散货暂存区(shzcqxx)
 * 作成者：侯殊佳
 * 作成日：2011/05/12
 * 更新履历：
 *********************************/
class cc_shzcqxxController extends cc_controllers_baseController {
	/*
     * 散货暂存区列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-散货暂存区信息维护' );
		$this->_view->display ( 'shzcqxx_01.php' );
	}
	
	/*
     * 散货暂存区登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$model = new cc_models_shzcqxx ( );
		$cangku = $model->getCangkuList ();
		$this->_view->assign ( 'title', '仓储管理-散货暂存区信息登陆' );
		$this->_view->assign ( "cangku", $cangku );
		$this->_view->display ( 'shzcqxx_02.php' );
	}
	/* 
     * 取得传送带出口数据
     */
	public function getdropdownAction()
	{
		$ckbh = $this->_getParam('ckbh');
		$model = new cc_models_shzcqxx ( );
		echo json_encode($model->getCsdList($ckbh));
	}
	
	/*
     * 散货暂存区详情画面
     */
	public function detailAction() {
		//散货暂存区信息取得
		$model = new cc_models_shzcqxx ( );
		$shzcqbh = $this->_getParam ( "shzcqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$chsdchk= $this->_getParam ( "chsdchk" );
		$rec = $model->getShzcqxx ( $shzcqbh,$ckbh,$chsdchk);
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '仓储管理-散货暂存区信息详情' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'shzcqxx_03.php' );
	}
	
	/*
	 * 散货暂存区信息修改
	 */
	public function updateAction() {
		$model = new cc_models_shzcqxx ( );
		$shzcqbh = $this->_getParam ( "shzcqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$chsdchk=$this->_getParam ( "chsdchk" );
		$rec = $model->getShzcqxx ( $shzcqbh,$ckbh,$chsdchk);			
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '仓储管理-散货暂存区信息修改' );
		$this->_view->assign ( "rec", $rec );	
		$this->_view->display ( 'shzcqxx_02.php' );
	}
	
	/*
	 * 得到散货暂存区列表数据
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
				$_SESSION['shzcqxx_searchParams'] = $_POST;
				unset($_SESSION['shzcqxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['shzcqxx_filterParams'] = $_POST;
				unset($_SESSION['shzcqxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['shzcqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['shzcqxx_searchParams'];  //固定查询条件
		$model = new cc_models_shzcqxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	

	
	/*
	 * 散货暂存区编号存在验证				
	 */
	public function checkAction() {
		
		$model = new cc_models_shzcqxx ( );
		$shzcqbh = $this->_getParam ( "shzcqbh" );
		$chsdchk = $this->_getParam ( "chsdchk");
		$ckbh = $this->_getParam ( "ckbh" );
		if ($model->getShzcqxx ( $shzcqbh,$ckbh,$chsdchk) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 散货暂存区信息保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值

		$result ['fjzcqbh'] = $_POST ['FJZCQBH']; //编号
		$result ['ckbh'] = $_POST['CKBH'];
		
		try{
		$model = new cc_models_shzcqxx ( );
		
		$model->beginTransaction();
		
		//散货暂存区登录
		if ($_POST ['action'] == 'new') {
			//插入新数据

			if ($model->insertShzcqxx () == false) {
				$result ['status'] = 2; //编号已存在

			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb( "散货暂存区信息登录  散货暂存区编号：".$_POST ['FJZCQBH']);
			}
		
		} else {
			//更新数据
			if ($model->updateShzcqxx () == false) {
					$result ['status'] = 3;//时间戳变化
			}
			else{
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb( "散货暂存区修改  散货暂存区编号：".$_POST ['FJZCQBH']);
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
	 * 取得散货暂存区信息  上一条,下一条
	 */
	public function getshzcqxxAction() {
		$shzcqbh = $this->_getParam ( "shzcqbh" );
		$chsdchk = $this->_getParam ( "chsdchk" );
		$ckbh = $this->_getParam ( "ckbh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['shzcqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['shzcqxx_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		
		
		
		$model = new cc_models_shzcqxx ( );
		$rec = $model->getShzcqxx ( $shzcqbh,$ckbh,$chsdchk, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "shzcqxx_03.php" ) ;
		}
	}
	
	
	/*
	 * 获取传送带状态信息后启用散货暂存区
	 */
	public function getchkstatusAction() {
		$shzcqbh = $this->_getParam ( 'shzcqbh', "" );
		$chsdchk = $this->_getParam ( 'chsdchk', "" );
		$ckbh = $this->_getParam ( "ckbh" );
		$result ['shzcqbh'] = $shzcqbh;
		$chkzht = $this->_getParam ( 'shzcqzht', "" );
		$model = new cc_models_shzcqxx();
		$rec = $model->getChkstatus( $ckbh,$chsdchk);
	
			if($rec == TRUE){   
				//编号为$shzcqbh的散货暂存区的传送带出口状态正常 ,进行启用操作
				$model->updateStatus ( $shzcqbh, $chkzht ,$ckbh,$chsdchk );
				Common_Logger::logToDb( "散货暂存区信息维护  启用散货暂存区  散货暂存区编号：".$shzcqbh);
				$result ['status'] = 0;     
			}else{
				//编号为$shzcqbh的散货暂存区的传送带出口状态为禁用
				$result ['status'] = 1;
			}
		echo Common_Tool::json_encode( $result );     //返回处理结果
	}
	
	/*
	 * 改变散货暂存区使用状态为禁用
	 */
	public function changestatusAction() {	
			$shzcqbh = $this->_getParam ( 'shzcqbh', "" );
			$chsdchk = $this->_getParam ( 'chsdchk', "" );
			$ckbh = $this->_getParam ( "ckbh" );
			$result ['shzcqbh'] = $shzcqbh;
			$chkzht = $this->_getParam ( 'shzcqzht', "" );
	
			$model = new cc_models_shzcqxx ( );
					
			try {
				$model -> beginTransaction ();	//开始一个事务	
				$model->updateStatus ( $shzcqbh, $chkzht ,$ckbh,$chsdchk);	
				Common_Logger::logToDb( "散货暂存区信息维护  禁用散货暂存区  散货暂存区编号：".$shzcqbh);  		//写入日志
				$result ['status'] = 0; 
				echo Common_Tool::json_encode( $result );     //返回处理结果
				$model -> commit();            //事务提交
			}catch ( Exception $e ) {
				$model -> rollBack();			//事务回滚
     			throw $e;
		} 
	}
	
	/*
	 * 得到传送带出口状态 
	 */
	public function checkchkAction (){
		$chsdchk = $this->_getParam ( "chsdchk" );
		$ckbh = $this->_getParam ( "ckbh" );
		$model = new cc_models_shzcqxx();
		$rec = $model->getChkstatus( $ckbh,$chsdchk);
		if ($rec==TRUE){
			echo "1";
		}else{
			echo "0";
		}
		
	}
	
	
	
}
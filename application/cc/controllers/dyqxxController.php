<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   待验区信息(dyqxx)
 * 作成者：侯殊佳
 * 作成日：2011/05/06
 * 更新履历：
 *********************************/
class cc_dyqxxController extends cc_controllers_baseController {
	
	/*
     * 待验区信息列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-待验区信息维护' );
		$this->_view->display ( 'dyqxx_01.php' );
	}
	
	/*
     * 待验区信息登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$model = new cc_models_dyqxx ( );
		
		$cangku = $model->getCangkuList ();
		$this->_view->assign ( 'title', '仓储管理-待验区信息登陆' );
		$con = $model->getKqlxList ();
		$this->_view->assign ( "kqlx", $con ); //库区类型
		$this->_view->assign ( "cangku", $cangku );
		$this->_view->display ( 'dyqxx_02.php' );
	}
	
	/*
     * 待验区信息详情画面
     */
	public function detailAction() {
		//待验区信息信息取得
		$model = new cc_models_dyqxx ( );
		$dyqbh = $this->_getParam ( "dyqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$rec = $model->getDyqxx ( $dyqbh,$ckbh );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '仓储管理-待验区信息详情' );
		$this->_view->assign ( "rec", $rec );
		
		
		$this->_view->display ( 'dyqxx_03.php' );
	}
	
	/*
	 * 待验区信息信息修改
	 */
	public function updateAction() {
		$model = new cc_models_dyqxx ( );
		
		$dyqbh = $this->_getParam ( "dyqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$rec = $model->getDyqxx ( $dyqbh,$ckbh);
		$cangku = $model->getCangkuList ();
		$ku = $model->getKqlxList(); //库区类型编号				
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '仓储管理-待验区信息修改' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'cangku', $cangku );
		$this->_view->assign ( "kqlx", $ku ); //库区类型			
		$this->_view->display ( 'dyqxx_02.php' );
	}
	
	/*
	 * 得到待验区信息列表数据
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
				$_SESSION['dyqxx_searchParams'] = $_POST;
				unset($_SESSION['dyqxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['dyqxx_filterParams'] = $_POST;
				unset($_SESSION['dyqxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['dyqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['dyqxx_searchParams'];  //固定查询条件
		$model = new cc_models_dyqxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 改变待验区信息使用状态
	 */
	public function changestatusAction() {	
	
			$model = new cc_models_dyqxx ( );
					//开始一个事务
			$ckzht = $model->getCkzht ( $_POST ['ckbh'] );
			if ($ckzht != '1') {
				$result ['status'] = 3; 
				echo Common_Tool::json_encode( $result );     //返回处理结果
			}else{
			try {
			$model -> beginTransaction ();		
			$model->updateStatus ( $_POST ['dyqbh'], $_POST ['dyqzht'] ,$_POST ['ckbh']);	
			Common_Logger::logToDb( "待验区信息维护  待验区启用  待验区编号：".$_POST ['dyqbh']);  		//写入日志
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
	 * 待验区信息编号存在验证				
	 */
	public function checkAction() {
		
		$model = new cc_models_dyqxx ( );
		$dyqbh = $this->_getParam ( "dyqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		if ($model->getDyqxx ( $dyqbh,$ckbh) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 待验区信息信息保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值

		$result ['dyqbh'] = $_POST ['DYQBH']; //编号
		$result ['ckbh'] = $_POST['CKBH'];
		
		try{
			$model = new cc_models_dyqxx ( );
		
			$model->beginTransaction();
		
		//待验区信息登录
			if ($_POST ['action'] == 'new') {
			
		//插入新数据

				if ($model->insertDyqxx () == false) {
					$result ['status'] = 2; //编号已存在

				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb( "待验区信息登录  待验区编号：".$_POST ['DYQBH']);
					}
		
			} else {
				//更新数据
				if ($model->updateDyqxx () == false) {
					$result ['status'] = 3;
				}
				else{
					$result ['status'] = 1; //修改成功
					Common_Logger::logToDb( "待验区信息修改  待验区编号：".$_POST ['DYQBH']);
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
	 * 取得待验区信息信息  上一条,下一条
	 */
	public function getdyqxxAction() {
		$dyqbh = $this->_getParam ( "dyqbh" );
		$ckbh = $this->_getParam ( "ckbh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['dyqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['dyqxx_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		
		
		
		$model = new cc_models_dyqxx ( );
		$rec = $model->getDyqxx ( $dyqbh,$ckbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "dyqxx_03.php" ) ;
		}
	}
	
	
	/*
	 * 获取库区状态信息
	 */
	public function getkwstatusAction() {
		$dyqbh = $this->_getParam ( 'dyqbh', "" );
		$ckbh = $this->_getParam ( "ckbh" );
		$result ['dyqbh'] = $dyqbh;
		$kwzht = $this->_getParam ( 'dyqzht', "" );
		$model = new cc_models_dyqxx();
		$rec = $model->getkwstatus( $dyqbh, $kwzht ,$ckbh);
		if($kwzht == "0"){      // 冻结待验区：$dyqzht == "0"   
			if($rec == TRUE){   
				//编号为$dyqbh的待验区的下属库位没有正在使用的库位,进行冻结操作
				$model->updateStatus ( $dyqbh, $kwzht ,$ckbh );
				Common_Logger::logToDb( "待验区信息维护  待验区冻结  待验区编号：".$dyqbh);
				$result ['status'] = 0;     
			}else{
				//编号为$dyqbh的仓库的下属库位有正在使用的库位
				$result ['status'] = 1;
			}
		} else{                // 删除待验区：$dyqzht == "X"
			if($rec == TRUE){
				//编号为$dyqbh的待验区的下属库位所有库位都处于删除状态，进行删除操作
				$model->updateStatus ( $dyqbh, $kwzht ,$ckbh);
				Common_Logger::logToDb( "待验区信息维护  待验区禁用  待验区编号：".$dyqbh);
				$result ['status'] = 0;
			}else{
				//编号为$dyqbh的待验区的下属库位有库位未处于删除状态
				$result ['status'] = 2;
			}
		}
		echo Common_Tool::json_encode( $result );     //返回处理结果
	}
	
	
	
	
	
}
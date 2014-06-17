<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库区信息(kqxx)
 * 作成者：姚磊
 * 作成日：2010/11/11
 * 更新履历：
 *********************************/
class cc_kqxxController extends cc_controllers_baseController {
	
	/*
     * 库区列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-库区信息维护' );
		$this->_view->display ( 'kqxx_01.php' );
	}
	
	/*
     * 库区详情画面
     */
	public function detailAction() {
		//库区信息取得
		$model = new cc_models_kqxx ( );
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //列表画面排序
		$this->_view->assign ( "searchkqbh", $this->_getParam ( "searchkqbh", '' ) ); //列表画面条件
		$this->_view->assign ( "searchckbh", $this->_getParam ( "searchckbh", '' ) ); //列表画面条件
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$rec = $model->getKqxx ( $ckbh, $kqbh );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'title', '库区信息详情' );
		
		$this->_view->display ( 'kqxx_03.php' );
	}
	
	/*
     * 库区登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$model = new cc_models_kqxx ( );
		$cangku = $model->getCangkuList ();
		$this->_view->assign ( 'title', '库区信息登陆' );
		$con = $model->getKqlxList ();
		$this->_view->assign ( "kqlx", $con ); //库区类型
		$this->_view->assign ( "cangku", $cangku );
		$this->_view->display ( 'kqxx_02.php' );
	}
	
	/*
	 * 库区信息修改
	 */
	public function updateAction() {
		
		$model = new cc_models_kqxx ( );

		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$rec = $model->getKqxx ( $ckbh, $kqbh, $filter );
		$cangku = $model->getCangkuList ();
		$ku = $model->getKqblxList (); //库区类型编号				
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '库区信息修改' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'cangku', $cangku );
		$this->_view->assign ( "kqlx", $ku ); //库区类型			
		$this->_view->display ( 'kqxx_02.php' );
	}
	
	/*
	 * 得到库区列表数据
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
				$_SESSION['kqxx_searchParams'] = $_POST;
				unset($_SESSION['kqxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kqxx_filterParams'] = $_POST;
				unset($_SESSION['kqxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['kqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['kqxx_searchParams'];  //固定查询条件
		$model = new cc_models_kqxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 改变库区使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_kqxx ( );
		if ($_POST ['kqzht'] == '1') {
			$ckzht = $model->getCkzht ( $_POST ['ckbh'] );
			if ($ckzht != '1') {
				echo 'false';
			} else {
				$model->updateStatus ( $_POST ['ckbh'], $_POST ['kqbh'], $_POST ['kqzht'] );
				//写入日志
				Common_Logger::logToDb ( ($_POST ['kqzht'] == '0' ? "库区冻结" : ($_POST ['kqzht'] == 'X' ? "库区删除" : "库区启用")) . " 仓库编号：" . $_POST ['ckbh'] . " 库区编号：" . $_POST ['kqbh'] );
			}
		} else {
			$model->updateStatus ( $_POST ['ckbh'], $_POST ['kqbh'], $_POST ['kqzht'] );
			//写入日志
			Common_Logger::logToDb ( ($_POST ['kqzht'] == '0' ? "库区冻结" : ($_POST ['kqzht'] == 'X' ? "库区删除" : "库区启用")) . " 仓库编号：" . $_POST ['ckbh'] . " 库区编号：" . $_POST ['kqbh'] );
		}
	}
	
	/*
	 * 库区编号存在验证				
	 */
	public function checkAction() {
		
		$model = new cc_models_kqxx ( );
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		if ($model->getKqxx ( $ckbh, $kqbh ) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 库区信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		$result ['ckbh'] = $_POST ['CKBH']; //仓库编号
		$result ['kqbh'] = $_POST ['KQBH']; //库存编号		
		$model = new cc_models_kqxx ( );
		//库存存储
		if ($_POST ['action'] == 'new') {
			//$model->beginTransaction();//开启事物
			//插入新数据
			if ($model->insertKqxx () == false) {
				$result ['status'] = 2; //库区编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "库区信息登录  仓库编号：" . $_POST ['CKBH'] . " 库区编号：" . $_POST ['KQBH'] );
			}
		
		} else {
			//更新数据
			if ($model->updateKqxx () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "库区信息修改  仓库编号：" . $_POST ['CKBH'] . " 库区编号：" . $_POST ['KQBH'] );
			}
		}
	
		echo Common_Tool::json_encode($result);
	}
	
	/*
	 * 取得库区信息  上一条,下一条
	 */
	public function getkqxxAction() {
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['kqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['kqxx_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		
		
		
		$model = new cc_models_kqxx ( );
		$rec = $model->getKqxx ( $ckbh, $kqbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "kqxx_03.php" ) );
		}
	}
}

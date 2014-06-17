<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库位信息(kwxx)
 * 作成者：苏迅
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_kwxxController extends cc_controllers_baseController {
	
	/*
     * 库位信息维护画面显示
     */
	public function indexAction() {		
		$this->_view->assign ( 'title', '仓储管理-库位信息维护' );
		$this->_view->display ( 'kwxx_01.php' );
	}
	
	/*
	 * 得到库位列表数据
	 */
	public function getlistdataAction() {
	
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
				$_SESSION['cc_kwxx_searchParams'] = $_POST;
				unset($_SESSION['kwxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kwxx_filterParams'] = $_POST;
				unset($_SESSION['cc_kwxx_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['kwxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_kwxx_searchParams'];  //固定查询条件
	
		$model = new cc_models_kwxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	/*
     * 库位信息登录
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$this->_view->assign ( 'title', '仓储管理-库位信息登录' );
		$this->_view->display ( 'kwxx_02.php' );
	}
	
	/*
     * 库位信息修改
     */
	public function updateAction() {
		
		$model = new cc_models_kwxx ( );
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$kwbh = $this->_getParam ( "kwbh" );
		//$rec = $model->getKwxx ( $ckbh, $kqbh, $kwbh, '', 'current' );
		$rec = $model->getKwxx ( $ckbh, $kqbh, $kwbh );
		$this->_view->assign ( 'action', 'update' );
		$this->_view->assign ( 'title', '仓储管理-库位信息修改' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'kwxx_02.php' );
	}
	
	/*
     * 库位信息详情画面
     */
	public function detailAction() {
		
		$model = new cc_models_kwxx ( );
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$kwbh = $this->_getParam ( "kwbh" );
		//$rec = $model->getKwxx ( $ckbh, $kqbh, $kwbh, '', 'current' );
		$rec = $model->getKwxx ( $ckbh, $kqbh, $kwbh );
		
		//画面项目赋值	
		//查询画面传递过来的查询条件(带查询的上下条用)
/*		$this->_view->assign ( "ckbhkey", $this->_getParam ( "ckbhkey", '' ) ); //仓库检索条件
		$this->_view->assign ( "kqbhkey", $this->_getParam ( "kqbhkey", '' ) ); //库区检索条件
		$this->_view->assign ( "kwbhkey", $this->_getParam ( "kwbhkey", '' ) ); //库位检索条件
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //排序*/
		
		//画面项目赋值
		$this->_view->assign ( 'title', '仓储管理-库位信息详情' );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'kwxx_03.php' );
	}
	
	/*
	 * 判断库位是否存在
	 */
	public function checkAction() {
		$model = new cc_models_kwxx ( );
		$ckbh = $this->_getParam ( "ckbh" );
		$kqbh = $this->_getParam ( "kqbh" );
		$kwbh = $this->_getParam ( "kwbh" );
		if ($model->getKwxx ( $ckbh, $kqbh ,$kwbh) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 取得库位信息
	 */
	public function getkwxxAction() {
		$ckbh = $this->_getParam ( "ckbh","" );
		$kqbh = $this->_getParam ( "kqbh" );
		$kwbh = $this->_getParam ( "kwbh","" );
		
		//检索条件
/*		$filter ['ckbhkey'] = $this->_getParam ( "ckbhkey", '' ); //检索条件
		$filter ['kqbhkey'] = $this->_getParam ( "kqbhkey", '' ); //检索条件
		$filter ['kwbhkey'] = $this->_getParam ( "kwbhkey", '' ); //检索条件
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式*/
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['ygxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_kwxx_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序		
		$model = new cc_models_kwxx ( );
		$rec = $model->getKwxx ( $ckbh,$kqbh,$kwbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "kwxx_03.php" ) );
		}
	}
	
	/*
	 * 保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值
		$result ['CKBH'] = $_POST ['CKBH'];
		$result ['KQBH'] = $_POST ['KQBH'];
		$result ['KWBH'] =($_POST ['HJPH'].$_POST ['HJLH'].$_POST ['HJSHWZH']);
		$model = new cc_models_kwxx ( );
		
		if ($_POST ['action'] == 'new') {
			//插入新数据
			if ($model->insertKwxx () == false) {
				$result ['status'] = 2; //库位信息已存在
			} else {
				$result ['status'] = 0; //库位信息登录成功
				Common_Logger::logToDb ( "库位信息登录  仓库编号：" . $_POST ['CKBH'] . " 库区编号：" . $_POST ['KQBH'] . " 库位编号：" . $result ['KWBH'] );
			}
		
		} else {
			//更新数据
			if ($model->updateKwxx () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "库位信息修改  仓库编号：" . $_POST ['CKBH'] . " 库区编号：" . $_POST ['KQBH'] . " 库位编号：" . $result ['KWBH'] );
			}
		}
		
		//返回处理结果
		echo json_encode ( $result );
	
	}
	
	/*
	 * 更改库位使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_kwxx ( );
		if ($_POST ['kwzht'] == '1') {
			$kqzht = $model->getKqzht ( $_POST ['ckbh'], $_POST ['kqbh'] );
			if ($kqzht != '1') {
				echo 'false';
			} else {
				$model->updateStatus ( $_POST ['ckbh'], $_POST ['kqbh'], $_POST ['kwbh'], $_POST ['kwzht'] );
				//写入日志
				Common_Logger::logToDb ( ($_POST ['kwzht'] == '0' ? "库位冻结" : ($_POST ['kwzht'] == 'X' ? "库位删除" : "库位启用")) . " 仓库编号：" . $_POST ['ckbh'] . " 库区编号：" . $_POST ['kqbh'] . " 库位编号：" . $_POST ['kwbh'] );
			}
		} else {
			$model->updateStatus ( $_POST ['ckbh'], $_POST ['kqbh'], $_POST ['kwbh'], $_POST ['kwzht'] );
			//写入日志
			Common_Logger::logToDb ( ($_POST ['kwzht'] == '0' ? "库位冻结" : ($_POST ['kwzht'] == 'X' ? "库位删除" : "库位启用")) . " 仓库编号：" . $_POST ['ckbh'] . " 库区编号：" . $_POST ['kqbh'] . " 库位编号：" . $_POST ['kwbh'] );
		}
	}

}
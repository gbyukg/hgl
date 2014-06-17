<?php 
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       传送带出口信息(chsdchk)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/05/12
 ***** 更新履历：

 ******************************************************************/

class cc_chsdchkController extends cc_controllers_baseController {
     /*
      * 退货区列表画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','仓储管理-传送带出口信息维护');
		$this->_view->display('chsdchk_01.php');
	}
	
	
    /*
     * 退货区登录画面显示
     */
	public function newAction(){
		$this->_view->assign('action','new');
		$this->_view->assign('title','仓储管理-传送带出口信息登录');
		$this->_view->display ( 'chsdchk_02.php' );
	}
	
	/*
     * 退货区详情画面
     */
	public function detailAction() {
		$model = new cc_models_chsdchk();
		
		//画面项目赋值
		$this->_view->assign ( 'title', '仓储管理-传送带出口信息详情' );
		$ckbh = $this->_getParam ( "ckbh" );
		$chsdchk = $this->_getParam ( "chsdchk" );
		$rec = $model->getChsdchk ( $ckbh, $chsdchk );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		$this->_view->display ('chsdchk_03.php' );
	}
	
	/*
	 * 得到退货信息
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
				unset($_SESSION['chsdchk_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['chsdchk_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['chsdchk_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cc_models_chsdchk();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 保存信息
	 */
	public function saveAction(){
		$result = array (); //定义返回值
		$result ['ckbh'] = $_POST ['CKBH']; //仓库编号
		$result ['chsdchk'] = $_POST ['CHSDCHK']; //传送带出口	
	    try{
			$model = new cc_models_chsdchk();
			$model->beginTransaction();	
			
		//库存存储
		if ($_POST ['action'] == 'new') {
			//$model->beginTransaction();//开启事物
			//插入新数据
			if ($model->insertChsdchk () == false) {
				$result ['status'] = 2; //退货区编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "传送带出口信息登录  仓库编号：" . $_POST ['CKBH'] . " 传送带出口：" . $_POST ['CHSDCHK'] );
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
	 * 判断退货区编号是否存在
	 */
	public function checkAction() {
		
		$model = new cc_models_chsdchk();
		$ckbh = $this->_getParam ( "ckbh" );
		$chsdchk = $this->_getParam ( "chsdchk" );
		if ($model->getChsdchk ($ckbh,$chsdchk) == FALSE) {
			echo "0"; //不存在
		

		} else {
			echo "1"; //存在
		}
	}
	
	/*
	 * 取得退货区信息  上一条,下一条
	 */
	public function getchsdchkAction() {
		$ckbh = $this->_getParam ( "ckbh" );
		$chsdchk = $this->_getParam ( "chsdchk" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['chsdchk_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new cc_models_chsdchk();
        $rec = $model->getChsdchk ($ckbh,$chsdchk, $filter, $flg);
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo $this->_view->fetchPage ( "chsdchk_03.php" );
		}
	}
	/*
	 * 更改退货区使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_chsdchk();
		 //如果启用传送带出口为正常状态  预先判断仓库是否为正常状态
	    if ($_POST ['chsdchkzht'] == '1') { 
			$ckzht = $model->getCkzht ( $_POST ['ckbh'] );
			if ($ckzht != '1') {
				echo 'false';
			} else {
				$model->updateStatus ( $_POST ['ckbh'], $_POST ['chsdchk'], $_POST ['chsdchkzht'] );
				//写入日志
				Common_Logger::logToDb (($_POST ['chsdchkzht'] == 'X' ? "传送带出口禁用" : "传送带出口启用") . " 仓库编号：" . $_POST ['ckbh'] . " 传送带出口：" . $_POST ['chsdchk'] );
			}
		} else {
			$model->updateStatus ( $_POST ['ckbh'], $_POST ['chsdchk'], $_POST ['chsdchkzht'] );
			//写入日志
			Common_Logger::logToDb (($_POST ['chsdchkzht'] == 'X' ? "退货区禁用" : "退货区启用") . " 仓库编号：" . $_POST ['ckbh'] . " 传送带出口：" . $_POST ['chsdchk'] );
		}
	}
	

	

	
	
}
?>
<?php 
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       发货出货口信息(fhchhkxx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/05/17
 ***** 更新履历：

 ******************************************************************/

class cc_fhchhkxxController extends cc_controllers_baseController {
     /*
      * 退货区列表画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','仓储管理-发货出货口信息维护');
		$this->_view->display('fhchhk_01.php');
	}
	
	
    /*
     * 退货区登录画面显示
     */
	public function newAction(){
		$this->_view->assign('action','new');
		$this->_view->assign('title','仓储管理-发货出货口信息登录');
		$this->_view->display ( 'fhchhk_02.php' );
	}
		/*
     * 员工修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new cc_models_fhchhkxx ( );
		
		//画面项目赋值
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$rec = $model->getFhchhkxx( $ckbh, $fhqbh,$chhkbh);			
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '仓储管理—发货出货口信息修改' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'fhchhk_02.php' );
	}
	
	/*
     * 退货区详情画面
     */
	public function detailAction() {
		$model = new cc_models_fhchhkxx();
		
		//画面项目赋值
		$this->_view->assign ( 'title', '仓储管理-发货出货口信息详情' );
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$rec = $model->getFhchhkxx ( $ckbh,$fhqbh,$chhkbh );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		$this->_view->display ('fhchhk_03.php' );
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
				unset($_SESSION['fhchhkxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fhchhkxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['fhchhkxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cc_models_fhchhkxx();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 保存信息
	 */
	public function saveAction(){
		$result = array (); //定义返回值
		$result ['ckbh'] = $_POST ['CKBH']; //仓库编号
		$result ['fhq'] = $_POST ['FHQ']; //发货区	
		$result ['chhkbh'] = $_POST ['CHHKBH'];//出货口编号
	    try{
			$model = new cc_models_fhchhkxx();
			$model->beginTransaction();	
			
		//库存存储
		if ($_POST ['action'] == 'new') {
			//$model->beginTransaction();//开启事物
			//插入新数据
			if ($model->insertFhchhkxx () == false) {
				$result ['status'] = 2; //退货区编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "发货出货口信息登录  仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
			}
		
		} else {
			//更新数据
			if ($model->updateFhchhkxx() == false) {

				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "发货出货口信息修改  仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
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
	 * 判断退货区出货口编号是否重复
	 */
	public function checkAction() {
		
		$model = new cc_models_fhchhkxx();
		//$ckbh = $this->_getParam ( "ckbh" );
		//$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam ( "chhkbh" );
		if ($model->getChhkbh($chhkbh) == FALSE) {
			echo "0"; //不存在
		

		} else {
			echo "1"; //存在
		}
	}
	
    /*
     * 显示发货区
     */
    public function fhqlistAction()
	 {
		$ckbh = $this->_getParam('ckbh');
		$model = new cc_models_fhchhkxx();
		echo json_encode($model->getFhqList($ckbh));
	 }
	
	/*
	 * 取得发货出货口信息  上一条,下一条
	 */
	public function getfhchhkxxAction() {
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['fhchhkxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new cc_models_fhchhkxx();
        $rec = $model->getFhchhkxx ($ckbh,$fhqbh,$chhkbh, $filter, $flg);
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo $this->_view->fetchPage ( "fhchhk_03.php" );
		}
	}
	/*
	 * 更改发货出货口使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_fhchhkxx();
		 //如果启用出货口为正常状态  预先判断仓库是否为正常状态
		 //如果启用出货口为正常状态  预先判断发货区是否为正常状态
	    if ($_POST ['fhchhkzht'] == '1') { 
			if($model->getCkzht ( $_POST ['ckbh'] ) !='1'){
				echo '0'; //仓库禁用
			  }
		    else if($model->getFhqzht($_POST['fhqbh']) !='1'){
				echo '1';  //发货区已被禁用
			  }
		
			else {
			
				$model->updateStatus ( $_POST ['ckbh'], $_POST ['fhqbh'],$_POST['chhkbh'], $_POST ['fhchhkzht'] );
				//写入日志
				Common_Logger::logToDb (($_POST ['fhchhkzht'] == 'X' ? "出货口禁用" : "出货口启用") . " 仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
			}
		} else {
			$model->updateStatus ( $_POST ['ckbh'], $_POST ['fhqbh'],$_POST['chhkbh'], $_POST ['fhchhkzht'] );
			//写入日志
			Common_Logger::logToDb (($_POST ['fhchhkzht'] == 'X' ? "出货口禁用" : "出货口启用") .  " 仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
		}
	}
	

	

	
	
}
?>
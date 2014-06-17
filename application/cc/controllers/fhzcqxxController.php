<?php 
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       发货暂存区信息(fhzcqxx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/05/24
 ***** 更新履历：

 ******************************************************************/

class cc_fhzcqxxController extends cc_controllers_baseController {
     /*
      *   发货暂存区列表画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','仓储管理-发货暂存区信息维护');
		$this->_view->display('fhzcq_01.php');
	}
	
	
    /*
     *   发货暂存区登录画面显示
     */
	public function newAction(){
		//$model = new cc_models_fhzcqxx();
		$this->_view->assign('action','new');
		$this->_view->assign('title','仓储管理-发货暂存区信息登录');
		$this->_view->assign ( "fhzcqlb_ops", array ("" =>'--选择类别--','1' => '近距离', '2' => '中距离', '3' => '远距离' ) );
		$this->_view->display ( 'fhzcq_02.php' );
	}
		/*
     * 员工修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new cc_models_fhzcqxx();
		
		//画面项目赋值
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$fhzcqbh = $this->_getParam("fhzcqbh");
		$this->_view->assign ( "fhzcqlb_ops", array ('1' => '近距离', '2' => '中距离', '3' => '远距离' ) );
		$rec = $model->getFhzcqxx( $ckbh, $fhqbh,$chhkbh,$fhzcqbh);			
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '仓储管理—发货暂存区信息修改' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'fhzcq_02.php' );
	}
	
	/*
     *   发货暂存区详情画面
     */
	public function detailAction() {
		$model = new cc_models_fhzcqxx();
		
		//画面项目赋值
		$this->_view->assign ( 'title', '仓储管理-发货暂存区信息详情' );
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$fhzcqbh = $this->_getParam("fhzcqbh");
		$rec = $model->getFhzcqxx( $ckbh, $fhqbh,$chhkbh,$fhzcqbh );
		$this->_view->assign ( "fhzcqlb_ops", array ("" =>'--选择类别--','1' => '近距离', '2' => '中距离', '3' => '远距离' ) );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		$this->_view->display ('fhzcq_03.php' );
	}
	
	/*
	 * 得到发货暂存区信息
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
				unset($_SESSION['fhzcqxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fhzcqxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['fhzcqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cc_models_fhzcqxx();
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
		$result ['chhk'] = $_POST ['CHHK'];//出货口编号
		$result ['fhzcqbh'] = $_POST ['FHZCQBH'];//发货暂存区
	    try{
			$model = new cc_models_fhzcqxx();
			$model->beginTransaction();	
		//库存存储
		if ($_POST ['action'] == 'new') {
			//$model->beginTransaction();//开启事物
			//插入新数据
			if ($model->insertFhzcqxx () == false) {
				$result ['status'] = 2; //发货暂存区编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "发货暂存区信息登录  仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQ'] . "出货口编号: " .$_POST['CHHK'] . "发货暂存区编号: " . $_POST['FHZCQBH'] );
			}
		
		} else {
			//更新数据
			if ($model->updateFhzcqxx() == false) {

				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "发货暂存区信信息修改  仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHK'] . "发货暂存区编号: " . $_POST['FHZCQBH'] );
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
	 * 判断发货暂存区编号是否存在
	 */
	public function checkAction() {
		
		$model = new cc_models_fhzcqxx();
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam ( "chhkbh" );
		$fhzcqbh = $this ->_getParam("fhzcqbh");
		if ($model->getFhzcqxx($ckbh,$fhqbh,$chhkbh,$fhzcqbh) == FALSE) {
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
		$model = new cc_models_fhzcqxx();
		echo json_encode($model->getFhqList($ckbh));
	 }
	 
	 /*
	  * 显示出货口
	  */
	 public function chhklistAction(){
	 	$ckbh = $this->_getParam('ckbh');
	 	$fhq = $this->_getParam('fhq');
	 	$model = new cc_models_fhzcqxx();
	 	echo json_encode($model->getChhkList($ckbh,$fhq));
	 	
	 }
	
	/*
	 * 取得  发货暂存区信息  上一条,下一条
	 */
	public function getfhzcqxxAction() {
		$ckbh = $this->_getParam ( "ckbh" );
		$fhqbh = $this->_getParam ( "fhqbh" );
		$chhkbh = $this->_getParam("chhkbh");
		$fhzcqbh = $this->_getParam("fhzcqbh");
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['fhzcqxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new cc_models_fhzcqxx();
        $rec = $model->getFhzcqxx($ckbh,$fhqbh,$chhkbh,$fhzcqbh, $filter, $flg);
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "fhzcqlb_ops", array ("" =>'--选择类别--','1' => '近距离', '2' => '中距离', '3' => '远距离' ) );
			$this->_view->assign ( "rec", $rec );
			echo $this->_view->fetchPage ( "fhzcq_03.php" );
		}
	}
	/*
	 * 更改 发货暂存区信息使用状态
	 */
	public function changestatusAction() {
		
		$model = new cc_models_fhzcqxx();
		 //如果启用出货口为正常状态  预先判断仓库是否为正常状态
		 //如果启用出货口为正常状态  预先判断发货区是否为正常状态 
		 if ($_POST ['fhzcqzht'] == '1') { 
			if($model->getCkzht ( $_POST ['ckbh'] ) !='1'){
				echo '0'; //仓库禁用
			  }
		    if($model->getFhqzht($_POST ['ckbh'],$_POST['fhqbh']) !='1'){
				echo '1';  //发货区已被禁用
			  }
		    if ($model->getFhchhkzht($_POST ['ckbh'],$_POST['fhqbh'],$_POST['chhkbh']) !='1'){
				echo '2';//出货口已被禁用
			}
			//if ($model->getFhzcqzht($_POST['ckbh'],$_POST['chhkbh'],$_POST['fhzcqbh']) != '1'){
				 //  echo '3';//发货暂存区已被禁用
		   // }
			else {
			
				$model->updateStatus ( $_POST ['ckbh'],$_POST['chhkbh'],$_POST['fhzcqbh'], $_POST ['fhzcqzht'] );
				//写入日志
				Common_Logger::logToDb (($_POST ['fhzcqzht'] == 'X' ? "发货暂存区禁用" : "发货暂存区启用") . " 仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
			         }
	 } else {
			$model->updateStatus ( $_POST ['ckbh'], $_POST['chhkbh'],$_POST['fhzcqbh'], $_POST ['fhzcqzht']);
			//写入日志
			Common_Logger::logToDb (($_POST ['fhzcqzht'] == 'X' ? "发货暂存区禁用" : "发货暂存区启用") .  " 仓库编号：" . $_POST ['CKBH'] . " 发货区编号：" . $_POST ['FHQBH'] . "出货口编号: " .$_POST['CHHKBH'] );
		             }
	}
		
	
}
?>
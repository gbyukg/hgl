<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：    采购开票(CGKP)
 * 作成者：姚磊
 * 作成日：2010/12/13
 * 更新履历：
 *********************************/
class cg_cgkpController extends cg_controllers_baseController {
	private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_BZHSHL = 6; // 包装数量
	private $idx_LSSHL = 7; // 零散数量
	private $idx_SHULIANG = 8; // 数量
	private $idx_DANJIA = 9; // 单价
	private $idx_HSHJ = 10; // 含税售价
	private $idx_KOULV = 11; // 扣率
	private $idx_SHUILV = 12; // 税率
	private $idx_HSHJE = 13; // 含税金额
	private $idx_JINE = 14; //金额
	private $idx_SHUIE = 15; // 税额
	private $idx_LSHJ = 16; // 零售价
	private $idx_CHANDI = 17; // 产地
	private $idx_BEIZHU = 18; // 备注
	private $idx_TONGYONGMING = 19; // 通用名	
	private $idx_ZDSHULIANG = 20; // 最大入库数量
	private $idx_SHFSHKW = 21; // 是否散货区
	private $idx_BZHDWBH = 22; // 包装单位编号
	private $idx_XUHAO = 23; // 序号
	
	
	/*
	 * 采购开票初始页面
	 */
	public function indexAction() { 
		$cgkpModel = new cg_models_cgkp();	
		$this->_view->assign ( 'action', 'index' ); 
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "kpybh", $_SESSION ["auth"]->userName );  //开票员编号，待换成名称
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //部门编号
		$cgkpdbh = $this->_getParam( "flg" );   //获取生成采购订单编号
		$rec = $cgkpModel->fhdata( $cgkpdbh );
		$this->_view->assign ( "title", "采购管理-采购订单生成" ); //标题
		$this->_view->assign ( "rec", $rec ); 
		$this->_view->display ( "cgkp_01.php" );
	}
	/*
	 * 采购开票入账页面
	 */
	public function getzhangdanAction(){
		$this->_view->assign ( "title", "采购管理-采购订单入账" ); //标题
		$this->_view->display ( "cgkp_02.php" );
	}
	
	/*
	 * 采购开票-订单导入
	 */
	public  function getdingdanAction(){

		$this->_view->assign ( "title", "采购管理-采购计划导入" ); //标题
		$this->_view->display ( "cgkp_03.php" );
	
	}
	
	/*
	 * 采购开票-历史最高价格查询
	 */
public  function getlishiAction(){

		$this->_view->assign ( "title", "采购管理-采购订单历史价格查询" ); //标题
		$this->_view->display ( "cgkp_04.php" );
	
	}
	/*
	 * 挂账保存
	 */
	public function savezhangdanAction(){
		$_POST ["#grid_mingxi"];
			$result['status'] = '0'; 
		try {
			$cgkpModel = new cg_models_cgkp();
			
			//必须输入项验证
		
				//开始一个事务
			    $cgkpModel->beginTransaction ();
			    //采购挂单单据编号取得
			    $kphdbh = Common_Tool::getDanhao('CGZ',$_POST['KPRQ']);
			    //采购挂单保存
			    $cgkpModel->saveGuadanMain($kphdbh);
			    //采购挂单明细保存
			    $cgkpModel->saveGuadanMingxi($kphdbh);
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $kphdbh;
				    $cgkpModel->commit ();
			    }else{
				    $cgkpModel->rollBack ();//有错误发生
			    }
			
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
	}			
	/*
	 * 采购开票保存
	 */

	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$cgkpModel = new cg_models_cgkp();
			
			//必须输入项验证
			if(!$cgkpModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$cgkpModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $cgkpModel->beginTransaction ();
			    //采购开票单编号取得
			    $cgkpbh = Common_Tool::getDanhao('CGD',$_POST['KPRQ']); //采购单据号
			    
			    $dwbh = $_POST['DWBH'];
			    $syturnValue = $cgkpModel->shenpiCheck($dwbh);  //首营审批check
//			    //定义一个序号，自增，用来当做数组下标
			    $index = 1;
			    if($syturnValue['status']!='0' && $syturnValue ['status']!=""){
			       $result['num'][$index] = "单位编号：【".$dwbh."】首营审批没有通过 \n";
			       $result['fenlei'][$index] = $syturnValue['status'];
			       $index ++;
			    }else {
			      $qxCheck = $cgkpModel->qxCheck($dwbh);	//Check首营期限
				if($syturnValue['status']==02){
			       $result['num'][$index] = "单位编号：【".$dwbh."】首营期限已过期 \n";
			       $result['fenlei'][$index] = $qxCheck['status'];
			       $index ++;
				}	
			    }
			    
			  
				foreach ( $_POST ["#grid_mingxi"] as $row ) {
					if ($row [$this->idx_SHPBH] == '')
						continue;
					$spValue = $cgkpModel->spCheck($row [$this->idx_SHPBH]); 		//商品首营期审批没有通过check
					if( $spValue ['status']!='0' && $spValue ['status']!=""){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品首营审批没有通过 \n";
				       $result['fenlei'][$index] = $spValue ['status'];
				       $index ++;
					}
					 $spqxCheck = $cgkpModel->spqxCheck($row [$this->idx_SHPBH]);	//商品首营期限已过期check	
					if( $spValue ['status']==04){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】商品首营期限已过期 \n";
				       $result['fenlei'][$index] = $spqxCheck['status'];
				       $index ++;
					}	
				    $returnValue =  $cgkpModel->shifMax($row [$this->idx_SHPBH],$row [$this->idx_SHULIANG]);	//最大采购数量check
					if( $returnValue ['status']!='0' && $returnValue ['status']!=""){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】超过最大采购数量 \n";
				       $result['fenlei'][$index] = $returnValue['status'];
				       $index ++;
					}
				    $zdjgValue = $cgkpModel->jgMax($row [$this->idx_SHPBH]);		//最大采购价格check
					if($row [$this->idx_SHPBH]){
				       $result['num'][$index] = "商品编号：【".$row [$this->idx_SHPBH]."】超过最大采购价格 \n";
				       $result['fenlei'][$index] = $zdjgValue['status'];
				        $index ++;
					}
				    $spyxValue = $cgkpModel->spyxCheck($row [$this->idx_SHPBH]);	//商品优先供应指定供应商check
				    if($spyxValue ['status']=='30'){
				    	$shpValue = $cgkpModel->gysCheck($row [$this->idx_SHPBH]);
					if( $shpValue ['status']=='08'){
				       $result['num'][$index] = "单位编号：【".$dwbh."】不是商品编号：【".$row [$this->idx_SHPBH]."】商品的优先指定供应商\n";
				       $result['fenlei'][$index] = $shpValue['status'];
				        $index ++;
					}else if($shpValue ['status']=='40'){
						$spyxValue = $cgkpModel->danwCheck($row [$this->idx_SHPBH]);
						if( $spyxValue ['status']=='07'){
					   $result['num'][$index] = "单位编号：【".$dwbh."】不是商品编号：【".$row [$this->idx_SHPBH]."】商品的最优指定供应商\n";
				       $result['fenlei'][$index] = $spyxValue['status'];
				       $index ++;
					}}
				}
				}
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $cgkpbh;
			    }else{
				    $cgkpModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
	
	}
	
	
	/*
	 * 保存开票采购数据
	 */
	function savecgAction(){
		$result['status'] = '0'; 
		try{
			$cgkpModel = new cg_models_cgkp();
		    	$cgkpModel->beginTransaction ();
			    //采购开票单编号取得
			    $cgkpbh = Common_Tool::getDanhao('CGD',$_POST['KPRQ']); //采购单据号
			    //采购开票订单保存
			    $cgkpModel->saveCgkpMain($cgkpbh);
			    //采购开票订单明细保存
			    $cgkpModel->saveCgkpMingxi($cgkpbh);
			 $filter ['errormeg'] = $_POST['ERRORMEG']; 
			 $filter ['error'] = $_POST['ERROR']; 
			 if( $filter ['errormeg']!=null){
			   $cgkpModel->errorSave($cgkpbh,$filter);
			 }
			    //审批警告信息保存
			    $cgkpModel->commit ();
				Common_Logger::logToDb ("采购订单做成  单据号：".$cgkpbh);
				$result['data'] = $cgkpbh;
				echo json_encode($result);
		}catch( Exception $e){
		//回滚
			$cgkpModel->rollBack ();
     		throw $e;
		}
		}

		
		
		
	
    /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$cgkpModel = new cg_models_cgkp();
		
	    echo json_encode($cgkpModel->getShangpinInfo($filter));
	}
	
	/**
	 * 获取入库限定数量
	 */
	public function getrkxzhshlAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$rkxzhshl = new cg_models_cgkp();
		echo Common_Tool::json_encode($rkxzhshl->getRkxzhshlInfo($filter));
	}
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
        $dwfilter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
    	$dwfilter ['flg'] = "1";//采购
 		$dwModel = new gt_models_danwei ( );
 		$result['dwinfo'] =  $dwModel->getDanweiInfo($dwfilter); //单位相关信息
 		$ywyModel = new gt_models_ywy();
 		$ywyfilter ['dwbh'] = $this->_getParam('dwbh');
 		$ywyfilter ['flg'] = '0';//采购 
 		$result['ywyinfo'] =  $ywyModel->getData($ywyfilter);
	    echo Common_Tool::json_encode($result);
	}

	/*
	 * 获取订单导入列表
	 */
 	public function getlistdddataAction(){
 		//取得列表参数	
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkpdd_searchParams'] = $_POST;
				unset($_SESSION['cgkpdd_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkpdd_filterParams'] = $_POST;
				unset($_SESSION['cgkpdd_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['cgkpdd_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgkpdd_searchParams'];  //固定查询条件	
		$model = new cg_models_cgkp();
	
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getDingdanGridData ( $filter );
 	}
 	
 	/*
 	 *  获取订单导入明细列表
 	 */
 	public function getdingdanlistdataAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$cggzhdbh = $this->_getParam ( "flg" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cg_models_cgkp();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getDaoruGridData ($cggzhdbh,$filter );
 	}
 	
 	/*
 	 * 开票采购历史价格查询
 	 */
 	public function getlistlishidataAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量					
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkpls_searchParams'] = $_POST;
				unset($_SESSION['cgkpls_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkpls_filterParams'] = $_POST;
				unset($_SESSION['cgkpls_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['cgkpls_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgkpls_searchParams'];  //固定查询条件
		$model = new cg_models_cgkp();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getLishiGridData ($filter );
 	}
 	
 	
 	
 	
 	/*
 	 *  获取采购入账单据信息列表
 	 */
 	public function getlistdataAction(){
 		//取得列表参数				
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkplz_searchParams'] = $_POST;
				unset($_SESSION['cgkplz_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgkplz_filterParams'] = $_POST;
				unset($_SESSION['cgkplz_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['cgkplz_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgkplz_searchParams'];  //固定查询条件	
		$model = new cg_models_cgkp();
		
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );

 	}
 	
 	/*
 	 *  获取采购入账明细信息列表
 	 */
 	
 	public function getmingxilistdataAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$cggzhdbh = $this->_getParam ( "flg" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cg_models_cgkp();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getMingxiGridData ($cggzhdbh,$filter );
 	}
 	
 	/*
 	 *  采购单数据入账信息页面赋值
 	 */
 	public function  getdanjuhaoinfoAction(){
 		
 		$flg = $this->_getParam("flg");
 		$model = new cg_models_cgkp();
 		echo Common_Tool::json_encode($model->getcgGridData($flg));		
 				
 	}
 	
 	/*
 	 * 删除采购入账单据信息
 	 */
 	public function deleteruzhangAction(){
 		$model = new cg_models_cgkp();
 		$flg = $this->_getParam("flg");		
 		$model->deletecgData($flg);
 			
 	}
 	
 	/*
 	 * 设置订单导入返回表单数据
 	 */
 	public function fhdataAction(){
 		$cggzhdbh = $this->_getParam ( "flg" );
 		$model = new cg_models_cgkp();
 //		$result = $model->fhdata($cggzhdbh);
// 		echo Common_Tool::json_encod($result);		
 		echo Common_Tool::json_encode($model->fhdata($cggzhdbh));
 		  
 	}
 	
 	
 	
}
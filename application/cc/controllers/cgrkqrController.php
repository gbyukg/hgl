<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库确认(CGRKQR)
 * 作成者：苏迅
 * 作成日：2010/12/28
 * 更新履历：
 * 2011/08/15--入库明细与采购订单可以不完全相符，以前必须完全一致，预付款的情况也必须要修改应付应收信息--修改position(搜索2011/08/15)
 *********************************/
class cc_cgrkqrController extends cc_controllers_baseController {
	
	/*
	 * 采购入库页面
	 */
	public function indexAction() {
		
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  //部门编号
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "yrkdbh", $this->_getParam ("yrkdbh","请双击选择")); 
		$this->_view->assign ( "title", "仓储管理-采购入库确认" ); 	//标题
		$this->_view->display ( "cgrkqr_01.php" );
	}
	
	/*
	 * 预入库单选择页面(状态='0')
	 */
	public function yrkdlistAction() {
		$this->_view->assign ( "title", "仓储管理-预入库单选择" ); 				//标题
		$this->_view->assign ( "KSRQKEY", date ( "Y-m-d",time() - (14 * 24 * 60 * 60) ) );	//开始日期
		$this->_view->assign ( "ZZRQKEY", date ( "Y-m-d" ) );	//终止日期
		$this->_view->display ( "cgrkqr_02.php" );
	}
	
	/*
	 * 预入库单明细选择页面
	 */
	public function yrkmxlistAction() {
		$this->_view->assign ( "title", "仓储管理-预入库明细列表" ); 			//标题
		$this->_view->display ( "cgrkqr_03.php" );
	}
	
	/*
	 * 商品大包装信息修改页面
	 */
	public function shpdbzxxAction() {
		$this->_view->assign ( "title", "仓储管理-商品大包装信息" ); 			//标题
		$this->_view->display ( "cgrkqr_04.php" );
	}
	
	
	/*
	 * 预入库单列表xml数据取得(预入库单选择页面)
	 */
	public function getyrkdlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列--退货单号
		$filter ['direction'] = $this->_getParam ( "direction", 'DESC' ); //排序方式--退货日期降序
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_cgrkqr_searchParams'] = $_POST;
				unset($_SESSION['cgrkqr_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgrkqr_filterParams'] = $_POST;
				unset($_SESSION['cc_cgrkqr_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['cgrkqr_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_cgrkqr_searchParams'];  //固定查询条件
		
		$model = new cc_models_cgrkqr ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData ( $filter );
	}
	
	/*
	 * 预入库单明细列表xml数据取得(预入库单选择页面)
	 */
	public function getyrkdmxlistdataAction() {
		
    	$yrkdbh = $this->_getParam('yrkdbh');
    	$model = new cc_models_cgrkqr();
    	header ( "Content-type:text/xml" ); //返回数据格式xml
    	echo $model->getmxdata($yrkdbh);
	}
	
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cc_models_cgrkqr ( );		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	/**
     * 取得预入库单信息(采购入库页面)
     *
     */
	public function getyrkdinfoAction() {
		$model = new cc_models_cgrkqr ();
		echo Common_Tool::json_encode($model->getSingleYrkdInfo($this->_getParam ( "yrkdbh", '' )));
		
	}	
	
	/*
	 * 采购预入库明细取得(采购入库页面)
	 */	
	public function getyrkmingxiAction() {
		$model = new cc_models_cgrkqr ();
		echo Common_Tool::json_encode($model->getyrkmingxi($this->_getParam ( "yrkdbh", '' )));
		
	}
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_cgrkqr ();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//库区编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkuweizht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);		
	}
	
	/*
	 * 判断选择库位选定商品是否存在其他批号
	 */
	public function checkphAction()
	{
		$filter["shpbh"] = $this->_getParam("shpbh");
		$filter["pihao"] = $this->_getParam("pihao");
		$filter["ckbh"] = $this->_getParam("ckbh");
		$filter["kqbh"] = $this->_getParam("kqbh");
		$filter["kwbh"] = $this->_getParam("kwbh");
		$model = new cc_models_cgrkqr();
		echo Common_Tool::json_encode($model->pdPhHw($filter));
	}	


	
	/*
	 * 采购入库信息保存
	 */
	public function saveAction() {
    	//返回值状态('0'：正常)	
		$result['status'] = '0';
		try {
			$model = new cc_models_cgrkqr ();
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{		
				//商品包装信息check
				$result = $this->commcheck('getshpchkg');
				//所在库位有其他批号该商品check
				if($result['status'] == '0'){					
					$result = $this->commcheck('checkpihao');
				}				
				if($result['status'] == '0'){
					//开始一个事务
				    $model->beginTransaction ();
				    //入库单编号取得
				    $rkdbh = Common_Tool::getDanhao('RKD',$_POST['KPRQ']);
				    //入库单信息保存
				    $model->saveRukudan($rkdbh);
				    //循环读取明细信息,采购入库更新操作
					$model->executeMingxi($rkdbh);				
					//保存采购结算信息（应付应收）-- 预付款('4')的情况不处理
					//修改--预付款要更新(由于入库与采购订单可以不相符)--2011/08/15
	          	   /*if($_POST['FKFSH'] != '4'){
						$model->saveCgjs($rkdbh);
					}*/
					$model->saveCgjs($rkdbh);
					//更新预入库单状态
					$model->uptYrkdZht();
					//更新采购订单状态--2011/08/12修改--更新采购订单明细状态,所有明细均已入库才能更新采购订单状态
					$model->uptCgddZht();
					//不合格品处理
					$model->executeBhgp();
				    //保存成功
				    if($result['status'] == '0'){
				    	$result['data'] = $rkdbh;//新生成的采购入库单编号
					    $model->commit();
					    Common_Logger::logToDb("新采购入库单号：".$rkdbh);
				    }else{
					    $model->rollBack ();//有错误发生
				    }
				}

			}
				
			echo json_encode($result);


		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
	
	}
	
	/*
	 * 商品包装信息保存
	 */
	public function savebzhxxAction(){
		$result = array (); //定义返回值
		$result ['SHPBH'] = $_POST ['SHPBH'];
		$result['status'] = '0';
		$model = new cc_models_cgrkqr ();
		try {
			$model->updateShpdbzhxx();
		}catch(Exception $e){
			//回滚
			$model->rollBack ();
     		throw $e;
		}
		//返回处理结果
		echo json_encode ( $result );
	}
	
	public function commcheck($func){
		$model = new cc_models_cgrkqr ();
		$result = $model->$func();//取得大包装信息
		
		return $result;
	}
	
	
}
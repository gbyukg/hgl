<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购退货出库(CGTHCK)
 * 作成者：刘枞
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class cc_cgthckController extends cc_controllers_baseController {
	/*
	 * 采购退货出库初始页面
	 */
	public function indexAction() {
    	$Model = new cc_models_cgthck ( );
		$this->_view->assign ( "fahuoqu", $Model->getFHQInfo() ); //取得发货区数据，并传到画面
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "title", "仓储管理-采购退货出库" ); //标题
		$this->_view->assign ( "bmbh", $_SESSION ["auth"]->bmbh ); //开票员
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->display ( "cgthck_01.php" );
	}
	
	
	/*
	 * 退货单选择页面
	 */
	public function thdlistAction() {
		$this->_view->assign ( "title", "采购退货单选择" ); 				//标题
		$this->_view->display ( "cgthck_02.php" );
	}
	
	
	/*
	 * 退货单明细选择页面
	 */
	public function thmxlistAction() {
		$this->_view->assign("bh",$this->_getParam("bh"));	    //退货单编号
		$this->_view->assign ( "title", "采购退货单明细" ); 		//标题
		$this->_view->display ( "cgthck_04.php" );
	}
	
	
	/*
	 * check出库单状态
	 */
	public function checkzhtAction() {
		$model = new cc_models_cgthck();
		$result = $model->getzht($this->_getParam("bh"));
		echo Common_Tool::json_encode($result);		
	}
	
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_cgthck();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//库区编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkuweizht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);		
	}
	
	
	/*
	 * 退货单列表xml数据取得(退货单选择页面)
	 */
	public function getthdlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	//开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	//终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	//单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' ); //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgthck_thdlist_searchParams'] = $_POST;
				unset($_SESSION['cgthck_thdlist_filterParams']);       //清空精确查询条件
			}

		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgthck_thdlist_filterParams'] = $_POST;
				unset($_SESSION['cgthck_thdlist_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cgthck_thdlist_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgthck_thdlist_searchParams'];                 //固定查询条件
		
		$model = new cc_models_cgthck();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 退货单明细列表xml数据取得(退货单选择页面,商品明细选择页面)
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['thdbh'] = $this->_getParam ( "thdbh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgthck_thdmxlist_searchParams'] = $_POST;
				unset($_SESSION['cgthck_thdmxlist_filterParams']);       //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cgthck_thdmxlist_filterParams'] = $_POST;
				unset($_SESSION['cgthck_thdmxlist_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cgthck_thdmxlist_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cgthck_thdmxlist_searchParams'];                 //固定查询条件
		
		$model = new cc_models_cgthck();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cc_models_cgthck();		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	
	/*
	 * 采购退货出库保存操作
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$Model = new cc_models_cgthck();

			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';             //必须输入项验证错误
			}elseif(!$Model->logicCheck()){
				$result['status'] = '2';             //项目合法性验证错误
			}else{
			    $Model->beginTransaction ();			   //开始一个事务
			    $chkdbh = Common_Tool::getDanhao('CKD',$_POST['KPRQ']);	   //出库单编号取得
			    
			    //库存相关数据更新（库存数量验证，库存数量更新，商品移动履历）
			    $returnValue = $Model->updateKucun($chkdbh);
			    if($returnValue['status']!='0'){
			       $result['status'] = '3';                //库存不足	
			       $result['data'] = $returnValue['data']; //库存数据
			    }else{
			    	
			    	$Model->saveChkdMain($chkdbh);		   //出库单信息保存
			    	$Model->saveChkdMingxi($chkdbh);	   //出库单明细保存
			    	$Model->updateCgthzht();   //更新DB:采购退货单信息（H01DB012308）的退货单状态为已出库
			    	$Model->updateCGJS();      //计算应付应收，更新采购结算信息
			    	$Model->updateJSCB();      //计算成本
			    	$Model->updateFLXY();      //更新返利协议的累计数量或累计金额(如果有协议的情况)
			    }

			    if($result['status'] == '0'){	     //保存成功
			    	$result['chkdbh'] = $chkdbh;
				    Common_Logger::logToDb("采购退货出库，采购退货单编号：".$result['chkdbh']);
				    $Model->commit();
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack();		       //回滚
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
 		$Model = new cc_models_cgthck();
	    echo json_encode($Model->getShangpinInfo($filter));
	}
 	
 	
	/*
	 * 采购退货单自动完成
	 */
	public function autocompleteAction(){
    	$filter ['searchkey'] = $this->_getParam('q');
    	$filter ['flg'] = $this->_getParam("flg",'0');
        $model = new cc_models_cgthck();
	    $result = $model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	
	/*
	 * 明细信息商品编号自动填充
	 * cgthdh:采购退货单号
	 */
	public function shangpinautocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值
    	$filter ['cgthdh'] = $this->_getParam('cgthdh');   //采购退货单号
        $shangpin_model = new cc_models_cgthck();
	    $result = $shangpin_model->getshangpinAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	
	/**
     * 取得采购退货单信息
     *
     */
	public function getcgthdinfoAction(){
    	$filter ['bh'] = $this->_getParam('bh');   //单位编号
 		$Model = new cc_models_cgthck();
	    echo Common_Tool::json_encode($Model->getcgthdInfo($filter));
	}
	
	
	/**
     * 取得采购退货单明细信息
     *
     */
	public function getcgthdmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');   //单位编号
 		$Model = new cc_models_cgthck();
	    echo Common_Tool::json_encode($Model->getcgthdmingxi($filter));
	}
	
	
	/*
	 * 商品库位/批号选择页面显示
	 */
	public function kuweiAction(){
		//取得参数，并传送到新页面
		$this->_view->assign ( "shpbh", $this->_getParam ( "shpbh" ));  //商品编号
		$this->_view->assign ( "pihao", $this->_getParam ( "pihao" ));  //商品批号
		$this->_view->assign ( "rkdbh", $this->_getParam ( "rkdbh" ));  //入库单编号
		$this->_view->assign ( "bzhdwbh", $this->_getParam ( "bzhdwbh" ));  //包装单位编号
		$this->_view->assign ( "title", "商品库位/批号选择" ); 			//标题
		$this->_view->display ( "cgthck_03.php" );
	}
	
	
	/*
	 * 商品库位/批号选择页面数据取得
	 */
	public function getkuweidataAction(){
		//业务相关参数
		$filter ['shpbh'] = $this->_getParam("shpbh"); //商品编号
		$filter ['pihao'] = $this->_getParam("pihao"); //商品批号
		$filter ['rkdbh'] = $this->_getParam("rkdbh"); //入库单编号
		$filter ['bzhdwbh'] = $this->_getParam("bzhdwbh"); //入库单编号
		$model = new cc_models_cgthck();
		header("Content-type:text/xml");
		echo $model->getkuweiData($filter);	
	}
	
	
	/*
	 * 得到最新库存数据
	 */
	public function getkucundataAction(){
   		$filter['shpbh'] = $this->_getParam("shpbh","");    //商品编号
		$filter['shfshkw'] = $this->_getParam("shfshkw","");//是否散货库位
		$filter['ckbh'] = $this->_getParam("ckbh","");      //是否散货库位
		$kucun_model = new cc_models_cgthck();
		echo json_encode($kucun_model->getKucunData($filter));
	}
}
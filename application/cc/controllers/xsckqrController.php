<?php
/**********************************************************
 * 模         块：    仓储模块(CC)
 * 机         能：    销售出库确认(XSCKQR)
 * 作  成  者：    刘枞
 * 作  成  日：    2010/12/28
 * 更新履历：
 * 
 **********************************************************/

class cc_xsckqrController extends cc_controllers_baseController {
	/*
	 * 销售出库确认初始页面
	 */
	public function indexAction(){
    	$Model = new cc_models_xsckqr();
		$this->_view->assign ( "fahuoqu", $Model->getFHQInfo() );  //取得发货区数据，并传到画面
		$this->_view->assign ( "kprq", date("Y-m-d"));             //开票日期
		$this->_view->assign ( "title", "仓储管理-销售出库确认" ); //标题
		$this->_view->display ( "xsckqr_01.php" );
	}
	

	/*
	 * 调用销售单选择页面
	 */
	public function thdlistAction(){
		$this->_view->display ( "xsckqr_02.php" );
	}
	
	
	/*
	 * check销售单状态
	 */
	public function checkzhtAction() {
		$model = new cc_models_xsckqr();
		$result = $model->getzht($this->_getParam("bh"));
		echo Common_Tool::json_encode($result);		
	}

	
	/*
	 * 销售单列表xml数据取得(销售单选择页面)
	 */
	public function getxsdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	//开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	//终止日期
		$filter ['xsdkey'] = $this->_getParam ( "xsdkey", '' ); 	//终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	//单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwkey", '' ); //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_xsckqr();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 销售单明细列表xml数据取得(销售单选择页面)
	 */
	public function getxsdmxlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	        //编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_xsckqr();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/*
	 * 销售订单确认操作
	 */
	public function saveAction() {
		$result['status'] = '0';
		try {
			$Model = new cc_models_xsckqr();
			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';        //必须输入项验证错误
			}else{
			    $Model->beginTransaction();	    //开始一个事务
			    //出库单编号取得
			    $chkdbh = Common_Tool::getDanhao('CKD',$_POST['KPRQ']);	 
			    //仓库，库区，库位状态验证
			    $returnValue = $Model->getKwzht();
			    if($returnValue['status']!='0'){
			       $result['status'] = '2';                //仓库，库区，库位状态冻结	
			       $result['data'] = $returnValue['data']; //第几行数据
			    }else{
			    	$Model->saveChkdMain($chkdbh);		 //出库单信息保存
			    	$Model->saveChkdMingxi($chkdbh);	 //出库单明细保存
			    	$Model->updatexsdzht();        //更新DB:销售订单信息（H01DB012201）的出库状态为已出库
			    }
			    if($result['status'] == '0'){	         //保存成功
			    	$result['chkdbh'] = $chkdbh;
				    Common_Logger::logToDb("销售出库确认，出库单编号：".$result['chkdbh']);
				    $Model->commit();
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack ();		 //事务回滚
     		throw $e;
		}
	}
 	
 	
	/*
	 * 销售订单自动完成
	 */
	public function autocompleteAction(){
    	$filter ['searchkey'] = $this->_getParam('q');
    	$filter ['flg'] = $this->_getParam("flg",'0');
        $model = new cc_models_xsckqr();
	    $result = $model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	
	/**
     * 取得销售订单信息
     */
	public function getxsdinfoAction(){
    	$filter ['bh'] = $this->_getParam('bh');
 		$Model = new cc_models_xsckqr();
	    echo Common_Tool::json_encode($Model->getxsdInfo($filter));
	}
	
	
	/**
     * 取得采购退货单明细信息
     */
	public function getxsdmingxiAction(){
    	$filter ['bh'] = $this->_getParam('bh');
 		$Model = new cc_models_xsckqr();
	    echo Common_Tool::json_encode($Model->getxsdmingxi($filter));
	}
	
	
	/**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cc_models_xsckqr();
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}

}

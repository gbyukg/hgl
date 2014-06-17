<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    销售退货入库(XSTHRK)
 * 作成者：苏迅
 * 作成日：2010/12/13
 * 更新履历：
 *********************************/
class cc_xsthrkController extends cc_controllers_baseController {
	
	/*
	 * 销售退货入库页面
	 */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  //部门编号
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "title", "仓储管理-销售退货入库" ); 	//标题
		$this->_view->display ( "xsthrk_01.php" );
	}
	
	/*
	 * 退货单选择页面
	 */
	public function thdlistAction() {
		$this->_view->assign ( "title", "仓储管理-退货单选择" ); 				//标题
		$this->_view->display ( "xsthrk_02.php" );
	}
	
	/*
	 * 退货单明细选择页面
	 */
	public function thmxlistAction() {
		//$this->_view->assign("thdbh",$this->_getParam("thdbh"));	//退货单编号
		$this->_view->assign ( "title", "仓储管理-退货明细列表" ); 			//标题
		$this->_view->display ( "xsthrk_03.php" );
	}
	
	/*
	 * 退货单列表xml数据取得(退货单选择页面)
	 */
	public function getthdlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
/*		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	//开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	//终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	//单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' ); //单位名称*/
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列--退货单号
		$filter ['direction'] = $this->_getParam ( "direction", 'DESC' ); //排序方式--退货日期降序
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_xsthrk_searchParams'] = $_POST;
				unset($_SESSION['xsthrk_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xsthrk_filterParams'] = $_POST;
				unset($_SESSION['cc_xsthrk_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['xsthrk_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_xsthrk_searchParams'];  //固定查询条件
		
		$model = new cc_models_xsthrk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData ( $filter );
	}
	
	/*
	 * 退货单明细列表xml数据取得(退货单选择页面)
	 */
	public function getthdmxlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['thdbh'] = $this->_getParam ( "thdbh", '' ); 	//开始日期
		//$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		//$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_xsthrk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData ( $filter );
	}
	
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cc_models_xsthrk ( );		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	/**
     * 取得退货单信息,退货单双击选择后lostfocus时触发
     *
     */
	public function getthdinfoAction() {
		$model = new cc_models_xsthrk ();
		echo Common_Tool::json_encode($model->getSingleThdInfo($this->_getParam ( "thdbh", '' )));
		
	}
	
	/*
	 * 退货单明细列表xml数据取得(销售退货页面)
	 */
/*	public function getthdmingxilistdataAction(){
		$model = new cc_models_xsthrk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $this->_getParam ( "thdbh", '' ) );
		
	}*/
		
	/*
	 * 退货单明细列表数据取得(销售退货页面)
	 */
	
	public function getxsthrkmingxiAction() {
		$model = new cc_models_xsthrk ();
		echo Common_Tool::json_encode($model->getxsthdmingxi($this->_getParam ( "thdbh", '' )));
		
	}
	
	/*
	 * 取得库位信息(check仓库/库区/库位状态是否为已删除或冻结)
	 */
	public function checkkuweiAction() {
		$model = new cc_models_xsthrk ();
		$ckbh = $this->_getParam ( "ckbh"); 	//仓库编号
		$kqbh = $this->_getParam ( "kqbh"); 	//库区编号
		$kwbh = $this->_getParam ( "kwbh"); 	//库位编号
		$result = $model->getkuweizht($ckbh,$kqbh,$kwbh);
		echo Common_Tool::json_encode($result);		
	}
	
	/*
	 * check退货单状态
	 */
	public function checkthzhtAction() {
		$model = new cc_models_xsthrk ();
		$result = $model->getthzht($this->_getParam ( "thdbh"));
		echo Common_Tool::json_encode($result);		
	}
			
	/*
	 * 退货入库信息保存
	 */
	public function saveAction() {
		//返回值状态('0'：正常)
		$result['status'] = '0'; 
		try {
			$model = new cc_models_xsthrk ();			
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
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
				    //更新结算信息--2011-7-13增加
				    $model->uptJsxx();
				    //循环读取明细信息,退货入库更新操作
					$returnValue = $model->executeMingxi($rkdbh);
					//返回值不为0,退货数量超过了客户采购数量
				    if($returnValue['status']!='0'){
				       $result['status'] = '5'; //退货数量超过了客户采购数量
				       $result['data'] = $returnValue['data'];
				    }
				    //保存成功
				    if($result['status'] == '0'){
				    	//销售退货单状态更新
				    	$model->updatezht();	//追加->销售退货单状态更新
				    	$result['data'] = $rkdbh;//新生成的退货入库单编号
					    $model->rollBack ();
					    Common_Logger::logToDb("新退货入库单号：".$rkdbh);
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
	
	public function commcheck($func){
		$model = new cc_models_xsthrk ();
		$result = $model->$func();
		
		return $result;
	}
}
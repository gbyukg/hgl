<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   不合格品处理(bhgpchl)
 * 作成者：姚磊
 * 作成日：2011/08/26
 * 更新履历：
 *********************************/
class cc_bhgpchlController extends cc_controllers_baseController {
	
	/*
     * 客户资料维护画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '仓储管理-不合格品处理' );
		$this->_view->display ( 'bhgpchl_01.php' );
	}
	
	/*
	 * 入库单选择页面
	 */
	public function thdlistAction(){
		$this->_view->display ( "bhgpchl_02.php" );
	}
	/*
	 * 退货单编号选择
	 */
	public function thdbhcxAction(){
		$this->_view->display ( "bhgpchl_04.php" );
	}
	/*
	 * 获取入库单商品信息
	 */
	public function getshpinfoAction(){
		$rkdbh = $this->_getParam ( "flg" );//获取入库单编号
		$this->_view->assign ( 'rkdbh', $rkdbh );
		$this->_view->display ( "bhgpchl_03.php" );
	}
	/*
	 * 取得不合格商品信息
	 */
	public function getshpAction(){
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$rkdbh = $this->_getParam ( "flg" );//获取入库单编号
		$model = new cc_models_bhgpchl();
		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_bhgpchl_searchParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_filterParams']);       //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_bhgpchl_filterParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_bhgpchl_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_bhgpchl_searchParams'];           //固定查询条件
		
		$model = new cc_models_bhgpchl();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getShangpinInfo ( $filter,$rkdbh );
	
	}

	
	/*
	 * 入库单列表xml数据取得(入库单选择页面)
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dwbhkey'] = $this->_getParam ( "dwbhkey", '' ); 	     //单位编号
		$filter ['dwmchkey'] = $this->_getParam ( "dwmchkey", '' );      //单位名称
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_bhgpchl_searchParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_filterParams']);       //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_bhgpchl_filterParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_bhgpchl_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_bhgpchl_searchParams'];           //固定查询条件
		
		$model = new cc_models_bhgpchl();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	/*
	 * 明细列表xml数据取得(入库单选择页面)
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_bhgpchl();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	/*
	 * 获取采购退货单编号
	 */
	public function getcgthAction(){
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
				$_SESSION['cc_bhgpchl_searchParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_filterParams']);       //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_bhgpchl_filterParams'] = $_POST;
				unset($_SESSION['cc_bhgpchl_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_bhgpchl_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_bhgpchl_searchParams'];           //固定查询条件
		
		$model = new cc_models_bhgpchl();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getcgthData( $filter );
	}
	
	/*
	 * 保存不合格品
	 */
	public function saveAction(){
		$result['status'] = '0'; 
		
			try{
			$Model = new cc_models_bhgpchl();
		    	$Model->beginTransaction ();
			    //合格品移入不合格品区单编号取得
			     $cldbh = Common_Tool::getDanhao('CZD',date("Y-m-d")); //处理单编号
			     $Model->saveMain($cldbh);
			     	$Model->commit ();
			     	Common_Logger::logToDb ("新增不合格品处理编号 ：".$cldbh);
			     	$result['data'] = $cldbh;
				 	echo json_encode($result);
				}catch( Exception $e){
			//回滚
			$Model->rollBack ();
     		throw $e;
		}
		
		
	}
}

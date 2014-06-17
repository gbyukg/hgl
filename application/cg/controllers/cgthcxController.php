<?php
/*********************************
 * 模块：    采购模块(CG)
 * 机能：    采购退货查询(CGTHCX)
 * 作成者：刘枞
 * 作成日：2011/01/12
 * 更新履历：
 *********************************/
class cg_cgthcxController extends cg_controllers_baseController {
	/*
	 * 采购退货查询初始页面
	 */
	public function indexAction(){
		$this->_view->display ( "cgthcx_01.php" );
	}
	
	
	/*
	 * 采购退货单列表xml数据取得
	 */
	public function getthdlistdataAction(){
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
				$_SESSION['cg_cgthcx_searchParams'] = $_POST;
				unset($_SESSION['cg_cgthcx_filterParams']);       //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cg_cgthcx_filterParams'] = $_POST;
				unset($_SESSION['cg_cgthcx_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cg_cgthcx_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cg_cgthcx_searchParams'];           //固定查询条件
		
		$model = new cg_models_cgthcx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 采购退货明细列表xml数据取得
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgthcx();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/**
     * 取得单位信息
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbhkey');   //单位编号
 		$model = new cg_models_cgthcx();		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
}
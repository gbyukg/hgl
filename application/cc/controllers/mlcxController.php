<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    毛利查询(MLCX)
 * 作成者：刘枞
 * 作成日：2011/07/15
 * 更新履历：
 *********************************/
class cc_mlcxController extends cc_controllers_baseController {
	
	/*
	 * 毛利查询初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( "title", "仓储管理-毛利查询" ); //标题
		$this->_view->display ( "mlcx_01.php" );
	}
	
	/*
	 * 获取成本单价
	 */
	public function getchbjeAction() {
		$filter ['shpbh'] = $this->_getParam ( "bh", '' ); 	    //商品编号
		$filter ['pihao'] = $this->_getParam ( "ph", '' ); 	    //商品批号
		$filter ['chbjs'] = $this->_getParam ( "chb", '' ); 	//成本计算方式
		
		$model = new cc_models_mlcx();
		$result = $model->getchbdj($filter);
		
		echo Common_Tool::json_encode($result);	
	}
	
	
	
	/*
	 * 毛利查询GRID列表xml数据取得
	 */
	public function getdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['ksrq'] = $this->_getParam ( "ksrqkey", '' ); 	         //开始日期
		$filter ['zzrq'] = $this->_getParam ( "zzrqkey", '' ); 	         //终止日期
		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); 	         //商品编号
		$filter ['pihao'] = $this->_getParam ( "pihao", '' );            //批号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_mlcx_searchParams'] = $_POST;
				unset($_SESSION['cc_mlcx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_mlcx_filterParams'] = $_POST;
				unset($_SESSION['cc_mlcx_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_mlcx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_mlcx_searchParams'];  //固定查询条件
		
		$model = new cc_models_mlcx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
}
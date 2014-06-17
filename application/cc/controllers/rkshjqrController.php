<?php
/**********************************************************
 * 模     块：  仓储模块(CC)
 * 机     能：  入库上架确认(rkshjqr)
 * 作成者：    姚磊
 * 作成日：    2011/07/13
 * 更新履历：
 **********************************************************/	
class cc_rkshjqrController extends cc_controllers_baseController {

	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-入库上架确认" ); //标题
		$this->_view->display ( "rkshjqr_01.php" );
	}

	/*
	 * 查询赠品信息 返回xml格式
	 */
	public function getdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['rkshjqr_searchParams'] = $_POST;
				unset($_SESSION['rkshjqr_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['rkshjqr_filterParams'] = $_POST;
				unset($_SESSION['rkshjqr_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['rkshjqr_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['rkshjqr_searchParams'];  //固定查询条件
		$model = new cc_models_rkshjqr();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	


	/*
	 * grid保存单条信息
	 */
	function alarmsetAction(){
		$model = new cc_models_rkshjqr ( );
		$rkdbh = $this->_getParam('rkdbh'); //入库单编号
		$xuhao = $this->_getParam('xuhao');//序号
		$bcsjsl = $this->_getParam('bcsjsl');//本次上架数量
		
		$model->saveOne ( $rkdbh, $xuhao,$bcsjsl); //保存单条记录
		$model->uprkdzt($rkdbh, $xuhao);
		
	}
	
	/*
	 * 保存所有数据
	 */
	function saveallAction(){
		$model = new cc_models_rkshjqr ( );
		$model->saveall();
		
	}
	
	/*
	 * 自动完成数据取得
	 */
	public function autocompleteAction(){
		$searchkey = $this->_getParam('q');
        $rkdbh_model = new cc_models_rkshjqr ( );
	    $result = $rkdbh_model->getAutocompleteData($searchkey);
	    echo json_encode($result);
	}
}

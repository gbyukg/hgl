<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：    库间调拨入库维护(KJDBFKWH)
 * 作成者：    姚磊
 * 作成日：    2011/01/25
 * 更新履历：
 **********************************************************/
class cc_kjdbfkwhController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库维护初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-库间调拨返库维护" ); //标题
		$this->_view->display ( "kjdbfkwh_01.php" );
	}
	
	
	/*
	 * 库间调拨出库单选择页面
	 */
	public function showdbckdAction(){
		$this->_view->display ( "kjdbfkwh_02.php" );
	}
	
	
	/*
	 * 库间调拨入库维护列表xml数据取得
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['kjdbfkwh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kjdbfkwh_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['kjdbfkwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		
		
		
		$model = new cc_models_kjdbfkwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 退货单列表xml数据取得(退货单选择页面)
	 */
	public function getthddataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 10 ); 		     //默认显示数量
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); 	     //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); 	     //终止日期
		$filter ['dcck'] = $this->_getParam ( "dcck", '' ); 	         //调出仓库编号
		$filter ['drck'] = $this->_getParam ( "drck", '' );              //调入仓库编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cc_models_kjdbfkwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridthdData( $filter );
	}
	
	/*
	 * 库间调拨返库、入库弹出画面前，判断处理
	 * 
	 */
	public function checkAction()
	{
		$result = array (); //定义返回值
		$model = new cc_models_kjdbfkwh();
		$djbh = $this->_getParam("djbh","");
		$rec = $model->getkjdbckdcxOne($djbh);
		
		//库间调拨出库单出库状态，请确认
		if ($rec!=false) {
			$result ['endstatus'] = 9;
		}else{
			$result ['endstatus'] = 0;
		}
		echo Common_Tool::json_encode ( $result );
	}
}
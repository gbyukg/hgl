<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  分箱维护(fxwh)
 * 作成者：    姚磊
 * 作成日：    2011/03/31
 * 更新履历：
 **********************************************************/
class cc_fxwhController extends cc_controllers_baseController {

	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-分箱维护" ); //标题
		$this->_view->assign ( "userid", $_SESSION ['auth']->userId ); //登陆者
		$last = time() - (7 * 24 * 60 * 60);		
		$lastweek = date("Y-m-d",$last);
		$riqid =date("Y-m-d");
		$this->_view->assign ( "kprqc", $lastweek);  //开票日期从
		$this->_view->assign ( "kprqd", $riqid);  //开票日期到
		$this->_view->display ( "fxwh_01.php" );
	}

	/*
	 * 查询分箱维护信息 返回xml格式
	 */
	public function getdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['XSHDZHT'] = $this->_getParam ( "xshdzht" );			//出库状态
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		$filter['searchParams']["FXRQC"] =$this->_getParam ( "fxrqc" );
		$filter['searchParams']["FXRQD"] =$this->_getParam ( "fxrqd" );
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fxwh_searchParams'] = $_POST;
				unset($_SESSION['fxwh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['fxwh_filterParams'] = $_POST;
				unset($_SESSION['fxwh_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['fxwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['fxwh_searchParams'];  //固定查询条件
		$model = new cc_models_fxwh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	
	/**
	 * 选择 状态不是已分箱的拣货信息进行分箱处理
	 */
	
	public function checkAction(){
		
		$result = array (); //定义返回值
		$model = new cc_models_fxwh();
		$xshdbh = $this->_getParam("xshdbh","");
		$rec = $model->getfxzt($xshdbh);
		
		if ($rec!=false) {
			$result ['endstatus'] = $rec ['XSHDZHT'];
		}else{
			$result ['endstatus'] = 2;
		}
		echo Common_Tool::json_encode ( $result );
		
	}
	
}

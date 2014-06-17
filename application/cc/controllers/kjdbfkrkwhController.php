<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库间调拨返库入库维护(kjdbfkrkwh)
 * 作成者：苏迅
 * 作成日：2010/1/30
 * 更新履历：

 *********************************/
class cc_kjdbfkrkwhController extends cc_controllers_baseController {
	
	/*
     * 库间调拨返库入库维护页面初始化显示
     */
	public function indexAction() {
		
		$this->_view->display ( 'kjdbfkrkwh_01.php' );
	}
	
	/*
     * 调出仓库autocomplete
     */
	public function autocompleteAction(){
    	$filter ['searchkey'] = $this->_getParam('q'); //查找字符串  
        $cangku_model = new cc_models_kjdbfkrkwh ( );
	    $result = $cangku_model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	/*
	 * 得到入库单据列表数据
	 */
	public function getlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
/*		$filter ['dbfkrkdkey'] = $this->_getParam ( "dbfkrkdkey", '' ); //调拨返库入库单
		$filter ['dcckkey'] = $this->_getParam ( "dcckkey", '' ); //调出仓库
		$filter ['dydbfkdkey'] = $this->_getParam ( "dydbfkdkey", '' ); //对应调拨返库单
		$filter ['dydbchkdkey'] = $this->_getParam ( "dydbchkdkey", '' ); //对应调拨出库单
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); //终止日期*/
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_kjdbfkrkwh_searchParams'] = $_POST;
				unset($_SESSION['kjdbfkrkwh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kjdbfkrkwh_filterParams'] = $_POST;
				unset($_SESSION['cc_kjdbfkrkwh_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['kjdbfkrkwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_kjdbfkrkwh_searchParams'];  //固定查询条件
		
		$model = new cc_models_kjdbfkrkwh ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
     * 入库单据详情画面
     */
	public function detailAction() {
		
		$model = new cc_models_kjdbfkrkwh ( );
		$djbh = $this->_getParam ( "djbh" );//返库入库单编号
		$rec = $model->getRkdjxx ( $djbh);
		
		//画面项目赋值
		//查询画面传递过来的查询条件(带查询的上下条用)
/*		$this->_view->assign ( "ksrqkey", $this->_getParam ( "ksrqkey", '' ) ); //开始日期检索条件
		$this->_view->assign ( "zzrqkey", $this->_getParam ( "zzrqkey", '' ) ); //终止日期检索条件
		$this->_view->assign ( "dbfkrkdkey", $this->_getParam ( "dbfkrkdkey", '' ) ); //调拨返库入库单编号检索条件
		$this->_view->assign ( "dcckkey", $this->_getParam ( "dcckkey", '' ) ); //调出仓库检索条件
		$this->_view->assign ( "dydbfkdkey", $this->_getParam ( "dydbfkdkey", '' ) ); //对应调拨返库单检索条件
		$this->_view->assign ( "dydbchkdkey", $this->_getParam ( "dydbchkdkey", '' ) ); //对应调拨出库单检索条件
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //排序列
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //排序方式*/
		$this->_view->assign ( "title", "仓储管理-调拨返库入库详情");
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'kjdbfkrkwh_02.php' );
	}
	
	/*
     * 入库单据明细xml数据取得
     */
	public function getmingxixmldataAction() {
		$model = new cc_models_kjdbfkrkwh ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getMxXmlData ( $this->_getParam ( "djbh", '' ) );	
	}
	
	/*
	 * 取得入库单据信息上下条
	 */
	public function getrkdjxxAction() {
		$djbh = $this->_getParam ( "djbh" );
		
		//检索条件
/*		$filter ['dbfkrkdkey'] = $this->_getParam ( "dbfkrkdkey", '' ); //调拨返库入库单
		$filter ['dcckkey'] = $this->_getParam ( "dcckkey", '' ); //调出仓库
		$filter ['dydbfkdkey'] = $this->_getParam ( "dydbfkdkey", '' ); //对应调拨返库单
		$filter ['dydbchkdkey'] = $this->_getParam ( "dydbchkdkey", '' ); //对应调拨出库单
		$filter ['ksrqkey'] = $this->_getParam ( "ksrqkey", '' ); //开始日期
		$filter ['zzrqkey'] = $this->_getParam ( "zzrqkey", '' ); //终止日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式*/
		$filter['filterParams'] = $_SESSION['kjdbfkrkwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_kjdbfkrkwh_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', 'current' ); //检索方向
		
		$model = new cc_models_kjdbfkrkwh ( );
		$rec = $model->getRkdjxx ( $djbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "kjdbfkrkwh_02.php" ) );
		}
	}

}
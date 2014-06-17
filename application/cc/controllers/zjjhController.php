<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  整件拣货(zjjh)
 * 作成者：    姚磊
 * 作成日：    2011/03/22
 * 更新履历：
 **********************************************************/
class cc_zjjhController extends cc_controllers_baseController {
	/*
	 * 库间调拨入库维护初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-整件拣货" ); //标题
		$this->_view->assign ( "userid", $_SESSION ['auth']->userId ); //登陆者
		$this->_view->display ( "zjjh_01.php" );
	}

	/*
	 * 查询整件拣货信息 返回xml格式
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
				$_SESSION['zjjh_searchParams'] = $_POST;
				unset($_SESSION['zjjh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zjjh_filterParams'] = $_POST;
				unset($_SESSION['zjjh_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['zjjh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zjjh_searchParams'];  //固定查询条件
		$model = new cc_models_zjjh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	public function updateAction(){
		
		$filter ['dytm'] = $this->_getParam ( "dytm", '');       //对应条码
		$filter ['ZHUANGTAI'] = $_POST ['bgzht']; //状态
		$model = new cc_models_zjjh();
		$rec = $model->update ($filter );
		if($rec){
		if($filter ['ZHUANGTAI']==2){
			Common_Logger::logToDb ("整件拣货" ."出库中:  $filter ['dytm']");
		}else{
			Common_Logger::logToDb ("整件拣货" ."待发送:  $filter ['dytm']");
		}
		}
		
	}
}
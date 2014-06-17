<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  库位对应传送带出口(kwdycsdck)
 * 作成者：    姚磊
 * 作成日：    2011/06/22
 * 更新履历：
 **********************************************************/	
class cc_kwdycsdckController extends cc_controllers_baseController {

	public function indexAction(){
		$this->_view->assign ( "title", "仓储管理-库位对应传送带出口" ); //标题
		//$this->_view->assign ( "userid", $_SESSION ['auth']->userId ); //登陆者
		$this->_view->display ( "kwdycsdck_01.php" );
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
		$filter['searchParams']["CKXX"] =$this->_getParam ( "CKXX" );
		$filter['searchParams']["KWXX"] =$this->_getParam ( "KWXX" );
		$filter['SHFSHKW'] = $_POST['SHFSHKW'];   //获取是否点击点选框
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kwdycsdck_searchParams'] = $_POST;
				unset($_SESSION['kwdycsdck_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['kwdycsdck_filterParams'] = $_POST;
				unset($_SESSION['kwdycsdck_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['kwdycsdck_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['kwdycsdck_searchParams'];  //固定查询条件
		$model = new cc_models_kwdycsdck();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	

	
    /*
     * 传送带出口信息设定
     */
    public function alarmsetAction()
    {
    	$model = new cc_models_kwdycsdck();
    	$ckbh = $this->_getParam('ckbh');
    	$chsdchk = $this->_getParam('chsdchk');
        $this->_view->assign ( 'title', '仓储管理-传送带出口设定' );
        $this->_view->assign('ckbh', $ckbh);  
        $chsdchklist=$model->getcsdxx ($ckbh) ; 
        $this->_view->assign ( 'red', $model->getcsdkwxx ($ckbh,$chsdchk));//取出库位信息   
        $this->_view->assign ( 'res',$chsdchklist);//取出传送带出口列表
       
        $this->_view->display ( 'kwdycsdck_02.php' );
    }
    /*
     * 弹出窗口保存
     */
    function saveAction(){
    	
    	$model = new cc_models_kwdycsdck();
    	$model->upDatecsdxx ();
    }
    /*
     * 页面保存
     */
    
    function savegridAction(){
    	
    	$model = new cc_models_kwdycsdck();
    	$model->upDatecsdxxgrid ();
    }
    function autocompleteAction(){
    	$ckbh = $this->_getParam('flg');
   		$model = new cc_models_kwdycsdck();
		$result = $model->getcsdxl($ckbh);
		echo Common_Tool::json_encode($result);   	
    }
}

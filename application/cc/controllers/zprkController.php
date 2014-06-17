<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    赠品入库(ZPRK)
 * 作成者：苏迅
 * 作成日：2011/7/14
 * 更新履历：
 *********************************/
class cc_zprkController extends cc_controllers_baseController {
	
	/*
	 * 赠品入库页面
	 */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
		$this->_view->assign('bmbh', $_SESSION['auth']->bmbh); 	  //部门编号
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "title", "仓储管理-赠品入库" ); 	//标题
		$this->_view->display ( "zprk_01.php" );
	}
	
	/*
	 * 赠品选择
	 */
	public function zplistAction() {
		$this->_view->display ( "zprk_02.php" );
	}
	
	/*
	 * 赠品选择弹出画面数据取得
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
	
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_zengpin_searchParams'] = $_POST;
				unset($_SESSION['cc_zengpin_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_zengpin_filterParams'] = $_POST;
				unset($_SESSION['cc_zengpin_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_zengpin_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['cc_zengpin_searchParams'];  //固定查询条件
			
		$zp_model = new cc_models_zprk();
		header("Content-type:text/xml");
		echo $zp_model->getListData($filter);
	}
	
    /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值    	
        $zp_model = new cc_models_zprk();
	    $result = $zp_model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	 /*
	 * 取得赠品信息
	 */
    public function getzpinfoAction(){
	   	$filter ['zpbh'] = $this->_getParam('zpbh');   //检索项目值
 		$zp_model = new cc_models_zprk();		
	    echo json_encode($zp_model->getZpInfo($filter));
	}
	
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$model = new cc_models_zprk ( );		
	    echo Common_Tool::json_encode($model->getDanweiInfo($filter));
	}
	
	/*
	 * 赠品入库信息保存
	 */
	public function saveAction() {
		
		//返回值状态('0'：正常)	
		$result['status'] = '0';
		try {
			$model = new cc_models_zprk ();
			//必须输入项验证
			if(!$model->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$model->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $model->beginTransaction ();
			    //赠品入库单编号取得
			    $zprkdbh = Common_Tool::getDanhao('ZPR',$_POST['KPRQ']);
			    //赠品入库单信息保存
			    $model->saveRukudan($zprkdbh);
			    //循环读取明细信息,赠品入库更新操作
				$model->executeMingxi($zprkdbh);				
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $zprkdbh;//新生成的采购入库单编号
				    $model->commit();
				    Common_Logger::logToDb("新赠品入库单号：".$zprkdbh);
			    }else{
				    $model->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
	
	}
	
	
}
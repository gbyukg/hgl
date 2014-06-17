<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   随时报损报溢(ssbsby)
 * 作成者：李洪波
 * 作成日：2011/01/26
 * 更新履历：
 *********************************/
class cc_ssbsbyController extends cc_controllers_baseController {
	
	/*
	 * 库位/批号选择弹出画面
	 */
	public function listAction(){
		$this->_view->assign("SHPBH",$this->_getParam("shpbh",'')); //商品编号
		$this->_view->assign("CKBH_H",$this->_getParam("ckbh",''));   //仓库编号
		$this->_view->assign("KQBH_H",$this->_getParam("kqbh",'')); //仓库编号		
		$this->_view->assign("ckmch",$this->_getParam("ckmch",''));   //仓库名称
		$this->_view->assign("kqmch",$this->_getParam("kqmch",'')); //仓库名称
		
		$this->_view->assign("title","库位/批号选择");
		$this->_view->display ( "ssbsby_02.php" );
	}
	
	/*
	 * 库位/批号列表画面数据取得
	 */
	public function getlistdataAction(){
		$filter ['shpbh'] = $this->_getParam("shpbh",'');
		$filter ['ckbh'] = $this->_getParam("ckbh",'');
		$filter ['kqbh'] = $this->_getParam("kqbh",'');
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
        $filter ['orderby'] = $this->_getParam ( "orderby",1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];	
	
        //一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_searchParams'] = $_POST;
				unset($_SESSION['cc_ssbsby_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_ssbsby_filterParams'] = $_POST;
				unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_ssbsby_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件		
		$kqbh_model = new cc_models_ssbsby();		
		header("Content-type:text/xml");
		echo $kqbh_model->getListData($filter);
	}

     /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$filter ['shpbh'] = $this->_getParam("shpbh",'');
		$filter ['ckbh'] = $this->_getParam("ckbh",'');
		$filter ['kqbh'] = $this->_getParam("kqbh",'');
		$kqbh_model = new cc_models_ssbsby();
	    $result = $kqbh_model->getAutocompleteData($filter);
	    echo json_encode($result);
	}
	
	/*
	 *  随时报损报溢初始页面
	 */
	public function indexAction()
	{
		$this->_view->assign ( "KPRQ", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "title", " 仓储管理-随时报损报溢" ); 
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->display ( "ssbsby_01.php" );
	}	
	
  	/**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$model = new cc_models_ssbsby ( );		
	    echo json_encode($model->getShangpinInfo($filter));
	}
	
  	/**
     * 取商品成本单价
     *
     */
	public function getchbdjAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh'); 
    	$filter ['pihao'] = $this->_getParam('pihao'); 
		$model = new cc_models_ssbsby ( );		
	    echo json_encode($model->getChbdjInfo($filter));
	}
	
	
	/*
	 * 保存实盘信息
	 */
	public function accountAction() {
	
		$riqi=date("Y-m-d");
		//单据编号取得
		$djbh = Common_Tool::getDanhao('SSY',$riqi);;
	
		try {
			$model = new cc_models_ssbsby();
			//开始一个事务
			$model->beginTransaction ();

			//实盘信息保存
			$result=$model->accountSsbsby($djbh);

			//保存成功
			if($result['status'] == '0'){			  	
			    $model->commit ();
			 }else{
			    $model->rollBack ();//有错误发生
			}

			echo json_encode($result);
		}
		 catch ( Exception $e )
		 {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
	
	}
}

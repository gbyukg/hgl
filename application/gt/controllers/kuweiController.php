<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库位选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_kuweiController extends gt_controllers_baseController {
	/*
	 * 库位选择弹出画面
	 * flg:  0 ->可用及冻结 1->全部
	 * ckbh:仓库编号 ckmch:仓库名称
	 * kqbh:库区编号 kqmch:库区名称
	 */
	public function listAction(){
		$this->_view->assign("title","库位选择");
		$this->_view->display ( "kuwei_01.php" );
	}
	
	/*
	 * 库位选择弹出画面
	 * kwlx:  库位类型 0 整件 1散货 2 全部
	 */
	public function listallAction(){
		$kuwei_model = new gt_models_kuwei();
		$this->_view->assign( "cklist",$kuwei_model->getCk() );//仓库
		$this->_view->assign( "kqlx",$kuwei_model->getKqlx() );//库区类型
		$this->_view->assign( "kwlx",$this->_getParam('kwlx','2') );//库位类型
    	$this->_view->display ( "kuwei_03.php" );
	}
	/*
	 * 取得库区
	 * ckbh:仓库编号 kqlx:库区类型
	 */
	public function getkqAction(){
		$kuwei_model = new gt_models_kuwei();
		$filter['ckbh'] = $this->_getParam('ckbh');
		$filter['kqlx'] = $this->_getParam('kqlx');
		echo json_encode($kuwei_model->getKq($filter));
	}
	
	/*
	 * 库区选择弹出画面数据取得
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		//查询相关参数
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['ckbh'] = $this->_getParam("ckbh","");
		$filter ['kqbh'] = $this->_getParam("kqbh","");

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_kuwei1_searchParams'] = $_POST;
				unset($_SESSION['gt_kuwei1_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_kuwei1_filterParams'] = $_POST;
				unset($_SESSION['gt_kuwei1_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_kuwei1_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_kuwei1_searchParams'];  //固定查询条件
			
		$kuwei_model = new gt_models_kuwei();
		header("Content-type:text/xml");
		echo $kuwei_model->getListData($filter);
	}
	
	/*
	 * 所有库区选择弹出画面数据取得
	 * flg:  0 ->可用及冻结 1->全部
	 */
	public function getlistalldataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//业务相关参数
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['ckbh'] = $this->_getParam("ckbh","");  //仓库编号
		$filter ['kqbh'] = $this->_getParam("kqbh","");  //库区编号
		$filter ['kqlx'] = $this->_getParam("kqlx","");  //库区类型
		$filter ['kwlx'] = $this->_getParam("kwlx","");  //库位类型（是否散货）
			
		$kuwei_model = new gt_models_kuwei();
		header("Content-type:text/xml");
		echo $kuwei_model->getListAllData($filter);
	}
	
	/*
	 * 库位选择树形列表
	 * flg:0 可用  1:可用冻结  2:全部
	 * shfshkw: 0:包装 1:散货 2:全部 
	 */
	public function treeAction(){
		$this->_view->assign('flg',$this->_getParam('flg','0'));
		$this->_view->assign('shfshkw',$this->_getParam('shfshkw','2'));  //默认全部
		$this->_view->display ( "kuwei_02.php" );
		
	}
	
	/*
	 * 库位选择树形列表数据取得
	 * flg:0可用  1:可用冻结  2:全部
	 */
	public function gettreedataAction(){
		//业务相关参数
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['shfshkw'] = $this->_getParam("shfshkw","2");
			
		$kuwei_model = new gt_models_kuwei();
		header("Content-type:text/xml");
		echo $kuwei_model->getTreeData($filter);
	}
}
<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    商品选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_shangpinController extends gt_controllers_baseController {
	
	/*
	 * 商品选择弹出画面
	 * flg:0 销售  1：采购 2:全部
	 * status:0: 可用  1:全部
	 * dwbh:单位编号
	 */
	public function listAction(){
		$this->_view->assign('title',"共通-商品选择");           //用途
		$this->_view->assign('flg',$this->_getParam('flg','0'));           //用途
		$this->_view->assign('status',$this->_getParam('status','0'));     //选择对象
		$this->_view->assign('dwbh',$this->_getParam('dwbh','00000000'));     //单位编号
		$this->_view->display ( "shangpin_01.php" );
	}
	
	/*
	 * 商品选择弹出画面数据取得
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		//查询相关参数		
		$filter ['flg'] = $this->_getParam("flg","0");                 //用途
		$filter ['status'] = $this->_getParam("status",'0');           //选择对象
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000');        //单位编号
		$filter ['flbm'] = $this->_getParam("flbm",'');                //分类编码
	
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_searchParams'] = $_POST;
				unset($_SESSION['gt_shangpin_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_filterParams'] = $_POST;
				unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
				unset($filter ['flbm']);
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_shangpin_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
			
		$model = new gt_models_shangpin();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}
	
	 /*
	 * 取得商品信息
	 * flg:0 销售  1：采购 2:其他
	 * status:0: 可用  1:全部
	 * dwbh:单位编号
	 */
    public function getshangpininfoAction(){
       	$filter ['flg'] = $this->_getParam("flg"); //用途
   		$filter ['status'] = $this->_getParam("status",'0');           //选择对象
	   	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$model = new gt_models_shangpin();
		
	    echo json_encode($model->getShangpinInfo($filter));
	}

    /*
	 * 自动完成
	 * flg:0 销售  1：采购 2:其他
	 * status:0: 可用  1:全部
	 * dwbh:单位编号
	 */
	public function autocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值
    	$filter ['flg'] = $this->_getParam("flg"); //用途
   		$filter ['status'] = $this->_getParam("status",'0');           //选择对象
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000');           //单位编号
    	
        $shangpin_model = new gt_models_shangpin ( );
	    $result = $shangpin_model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
}
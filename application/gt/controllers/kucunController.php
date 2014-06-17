<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库存选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_kucunController extends gt_controllers_baseController {
	/*
	 * 库存选择弹出画面
	 * flg: 0 包装 1散货 2全部
	 */
	public function listAction(){
		$this->_view->assign('flg',$this->_getParam("flg","2"));
		$this->_view->assign('shpbh',$this->_getParam("shpbh"));//商品编号
		//20110302 追加
		$this->_view->assign('ckbh',$this->_getParam("ckbh"));//商品编号
		$this->_view->display ( "kucun_01.php" );	
	}
	
	
	/*
	 * 库存选择弹出画面(销售开票专用)
	 * 
	 */
	public function listforxsAction(){
		$this->_view->assign('title',"销售批号选择");//商品编号
		$this->_view->assign('shpbh',$this->_getParam("shpbh"));//商品编号
		$this->_view->display ( "kucun_02.php" );	
	}
	
	/*
	 * 库存选择弹出画面数据取得
	 * flg: 0 包装 1散货 2全部
	 * ckbh:仓库编号
	 */
	public function getlistdataAction()	{
		//业务相关参数
		$filter ['shpbh'] = $this->_getParam("shpbh"); //商品编号
		$filter ['flg'] = $this->_getParam("flg");
		//20110302 追加
		$filter ['ckbh'] = $this->_getParam("ckbh");//仓库编号
		
		$kucun_model = new gt_models_kucun();
		header("Content-type:text/xml");
		echo $kucun_model->getListData($filter);		
	}
	
	/*
	 * 库存选择弹出画面数据取得
	 */
	public function getlistdataforxsAction()	{
		//业务相关参数
		$shpbh = $this->_getParam("shpbh"); //商品编号
		$kucun_model = new gt_models_kucun();
		header("Content-type:text/xml");
		echo $kucun_model->getListDataForXs($shpbh);		
	}
	
	
	/*
	 * 得到最新库存数据
	 */
	public function getkucundataAction(){
   		$filter['shpbh'] = $this->_getParam("shpbh","");    //商品编号
		$filter['shfshkw'] = $this->_getParam("shfshkw","");//是否散货库位
		$filter['ckbh'] = $this->_getParam("ckbh","");//是否散货库位
			
		$kucun_model = new gt_models_kucun();
		echo json_encode($kucun_model->getKucunData($filter));
	}
}
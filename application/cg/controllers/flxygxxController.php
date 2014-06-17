<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购返利协议(供应商)(flxygxx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/06/02
 ***** 更新履历：

 ******************************************************************/

class cg_flxygxxController extends cg_controllers_baseController {
     /*
      * 退货区列表画面显示
      */
	public function indexAction(){
		$this->_view->assign("kprq",date("Y-m-d"));//开票日期
		$model = new cg_models_flxygxx();
		$this->_view->assign('action','new');
		$rec = $model->getYgxx();
		$this->_view->assign('title','采购管理-采购返利协议(供应商)');
		$this->_view->assign("rec",$rec);
		$this->_view->display('flxyg_01.php');
	}
	
	/*
	 * 保存信息
	 */
	public function saveAction(){
		$result = array();//定义返回值
	     $xybh = Common_Tool::getDanhao('CFX',$_POST['KPRQ']);
	     $result ['xybh'] = $xybh; 
		 try{
			$model = new cg_models_flxygxx();
			$model->beginTransaction();
			
			if ($_POST ['action'] == 'new') {
			//采购返利协议编号取得
			//插入新数据	
		    $model->insertFlxygxx($xybh);
		    Common_Logger::logToDb ("采购返利协议  协议编号：".$xybh);
		    $result ['status'] = '0';
			}else {
			//更新数据
			if ($model->updateFlxygxx() == false) {

				$result ['status'] = '2'; //时间戳已变化
			} else {
				$result ['status'] = '1'; //修改成功
				Common_Logger::logToDb ( "采购返利协议 信息修改    协议编号：".$xybh );
			}
		
		}
		    $model->commit();
		    //返回处理结果
		    echo json_encode ( $result );
			 
		 }catch (Exception $ex){
			$model->rollBack();
			throw $ex;
		}
	}
	
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$Model = new cg_models_flxygxx();
		
	    echo Common_Tool::json_encode($Model->getDanweiInfo($filter));
	}
}
?>
<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   盘点结束(pdjs)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_pdjsController extends cc_controllers_baseController {

	
	/**
	 * 盘点结束画面显示
	 *
	 */
	public function deleteAction() {
		$djbh = $this->_getParam("djbh"); //盘点单据号
		
		$model = new cc_models_pdjs();
		
		$rec = $model->getPdjsOne($djbh);
		
		//不可更改
		$this->_view->assign ( 'disabled', 'disabled' );
		$this->_view->assign ( 'disabledbm', '');
		$this->_view->assign ( 'disableduser', '');
		$this->_view->assign ( 'action', 'detail' );  								
		$this->_view->assign ( 'title', '基础管理-盘点结束' );
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
		$this->_view->assign ( 'rec', $rec );
		
		//账面数量条件设定
		switch($rec[ZHMSHLTJ]){

			case 1 :
				$this->_view->assign ( 'check1', 'checked');	
				break;
			case 2 :
				$this->_view->assign ( 'check2', 'checked');
				break;
			case 3 :
				$this->_view->assign ( 'check3', 'checked');
				break;	
			default:
				$this->_view->assign ( 'check1', 'checked');
				break;	
		}
		
		//冻结标志设定
		if ($rec[DJBZH]==1){
			$this->_view->assign ( 'check4', 'checked');	
		} 
		$this->_view->display ( 'pdjs_01.php' );
		
	}
	/*
	 * 盘点结束保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		$model = new cc_models_pdjs();
		
		if (!$model->inputCheck()) {
			$result ['status'] = 3; //必须输入判断
			echo Common_Tool::json_encode ( $result );
			return false;
		}
		//保存盘点信息到tbl:盘点信息
		$res = $model->updatePdjs($_POST['BUMEN_H'],$_POST['YEWUYUAN_H'],$_POST['DJBH_H'],$_POST['BGZH_H'],$_POST['BGRQ_H']);
		
		if ($res==true){
			$result ['status'] = 0; //登录成功
			
			$result ['pdjs'] = $_POST['DJBH'];
			
			Common_Logger::logToDb( "【盘点结束 盘点单据号：".$_POST['DJBH_H']."】");
		}else{
			
			$result ['status'] = 2; //该盘点信息发生了判断
			echo Common_Tool::json_encode ( $result );
			return false;
		}
	
		//返回处理结果
		echo Common_Tool::json_encode ( $result );
		
		return true;
	}
	
  
	
}
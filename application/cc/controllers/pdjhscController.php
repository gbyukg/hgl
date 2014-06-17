<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   盘点计划生成(kwxx)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_pdjhscController extends cc_controllers_baseController {

	/*
     * 盘点计划生成画面显示
     */
	public function indexAction() {
		
		$this->_view->assign ( 'action', 'new' );  								
		$this->_view->assign ( 'title', '基础管理-盘点计划生成' );
	    $this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
		$this->_view->display ( 'pdjhsc_01.php' );
				
	}
	/*
	 * 盘点计划生成保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		//仓库编号
		$chkckbh = $_POST['CKBH_H'];
		//库区编号
		$chkkqbh = $_POST['KQBH_H'];
		//库位编号
		$chkkwbh = $_POST['KWBH_H'];
		
		$model = new cc_models_pdjhsc ( );
		
		//必须输入项
		if($chkckbh=="" ||$chkkqbh=="" || $chkkwbh="" ||
			$_POST['YJJSHRQ']== "" || $_POST['YJJSHRQT']== "" || $_POST['YJJSHRQ']== "" || $_POST['YJJSHRQT']== "" ||  
			$_POST['YEWUYUAN_H']== "" || $_POST['BMBH']== ""){
			
			$result ['status'] = 5; 
			echo Common_Tool::json_encode ( $result );
			return false;
		}
		//3保存盘点计划信息到tbl:盘点计划信息
		//员工登录
		if ($_POST ['action'] == 'new') {
			
			//2以画面项目仓库，库区，库位为条件，抽取仓库，库区，库位表。判断指定条件的仓库，库区，库位是否存在。
			
			if($model->getCkstatus( $chkckbh )==false){
				
				$result ['status'] = 2; //仓库编号已不存在
				echo Common_Tool::json_encode ( $result );
				return false;
			}

			if($model->getKcstatus($chkckbh,$chkkqbh) ==false){
			   
				$result ['status'] = 3; //库区编号已不存在
				echo Common_Tool::json_encode ( $result );
				return false;
			}
			
			if ($chkkwbh!=''){
			
				if($model->getKwstatus($chkckbh,$chkkqbh,$chkkwbh)==false){
					$result ['status'] = 4; //库位编号已不存在
					echo Common_Tool::json_encode ( $result );
					return false;
				}
			}
			try {
				//开始一个事务
				$model->beginTransaction ();
				
				$riqi = date("Y-m-d");
				$pdjbh = Common_Tool::getDanhao(PDJ,$riqi);
				//插入新数据
				if ($model->insertpdjhsc($pdjbh)== false) {
					$result ['status'] = 1; //盘点计划信息已存在
					echo Common_Tool::json_encode ( $result );
					return false;
				} else {
					
					$result ['status'] = 0; //登录成功
					$result ['pdjhsc'] = $pdjbh;
					Common_Logger::logToDb( "盘点计划编号：".$pdjbh);
				}
				$model->commit();
			} catch ( Exception $e ) {
			//回滚
				$model->rollBack ();
     			throw $e;
			}
		
		} 
		
		//返回处理结果
		echo Common_Tool::json_encode ( $result );
		return true;
	}
	
}